<?php

namespace App\Http\Controllers\Api\Widget;

use App\Http\Controllers\Controller;
use App\Services\AlbumService;
use App\Services\AudiobookService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AudiobookWidgetController extends Controller
{

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function show(Request $request): JsonResponse
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
