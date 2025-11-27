<?php

namespace App\Services;

use App\Models\Album;
use App\Models\Artist;
use App\Models\Genre;
use App\Models\Song;
use App\Services\UrlSafeService;
use \Illuminate\Support\Collection;

class ArtistService
{

    /**
     * @function json format artist data
     * @param Artist $artist
     * @param bool $addSongs
     * @return array
     */
    public function formatArtist(Artist $artist, bool $addSongs = false): array
    {
        $u = new UrlSafeService;
        $songs = $artist->songs;
        $albums = $artist->albums;
        $arr = [
            'id' => $artist->id,
            'name' => $artist->name,
            'encodedName' => $u->encode($artist->name),
            'albums' => $albums->map(function (Album $album) use ($u) {
                return [
                    'id' => $album->id,
                    'name' => $album->name,
                    'encodedName' => $u->encode($album->name),
                    'year' => $album->year,
                ];
            })->toArray(),
            'numAlbums' => $albums->count(),
            'numSongs' => $songs->count(),
            'songsDuration' => $songs->sum('duration'),
            'songsFileSize' => $songs->sum('size'),
        ];
        if ($addSongs) {
            $s = new SongService;
            $arr['songs'] = $artist->songs->map(function ($song) use ($s) {
               return $s->formatSong($song);
            })->toArray();
        }
        return $arr;
    }

    /**
     * @function get random artists and return array for json
     * @return array
     */
    public function getRandomArtists():array
    {
        $artists = Artist::with('songs')
            ->with('albums')
            ->inRandomOrder()
            ->get()
            ->filter(function ($artist) {
                return $artist->albums->count() > 0;
            })->take(config('collection.stats.artists.random'))
            ->map(function (Artist $artist) {
                return $this->formatArtist($artist);
            })->toArray();
        return array_values($artists);
    }

    /**
     * @function get all artists in json format, including non albumArtists
     * @return array
     */
    public function getAllArtists():array
    {
        $artists = Artist::with('songs')
            ->with('albums')
            ->get()
            ->map(function (Artist $artist) {
                return $this->formatArtist($artist);
            })->toArray();
        return array_values($artists);
    }

    /**
     * @function get artist by name and return array for json response
     * @param string $artistName
     * @return array
     */
    public function getArtistByName(string $artistName):array
    {
        $u = new UrlSafeService;
        $artist = Artist::where('name', $u->decode($artistName))
            ->with('songs')
            ->with('albums')
            ->first();
        if ($artist) {
            return $this->formatArtist($artist, true);
        } else {
            return [];
        }
    }

}
