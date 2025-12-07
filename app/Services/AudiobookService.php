<?php

namespace App\Services;

use App\Models\Audiobook;
use App\Models\Author;
use App\Models\Narrator;
use App\Models\Track;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class AudiobookService
{

    /**
     * @function format json for audiobook
     * @param Audiobook $audiobook
     * @param bool $addCover
     * @param bool $addTracks
     * @param bool $thumb
     * @return array
     * @throws \Exception
     */
    private function formatAudiobook(Audiobook $audiobook, bool $addCover = false, bool $addTracks = false, bool $thumb = false): array
    {
        $c = new CoverService();
        $u = new UrlSafeService();
        $arr = [
            'id' => $audiobook->id,
            'name' => $audiobook->name,
            'encodedName' => $u->encode($audiobook->name),
            'year' => $audiobook->year,
            'duration' => $audiobook->duration,
            'size' => $audiobook->size,
            'authors' => $audiobook->authors,
            'narrators' => $audiobook->narrators,
            'numTracks' => $audiobook->tracks->count()
        ];
        if ($addCover && !$thumb) {
            $track = $audiobook->tracks()->first();
            $arr['cover'] = $c->getCover($track->id, $track->path, 'audiobooks', $track->cover);
        }
        if ($addTracks) {
            $arr['tracks'] = $audiobook->tracks;
        }
        if ($thumb) {
            $track = $audiobook->tracks()->first();
            $arr['thumbnail'] = $c->getCover($track->id, $track->path, 'audiobooks', $track->cover, true);
        }
        return $arr;
    }

    /**
     * @function json format for single track
     * @param Track $track
     * @param int $discs
     * @return array
     */
    private function formatTrack(Track $track, int $discs): array
    {
        $u = new UrlSafeService();
        $arr = [
            'id' => $track->id,
            'name' => $track->name,
            'track' => $track->track,
            'disc' => $track->disc,
            'discs' => $discs,
            'codec' => $track->codec,
            'channel' => $track->channel,
            'size' => $track->size,
            'duration' => $track->duration,
            'sample_rate' => $track->sample_rate,
            'encodedPath' => $u->encode($track->path),
            'path' => $track->path
        ];
        if ($track->disc) {
            $currentDisc = $track->disc;
            $arr['discTracks'] = $track->audiobook->tracks->filter( function ($disc) use ($currentDisc) {
                return $disc->disc == $currentDisc;
            })->count();
        }

        return $arr;
    }


    /**
     * @function create response for "all audiobooks"
     * @return array
     */
    public function getAllAudiobooks(): array
    {
        $allAuthors = Author::all();
        $allNarrators = Narrator::all();
        $books = Audiobook::with('tracks')
            ->get()
            ->map(function (Audiobook $audiobook) {
                $audiobook->duration = $audiobook->tracks->sum('duration');
                $audiobook->size = $audiobook->tracks->sum('size');
                $audiobook->authors = $this->getBookAuthors($audiobook);
                $audiobook->narrators = $this->getBookNarrators($audiobook);
                // format audiobook json array
                return $this->formatAudiobook($audiobook, true, false);
            })->sortByDesc('year');

        return [
            'audiobooks' => array_values($books->toArray()),
            'authors' => array_values($allAuthors->toArray()),
            'narrators' => array_values($allNarrators->toArray())
        ];
    }

    /**
     * @function get authors of an audiobook
     * @param Audiobook $audiobook
     * @return array
     */
    private function getBookAuthors(Audiobook $audiobook): array
    {
        $allAuthors = Author::all();
        // get authors of the audiobook. some tracks might not have an author.
        return array_values(
            $audiobook->tracks
                ->unique('author_id')
                ->pluck('author_id')
                ->filter( function($authorId) {
                    return !is_null($authorId);
                })->map( function($authorId) use ($allAuthors) {
                    $author = $allAuthors->where('id', $authorId)->first();
                    return [
                        'id' => $author->id,
                        'name' => $author->name
                    ];
                })->toArray()
        );
    }

    /**
     * @function get narrators of an audiobook
     * @param Audiobook $audiobook
     * @return array
     */
    private function getBookNarrators(Audiobook $audiobook): array
    {
        $allNarrators = Narrator::all();
        return array_values(
            $audiobook->tracks
                ->unique('narrator_id')
                ->pluck('narrator_id')
                ->filter( function($narratorId) {
                    return !is_null($narratorId);
                })->map( function($narratorId) use ($allNarrators) {
                    $narrator = $allNarrators->where('id', $narratorId)->first();
                    return [
                        'id' => $narrator->id,
                        'name' => $narrator->name
                    ];
                })->toArray()
        );
    }

    /**
     * @function get a specific audiobook
     * @param string $name
     * @return array
     * @throws \Exception
     */
    public function getAudiobook(string $name): array
    {
        $u = new UrlSafeService();
        $book = Audiobook::where('name', $u->decode($name))
            ->with('tracks')
            ->first();
        $book->duration = $book->tracks->sum('duration');
        $book->size = $book->tracks->sum('size');
        $book->authors = $this->getBookAuthors($book);
        $book->narrators = $this->getBookNarrators($book);
        $book->discs = $book->tracks->unique('disc')->count();
        $book->tracks = $book->tracks
            ->sortBy([
                ['disc', 'asc'],
                ['track', 'asc'],
            ])
            ->values()
            ->map(function($track) use ($book) {
                return $this->formatTrack($track, $book->discs);
            });
        // format audiobook json array
        return $this->formatAudiobook($book, true, true);
    }

    /**
     * @function prepare mp3 track and return storage file name
     * @param string $path
     * @return array
     */
    public function playTrack(string $path): array
    {
        $u = new UrlSafeService();
        $track = Track::where('path', $u->decode($path))
            ->first();
        $storageTrackName = "$track->id.mp3";
        $serverPathName = config('collection.server.audiobooks.path').$track->path;
        if (Storage::disk('public')->missing($storageTrackName)) {
            Log::channel('api')->info("File '$storageTrackName' missing in public storage.");
            if (file_exists($serverPathName)) {
                Storage::disk('public')->put($storageTrackName, file_get_contents($serverPathName));
                Log::channel('api')->error("File '$storageTrackName' copied to public storage.");
            } else {
                Log::channel('api')->error("File '$serverPathName' does not exist.");
            }
        } else {
            Log::channel('api')->info("File '$storageTrackName' already exists in public storage.");
        }

        return [
            'id' => $track->id,
            'path' => "/storage/$storageTrackName",
            'name' => $track->name,
        ];
    }

    /**
     * @function get a number of random audiobooks
     * @return array
     */
    public function getRandomAudiobooks(): array
    {
        $books = Audiobook::with('tracks')
            ->inRandomOrder()
            ->limit(config('collection.stats.audiobooks.random'))
            ->get()
            ->map(function (Audiobook $audiobook) {
                $audiobook->duration = $audiobook->tracks->sum('duration');
                $audiobook->size = $audiobook->tracks->sum('size');
                $audiobook->authors = $this->getBookAuthors($audiobook);
                $audiobook->narrators = $this->getBookNarrators($audiobook);
                // format audiobook json array
                return $this->formatAudiobook($audiobook, false, false, true);
            });
        return array_values($books->toArray());
    }


}
