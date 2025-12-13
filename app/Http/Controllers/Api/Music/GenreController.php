<?php

namespace App\Http\Controllers\Api\Music;

use App\Models\Genre;
use App\Http\Controllers\Controller;
use App\Services\GenreService;
use App\Services\SongService;
use App\Services\UrlSafeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GenreController extends Controller
{

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function list(Request $request): JsonResponse
    {
        $g = new GenreService;
        $allGenres = $g->getGenresByDuration();
        if (count($allGenres) > 0) {
            return response()->json($allGenres);
        } else {
            return response()
                ->json(['message' => 'Fehler beim laden der Genres. app:update durchgeführt?'], 422);
        }
    }

    /**
     * @param Request $request
     * @param string $name
     * @return JsonResponse
     */
    public function show(Request $request, string $name): JsonResponse
    {
        $g = new GenreService;
        $genre = $g->getGenreByName($name);
        if (count($genre) > 0) {
            return response()->json($genre);
        } else {
            return response()
                ->json(['message' => 'Es existiert kein Genre mit diesem Namen.'], 422);
        }
    }

    /**
     * @param Request $request
     * @param string $search
     * @return JsonResponse
     */
    public function search(Request $request, string $search): JsonResponse
    {
        $g = new GenreService;
        $genres = $g->searchGenreByName($search);
        return response()->json([
            'searchTerm' => $search,
            'results' => $genres
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function widget(Request $request): JsonResponse
    {
        $g = new GenreService;
        $genres = $g->getGenresByDuration();
        if (count($genres) > 0) {
            return response()->json(
                array_slice(
                    $genres, 0, config('collection.stats.genres.num_top')
                )
            );
        } else {
            return response()
                ->json(['message' => 'Fehler beim Laden der Kachel Genres.'], 422);
        }
    }

}
