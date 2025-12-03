<?php

namespace App\Services;
use App\Models\Song;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class SongService
{

    /**
     * @function format song in json format with all needed infos
     * @param Song $song
     * @param bool $getCover
     * @param bool $getMp3
     * @param bool $getThumbnail
     * @return array
     * @throws \Exception
     */
    public function formatSong (Song $song, bool $getCover = false, bool $getMp3 = false, bool $getThumbnail = false): array
    {
        $u = new UrlSafeService();
        $a = new AlbumService();
        $c = new CoverService();
        $json = [
            'name' => $song->name,
            'track' => $song->track,
            'disc' => $song->disc,
            'publisher' => $song->publisher,
            'composer' => $song->composer,
            'codec' => $song->codec,
            'channel' => $song->channel,
            'size' => $song->size,
            'duration' => $song->duration,
            'sampleRate' => $song->sample_rate,
            'bitRate' => $song->bit_rate,
            'vbr' => $song->vbr,
            'inlineCover' => $song->cover,
            'artist' => [
                'id' => $song->artist->id,
                'name' => $song->artist->name,
                'encodedName' => $u->encode($song->artist->name)
            ],
            'album' => [
                'id' => $song->album->id,
                'name' => $song->album->name,
                'encodedName' => $u->encode($song->album->name),
                'year' => $song->album->year,
                'tracks' => $song->album->songs->count(),
                'discTracks' => $song->album->songs->filter(function ($albumSong) use ($song) {
                    return $albumSong->disc == $song->disc;
                })->count(),
                'discs' => count($a->getDiscsByAlbum($song->album))
            ],
            'genre' => [
                'id' => $song->genre->id,
                'name' => $song->genre->name,
                'encodedName' => $u->encode($song->genre->name)
            ],
            'encodedPath' => $u->encode($song->path),
            'path' => $song->path,
            'modifiedAt' => Carbon::parse("$song->modified_at Europe/Berlin")
                ->format('Y-m-d\TH:i:sP')
        ];
        // only copy files if we explicitly should do so (mostly song page)
        if ($getCover) {
            $json['cover'] = $c->getCover($song->id, $song->path, 'music', $song->cover, false);;
        }
        if ($getThumbnail) {
            $json['thumbnail'] = $c->getCover($song->id, $song->path, 'music', $song->cover, true);
        }
        if ($getMp3) {
            $json['mp3Path'] = $this->getSongPath($song);
        }

        // add info for albumArtist relation if it exists
        if ($song->albumArtist) {
            $json['albumArtist'] = [
                'id' => $song->albumArtist->id,
                'name' => $song->albumArtist->name
            ];
        }

        return $json;

    }


    /**
     * @function get next and prev links for a song.
     * @param Song $song
     * @return array
     */
    public function getNavigation (Song $song): array
    {
        $a = new AlbumService;
        $u = new UrlSafeService;
        $currentTrack = $song->track;
        $currentDisc = $song->disc;
        $numDiscs = count($a->getDiscsByAlbum($song->album));
        $albumSongs = $song->album->songs; // all songs from the album
        $discSongs = $albumSongs->filter(function ($song) use ($currentDisc) {
            return $song->disc == $currentDisc;
        });
        $numTracks = count($albumSongs);
        $nextTrackNumber = $song->track;
        $nextDiscNumber = $song->disc;
        $hasNext = false;
        $prevTrackNumber = $song->track;
        $prevDiscNumber = $song->disc;
        $hasPrev = false;
        $nav = [];
        // one disc: easy.
        if ($numDiscs === 1) {
            // more than one track on disc, and not the last.
            // last track or only one track: no next.
            if ($currentTrack < $numTracks && $numTracks > 1) {
                $nextTrackNumber += 1;
                $hasNext = true;
            }
            // only for tracks 2+. track 1: no prev.
            if ($currentTrack > 1) {
                $prevTrackNumber -= 1;
                $hasPrev = true;
            }
        }
        // more than one disc: shits getting complicated.
        else if ($numDiscs > 1) {
            // not the last track on current disc
            if ($song->track < count($discSongs)) {
                $nextTrackNumber += 1;
                $hasNext = true;
            }
            // last track, but not the last disc
            else if ($song->track == count($discSongs) && $currentDisc < $numDiscs) {
                $nextTrackNumber = 1;
                $nextDiscNumber += 1;
                $hasNext = true;
            }
            // not the first track, ez
            if ($currentTrack != 1) {
                $prevTrackNumber -= 1;
                $hasPrev = true;
            }
            // first track on discs 2+
            else if ($currentDisc != 1) {
                $prevDiscNumber -= 1;
                $prevTrackNumber = $albumSongs->filter(function ($song) use ($prevDiscNumber) {
                    return $song->disc == $prevDiscNumber;
                })->max('track');
                $hasPrev = true;
            }
        }

        // prepare json for next and prev track
        if ($hasNext) {
            $nextTrack = $albumSongs->filter(function ($song) use ($nextTrackNumber, $nextDiscNumber) {
                return $song->track == $nextTrackNumber && $song->disc == $nextDiscNumber;
            })->first();
            if ($nextTrack) {
                $nav['next'] = [
                    'encodedPath' => $u->encode($nextTrack->path),
                    'track' => $nextTrack->track,
                    'disc' => $nextTrack->disc,
                    'name' => $nextTrack->name,
                ];
            }
        }
        if ($hasPrev) {
            $prevTrack = $albumSongs->filter(function ($song) use ($prevTrackNumber, $prevDiscNumber) {
                return $song->track == $prevTrackNumber && $song->disc == $prevDiscNumber;
            })->first();
            if ($prevTrack) {
                $nav['prev'] = [
                    'encodedPath' => $u->encode($prevTrack->path),
                    'track' => $prevTrack->track,
                    'disc' => $prevTrack->disc,
                    'name' => $prevTrack->name,
                ];
            }
        }

        return $nav;
    }


    /**
     * @function copy mp3 file to storage if needed, get filename
     * @param Song $song
     * @return string
     */
    private function getSongPath(Song $song): string
    {
        // create new filename
        $serverPath = config('collection.server.music.path')."/".$song->path;
        $fileInfo = new \SplFileInfo($serverPath);
        $extension = $fileInfo->getExtension();
        $storageFileName = $song->id.".".$extension;

        // copy file to public disc if it doesn't yet exist.
        if (Storage::missing($storageFileName)) {
            Storage::disk('public')
                ->put($storageFileName, file_get_contents($serverPath));
        }

        return $storageFileName;
    }


    /**
     * @function return all songs with all relations.
     * @return array
     */
    public function getAllSongs(): array
    {
        return Song::with('artist')
            ->with('album')
            ->with('genre')
            ->with('albumArtist')
            ->get()
            ->map(function ($song) {
                return $this->formatSong($song);
            })->toArray();
    }

    /**
     * @function get Song stats
     * @return array[]
     */
    public function getRandomSongs(): array
    {
        return Song::with('artist')
            ->with('album')
            ->with('genre')
            ->with('albumArtist')
            ->inRandomOrder()
            ->limit(config('collection.stats.songs.random'))
            ->get()
            ->map(function (Song $song) {
                return $this->formatSong($song, false, false, true);
            })->toArray();
    }

    /**
     * @function search database for album
     * @param string $name
     * @return array
     */
    public function searchSongByName(string $name): array
    {
        $json = [];
        $l = new LibraryService;
        $f = new FormatService;
        $u = new UrlSafeService;
        Song::whereLike('name', "%$name%", caseSensitive: false)
            ->with('artist')
            ->take(config('collection.search_max.artists'))
            ->get()
            ->sortBy('path')
            ->each(function ($song) use (&$json, $l, $f, $u) {
                $json[] = $l->formatSearchItem(
                    'song',
                    'music',
                    $u->encode($song->path),
                    $song->artist->name." - ".$song->name,
                    $f->formatDuration($song->duration),
                    "time"
                );
            })->toArray();
        return array_values($json);
    }

}
