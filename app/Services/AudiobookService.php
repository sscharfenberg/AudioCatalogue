<?php

namespace App\Services;

use App\Models\Audiobook;
use App\Models\Author;
use App\Models\Narrator;
use App\Models\Track;

class AudiobookService
{

    /**
     * @function format json for audiobook
     * @param Audiobook $audiobook
     * @param bool $addCover
     * @param bool $addTracks
     * @return array
     * @throws \Exception
     */
    private function formatAudiobook(Audiobook $audiobook, bool $addCover = false, bool $addTracks = false): array
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
        if ($addCover) {
            $track = $audiobook->tracks()->first();
            $arr['cover'] = $c->getCover($track->id, $track->path, 'audiobooks', $track->cover);
        }
        if ($addTracks) {
            $arr['tracks'] = $audiobook->tracks;
        }
        return $arr;
    }

    private function formatTrack(Track $track): array
    {
        $u = new UrlSafeService();
        $arr = [
            'id' => $track->id,
            'name' => $track->name,
            'track' => $track->track,
            'codec' => $track->codec,
            'channel' => $track->channel,
            'size' => $track->size,
            'duration' => $track->duration,
            'sample_rate' => $track->sample_rate,
            'path' => $u->encode($track->path)
        ];
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
        $book->tracks = $book->tracks->map(function($track) {
            return $this->formatTrack($track);
        });
        // format audiobook json array
        return $this->formatAudiobook($book, true, true);
    }

}
