<?php

namespace App\Http\Controllers\Api\Music;

use App\Http\Controllers\Controller;
use App\Models\Song;
use App\Services\UrlSafeService;
use App\Services\SongService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SongController extends Controller
{

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function list(Request $request): JsonResponse
    {
        $s = new SongService;
        $allSongs = $s->getAllSongs();
        if (count($allSongs) > 0) {
            return response()->json($allSongs, 200);
        } else {
            return response()
                ->json(['message' => 'Fehler beim laden der Songs. app:update durchgeführt?'], 422);
        }
    }

    /**
     * @param Request $request
     * @param string $path
     * @return JsonResponse
     * @throws \Exception
     */
    public function show(Request $request, string $path): JsonResponse
    {
        $s = new SongService;
        $song = $s->getSongByPath($path);
        if (count($song) > 0) {
            return response()->json($song);
        } else {
            return response()
                ->json(['message' => 'Es existiert kein Song mit diesem Pfad.'], 422);
        }
    }

    /**
     * @param Request $request
     * @param string $search
     * @return JsonResponse
     */
    public function search(Request $request, string $search): JsonResponse
    {
        $a = new SongService;
        $artists = $a->searchSongByName($search);
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
        $s = new SongService;
        $stats = $s->getRandomSongs();
        if (count($stats) > 0) {
            return response()->json($stats, 200);
        } else {
            return response()
                ->json(['message' => 'Fehler beim laden der Kachel Songs.'], 422);
        }
    }

}
