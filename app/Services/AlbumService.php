<?php

namespace App\Services;

use App\Models\Album;
use App\Models\Artist;
use App\Models\Song;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Storage;

class AlbumService
{

    /**
     * @function get the distinct discs on an album
     * @param Album $album
     * @return Collection
     */
    public function getDiscsByAlbum(Album $album): Collection
    {
        return Song::where('album_id', $album->id)
            ->select('disc')
            ->distinct()
            ->get();
    }

    /**
     * @function get the file path to Folder.jpg file.
     * @param Album $album
     * @return string
     */
    private function getAlbumCoverPath(Album $album): string
    {
        $path = explode('/', $album->songs->first()->path);
        array_pop($path); // remove last entry; filename of the song.
        $path[] = config('collection.coverFile.name');
        return config('collection.server.music.path').implode('/', $path);
    }

    /**
     * @function json format an album
     * @param Album $album
     * @param bool $addSongs
     * @param bool $addDownload
     * @return array
     */
    public function formatAlbum(Album $album, bool $addSongs = false, bool $addDownload = false): array
    {
        $u = new UrlSafeService;
        $genre = $album->songs->first()->genre;
        $artist = $album->artist;
        $arr = [
            'id' => $album->id,
            'name' => $album->name,
            'encodedName' => $u->encode($album->name),
            'year' => $album->year,
            'artist' => [
                'id' => $artist->id,
                'name' => $artist->name,
                'encodedName' => $u->encode($artist->name)
            ],
            'genre' => [
                'id' => $genre->id,
                'name' => $genre->name,
                'encodedName' => $u->encode($genre->name)
            ],
            'numSongs' => $album->numSongs,
            'duration' => $album->duration,
            'fileSize' => $album->fileSize,
            'discs' => $album->discs
        ];
        if ($album->coverPath) {
            $arr['coverPath'] = $album->coverPath;
        }
        if($addSongs) {
            $s = new SongService;
            $arr['songs'] = $album->songs->map(function ($song) use ($s) {
                return $s->formatSong($song);
            });
        }
        if(
            $addDownload &&
            $album->fileSize <= config('collection.server.download_album_threshold')
        ) {
            $arr['downloadLink'] = "/api/music/albums/".$album->id."/download";
        }
        if ($album->thumbnail) {
            $arr['thumbnail'] = $album->thumbnail;
        }
        return $arr;
    }

    /**
     * @function get album stats
     * @param string $sortedBy
     * @return array
     */
    public function getAllAlbums(string $sortedBy = ""): array
    {
        $albums = Album::with('songs')
            ->with('artist')
            ->get()
            ->map(function (Album $album) {
                $album->numSongs = $album->songs->count();
                $album->duration = $album->songs->sum('duration');
                $album->fileSize = $album->songs->sum('size');
                $album->discs = $album->songs->unique('disc')->count();
                return $this->formatAlbum($album);
            });
        if (strlen($sortedBy) > 0) {
            $albums = $albums->sortByDesc($sortedBy);
        }
        return $albums->toArray();
    }

    /**
     * @function get random albums
     * @return array
     */
    public function getRandomAlbums(): array
    {
        return Album::with('songs')
            ->with('artist')
            ->inRandomOrder()
            ->limit(config('collection.stats.albums.random'))
            ->get()
            ->map(function (Album $album) {
                $album->numSongs = $album->songs->count();
                $album->duration = $album->songs->sum('duration');
                $album->fileSize = $album->songs->sum('size');
                $album->discs = $album->songs->unique('disc')->count();
                $album->thumbnail = $this->getCoverThumb($album);
                return $this->formatAlbum($album);
            })->toArray();
    }


    /**
     * @function get album by id ^.^
     * @param string $albumId
     * @return array
     */
    public function getAlbumById(string $albumId): array
    {
        $album = Album::with('songs')
            ->with('artist')
            ->findOr($albumId, function () {
                return null;
            });
        if ($album) {
            $album->numSongs = $album->songs->count();
            $album->duration = $album->songs->sum('duration');
            $album->fileSize = $album->songs->sum('size');
            $album->discs = $album->songs->unique('disc')->count();
            $album->coverPath = $this->getCover($album);
            return $this->formatAlbum($album, true, true);
        } else {
            return [];
        }
    }

    /**
     * @function copy Folder.jpg to storage, get filename for cover
     * @param Album $album
     * @return string
     * @throws \Exception
     */
    private function getCover(Album $album): string
    {
        $s = new CoverService();
        $song = $album->songs->first();
        return $s->getCover($song->id, $song->path, 'music', $song->cover);
    }

    /**
     * @function get thumbnail for album cover
     * @param Album $album
     * @return string
     * @throws \Exception
     */
    private function getCoverThumb(Album $album): string
    {
        $s = new CoverService();
        $song = $album->songs->first();
        return $s->getCover($song->id, $song->path, 'music', $song->cover, true);
    }

    /**
     * @function clean up downloads
     * @return void
     */
    public function cleanDownloadStorage(): void
    {
        Log::channel('api')->info("Pruning download disk prior to preparing album download.");

        // delete directories
        $directories = Storage::disk('downloads')->directories();
        foreach ($directories as $directory) {
            if (Storage::disk('downloads')->deleteDirectory($directory)) {
                Log::channel('api')->debug("Deleted directory '".$directory."'.");
            };
        }
        // delete .zip files
        $allFiles = Storage::disk('downloads')->files();
        foreach ($allFiles as $file) {
            if (str_ends_with($file, ".zip")) {
                Storage::disk('downloads')->delete($file);
                Log::channel('api')->debug("Deleted file '".$file."'.");
            }
        }
    }

    /**
     * @function prepare zip download, and return the filename
     * @param Album $album
     * @return string
     */
    public function downloadAlbum(Album $album): string
    {
        $start = now();
        Log::channel('api')->info("Preparing download for album '".$album->name."' by '".$album->artist->name."'");
        $this->cleanDownloadStorage();
        $zipFileName = "$album->id.zip";
        $albumDirectoryName = $album->id;
        $albumDirectoryServerPath = storage_path('app/downloads')."/".$albumDirectoryName;

        // check if directory already exists. if not, create it.
        if (Storage::disk('downloads')->missing($albumDirectoryName)) {
            Storage::disk('downloads')->makeDirectory($albumDirectoryName);
            Log::channel('api')->debug("Directory '$albumDirectoryName' created.");
        }

        // copy each song of the album to the $albumDirectory
        $album->songs->each(function (Song $song) use ($album, $albumDirectoryName) {
            $songFilePath = config('collection.server.music.path').$song->path;
            $fileInfo = new \SplFileInfo($songFilePath);
            $extension = $fileInfo->getExtension();
            $storageFileName = "$song->track - $song->name.$extension";
            $storageFilePath = "$albumDirectoryName/$storageFileName";
            Storage::disk('downloads')->put($storageFilePath, file_get_contents($songFilePath));
            Log::channel('api')->debug("File '$storageFileName' copied to '$albumDirectoryName'.");
        });

        // copy cover file
        $coverFileServerPath = $this->getAlbumCoverPath($album);
        $coverFileStoragePath = $albumDirectoryName."/".config('collection.coverFile.name');
        Storage::disk('downloads')->put($coverFileStoragePath, file_get_contents($coverFileServerPath));
        Log::channel('api')->debug("File '".config('collection.coverFile.name')."' copied to '$albumDirectoryName'.");

        // zip directory
        // ext-zip does not (yet?) work on php8.4, so we use native debian zip command
        if (Storage::disk('downloads')->missing($zipFileName)) {
            $result = Process::run("zip --junk-paths ".storage_path('app/downloads')."/$zipFileName $albumDirectoryServerPath/*");
            if (strlen($result->output()) > 0) {
                Log::channel('api')->debug("ZIP output:\n".$result->output());
            }
        }

        $ms = $start->diffInMilliseconds(now());
        $fd = new FormatService;
        Log::channel('api')->info("finished preparing download file in ".$fd->formatMs($ms).".");

        return $zipFileName;

    }

    /**
     * @function search database for album
     * @param string $name
     * @return array
     */
    public function searchAlbumByName(string $name): array
    {
        $json = [];
        $l = new LibraryService;
        $f = new FormatService;
        Album::whereAny(['name', 'year'], 'like', "%$name%")
            ->with('songs')
            ->take(config('collection.search_max.albums'))
            ->get()
            ->map(function ($album) {
                $album->duration = $album->songs->sum('duration');
                return $album;
            })->sortByDesc('duration')
            ->each(function ($album) use (&$json, $l, $f) {
                $json[] = $l->formatSearchItem(
                    'album',
                    'album',
                    $album->id,
                    $album->name,
                    $f->formatDuration($album->duration),
                    "time"
                );
            })->toArray();
        return array_values($json);
    }

}
