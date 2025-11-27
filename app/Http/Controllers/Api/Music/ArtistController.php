<?php

namespace App\Http\Controllers\Api\Music;

use App\Http\Controllers\Controller;

use App\Services\ArtistService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ArtistController extends Controller
{

    /**
     * @param Request $request
     * @param string $name
     * @return JsonResponse
     */
    public function show(Request $request, string $name): JsonResponse
    {
        $a = new ArtistService;
        $artist = $a->getArtistByName($name);
        if (count($artist) > 0) {
            return response()->json($artist);
        } else {
            return response()
                ->json(['message' => 'Es existiert kein Artist mit diesem Namen.'], 422);
        }
    }

}
