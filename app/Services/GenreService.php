<?php

namespace App\Services;

use App\Models\Genre;

class GenreService
{

    /**
     * @function determine the top genres by songs and return a formatted array
     * @return array
     */
    public function getGenresByDuration(): array
    {
        $json = [];
        Genre::with('songs')
            ->get()
            ->map(function ($genre) {
                $genre->numSongs = $genre->songs->count();
                $genre->duration = $genre->songs->sum('duration');
                $genre->size = $genre->songs->sum('size');
                $genre->numArtists = $genre->songs->unique('album_artist_id')->count();
                return $genre;
            })->sortByDesc('duration')
            ->each(function ($genre) use (&$json) {
                $json[] = $this->formatGenre($genre);
            });
        return $json;
    }

    /**
     * @function format a single genre
     * @param Genre $genre
     * @return array
     */
    public function formatGenre(Genre $genre): array
    {
        $u = new UrlSafeService;
        return [
            'id' => $genre->id,
            'encodedName' => $u->encode($genre->name),
            'name' => $genre->name,
            'numSongs' => $genre->numSongs,
            'numArtists' => $genre->numArtists,
            'duration' => $genre->duration,
            'size' => $genre->size
        ];
    }

    /**
     * @function get a specific genre by encoded name
     * @param string $name
     * @return array
     */
    public function getGenreByName(string $name): array
    {
        $u = new UrlSafeService;
        $a = new ArtistService;
        $s = new SongService;
        $genre = Genre::where('name', $u->decode($name))
            ->with('songs')
            ->first();
        $genre->numSongs = $genre->songs->count();
        $genre->duration = $genre->songs->sum('duration');
        $genre->size = $genre->songs->sum('size');
        $genre->numArtists = $genre->songs->unique('album_artist_id')->count();
        $json['genre'] = $this->formatGenre($genre);
        $json['songs'] = $genre->songs->map(function ($song) use ($s) {
            return $s->formatSong($song);
        });
        return $json;
    }

    /**
     * @function search database for genres
     * @param string $name
     * @return array
     */
    public function searchGenreByName(string $name): array
    {
        $genres = Genre::whereLike('name', "%$name%", caseSensitive: false)
            ->get();
        dd($genres);
    }
}
