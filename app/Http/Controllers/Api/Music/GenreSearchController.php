<?php

namespace App\Http\Controllers\Api\Music;

use App\Http\Controllers\Controller;
use App\Services\GenreService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GenreSearchController extends Controller
{
    /**
     * @param Request $request
     * @param string $search
     * @return JsonResponse
     */
    public function show(Request $request, string $search): JsonResponse
    {
        $g = new GenreService;
        $genres = $g->searchGenreByName($search);
        if (count($genres) > 0) {
            return response()->json([
                'searchTerm' => $search,
                'genres' => $genres
            ]);
        }
        else {
            return response()
                ->json(['message' => 'Die Suchanfrage konnte nicht ausgeführt werden.'], 422);
        }
    }
}
