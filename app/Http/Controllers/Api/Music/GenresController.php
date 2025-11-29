<?php

namespace App\Http\Controllers\Api\Music;

use App\Http\Controllers\Controller;
use App\Services\GenreService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GenresController extends Controller
{

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function show(Request $request): JsonResponse
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

}
