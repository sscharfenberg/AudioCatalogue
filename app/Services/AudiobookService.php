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
     * @return array
     */
    private function formatAudiobook(Audiobook $audiobook, bool $addCover = false): array
    {
        $c = new CoverService();
        $arr = [
            'id' => $audiobook->id,
            'name' => $audiobook->name,
            'year' => $audiobook->year,
            'duration' => $audiobook->duration,
            'size' => $audiobook->size,
            'authors' => $audiobook->authors,
            'narrators' => $audiobook->narrators
        ];
        if ($addCover) {
            $track = $audiobook->tracks()->first();
            $arr['cover'] = $c->getCover($track->id, $track->path, 'audiobooks', $track->cover);
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
            ->map(function (Audiobook $audiobook) use ($allAuthors, $allNarrators) {
                $audiobook->duration = $audiobook->tracks->sum('duration');
                $audiobook->size = $audiobook->tracks->sum('size');
                // get authors of the audiobook. some tracks might not have an author.
                $authors = [];
                $audiobook->tracks
                    ->unique('author_id')
                    ->pluck('author_id')
                    ->filter( function($authorId) {
                        return !is_null($authorId);
                    })->each( function($authorId) use ($allAuthors, &$authors) {
                        $author = $allAuthors->where('id', $authorId)->first();
                        if ($author) {
                            $authors[] = [
                                'id' => $author->id,
                                'name' => $author->name
                            ];
                        };
                    });
                $audiobook->authors = $authors;
                // get narrators. some tracks might not have a narrator, some audiobooks have several narrators.
                $narrators = [];
                $audiobook->tracks
                    ->unique('narrator_id')
                    ->pluck('narrator_id')
                    ->filter( function($narratorId) {
                        return !is_null($narratorId);
                    })->each( function($narratorId) use ($allNarrators, &$narrators) {
                        $narrator = $allNarrators->where('id', $narratorId)->first();
                        if ($narrator) {
                            $narrators[] = [
                                'id' => $narrator->id,
                                'name' => $narrator->name
                            ];
                        };
                    });
                $audiobook->narrators = $narrators;
                // format audiobook json array
                return $this->formatAudiobook($audiobook, true);
            })->sortByDesc('year');

        return [
            'audiobooks' => array_values($books->toArray()),
            'authors' => array_values($allAuthors->toArray()),
            'narrators' => array_values($allNarrators->toArray())
        ];
    }


    private function getCoverPath(Track $track, bool $thumb = false): string
    {
        return "";
    }

}
