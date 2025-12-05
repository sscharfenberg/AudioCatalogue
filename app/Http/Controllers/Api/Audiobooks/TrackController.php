<?php

namespace App\Http\Controllers\Api\Audiobooks;

use App\Http\Controllers\Controller;
use App\Services\AudiobookService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TrackController extends Controller
{

    /**
     * @param Request $request
     * @param string $path
     * @return JsonResponse
     */
    public function play(Request $request, string $path): JsonResponse
    {
        $a = new AudiobookService();
        $a->playTrack($path);
    }

}
