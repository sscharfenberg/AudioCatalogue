<?php

namespace App\Http\Controllers\Api\Music;

use App\Http\Controllers\Controller;
use App\Models\Album;
use App\Services\AlbumService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AlbumController extends Controller
{

    /**
     * @param Request $request
     * @param string $id
     * @return JsonResponse
     */
    public function show(Request $request, string $id): JsonResponse
    {
        $a = new AlbumService;
        $album = $a->getAlbumById($id);
        if (count($album) > 0) {
            return response()->json($album);
        } else {
            return response()
                ->json(['message' => 'Es existiert kein Album mit dieser ID.'], 422);
        }
    }


    /**
     * @param Request $request
     * @param string $id
     * @return StreamedResponse
     */
    public function download(Request $request, string $id): StreamedResponse
    {
        $album = Album::with('songs')
            ->with('artist')
            ->findOrFail($id);
        $a = new AlbumService;

        return Storage::disk('downloads')->download(
            $a->downloadAlbum($album),
            $album->artist->name." - $album->name.zip",
            [ 'Content-Type' => 'application/zip' ]
        );
    }

}
