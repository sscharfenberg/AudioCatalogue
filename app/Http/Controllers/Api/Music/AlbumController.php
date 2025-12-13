<?php

namespace App\Http\Controllers\Api\Music;

use App\Http\Controllers\Controller;
use App\Models\Album;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Services\AlbumService;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AlbumController extends Controller
{

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function list(Request $request): JsonResponse
    {
        $a = new AlbumService;
        $allAlbums = $a->getAllAlbums();
        if (count($allAlbums) > 0) {
            return response()->json($allAlbums, 200);
        } else {
            return response()
                ->json(['message' => 'Fehler beim laden der Alben. app:update durchgeführt?'], 422);
        }
    }

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

    /**
     * @param Request $request
     * @param string $search
     * @return JsonResponse
     */
    public function search(Request $request, string $search): JsonResponse
    {
        $a = new AlbumService;
        $artists = $a->searchAlbumByName($search);
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
        $a = new AlbumService;
        $stats = $a->getRandomAlbums();
        if (count($stats) > 0) {
            return response()->json($stats);
        } else {
            return response()
                ->json(['message' => 'Fehler beim laden der Kachel Alben.'], 422);
        }
    }

}
