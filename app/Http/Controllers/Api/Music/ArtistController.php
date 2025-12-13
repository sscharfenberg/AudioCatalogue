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

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function list(Request $request): JsonResponse
    {
        $a = new ArtistService;
        $artists = $a->getAllArtists();
        if (count($artists) > 0) {
            return response()->json($artists, 200);
        } else {
            return response()
                ->json(['message' => 'Fehler beim laden der Künstler. app:update durchgeführt?'], 422);
        }
    }

    /**
     * @param Request $request
     * @param string $search
     * @return JsonResponse
     */
    public function search(Request $request, string $search): JsonResponse
    {
        $a = new ArtistService;
        $artists = $a->searchArtistByName($search);
        return response()->json([
            'searchTerm' => $search,
            'results' => $artists
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function widget(Request $request): JsonResponse
    {
        $a = new ArtistService;
        $artists = $a->getRandomArtists();
        if (count($artists) > 0) {
            return response()->json($artists);
        } else {
            return response()
                ->json(['message' => 'Fehler beim laden der kachel Künstler.'], 422);
        }
    }

}
