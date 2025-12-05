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
     * @return JsonResponse
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
}
