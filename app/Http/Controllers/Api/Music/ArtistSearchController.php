<?php

namespace App\Http\Controllers\Api\Music;

use App\Http\Controllers\Controller;
use App\Services\ArtistService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ArtistSearchController extends Controller
{

    /**
     * @param Request $request
     * @param string $search
     * @return JsonResponse
     */
    public function show(Request $request, string $search): JsonResponse
    {
        $a = new ArtistService;
        $artists = $a->searchArtistByName($search);
        return response()->json([
            'searchTerm' => $search,
            'results' => $artists
        ]);
    }

}
