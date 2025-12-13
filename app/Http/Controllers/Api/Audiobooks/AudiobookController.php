<?php

namespace App\Http\Controllers\Api\Audiobooks;

use App\Http\Controllers\Controller;
use App\Services\AudiobookService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AudiobookController extends Controller
{
    /**
     * @param Request $request
     * @param string $path
     * @return JsonResponse
     * @throws \Exception
     */
    public function show(Request $request,  string $path): JsonResponse
    {
        $a = new AudiobookService;
        $audiobooks = $a->getAudiobook($path);
        if (count($audiobooks) > 0) {
            return response()->json($audiobooks, 200);
        } else {
            return response()
                ->json(['message' => 'Kein Hörbuch mit diesem Namen gefunden. app:update durchgeführt?'], 422);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function list(Request $request): JsonResponse
    {
        $a = new AudiobookService;
        $audiobooks = $a->getAllAudiobooks();
        if (count($audiobooks) > 0) {
            return response()->json($audiobooks, 200);
        } else {
            return response()
                ->json(['message' => 'Fehler beim laden der AudiobookBooks. app:update durchgeführt?'], 422);
        }
    }

    /**
     * @param Request $request
     * @param string $path
     * @return JsonResponse
     */
    public function play(Request $request, string $path): JsonResponse
    {
        $a = new AudiobookService();
        return response()->json($a->playTrack($path));
    }

    /**
     * @param Request $request
     * @param string $search
     * @return JsonResponse
     */
    public function search(Request $request, string $search): JsonResponse
    {
        $a = new AudiobookService();
        $books = $a->searchAudiobookByName($search);
        return response()->json([
            'searchTerm' => $search,
            'results' => $books
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function widget(Request $request): JsonResponse
    {
        $a = new AudiobookService();
        $books = $a->getRandomAudiobooks();
        if (count($books) > 0) {
            return response()->json($books);
        } else {
            return response()
                ->json(['message' => 'Fehler beim laden der Kachel Audiobooks.'], 422);
        }
    }
}
