<?php

use Illuminate\Support\Facades\Route;


Route::prefix('api')->group(function () {

    /**
     * Widget Controllers
     */
    // Global Widget Controller
    Route::get('/widget/global',
        [\App\Http\Controllers\Api\Widget\GlobalWidgetController::class, "show"]
    );
    // Song Widget Controller
    Route::get('/widget/song',
        [\App\Http\Controllers\Api\Widget\SongWidgetController::class, "show"]
    );
    // Genre Widget Controller
    Route::get('/widget/genre',
        [\App\Http\Controllers\Api\Widget\GenreWidgetController::class, "show"]
    );
    // Album Widget Controller
    Route::get('/widget/album',
        [\App\Http\Controllers\Api\Widget\AlbumWidgetController::class, "show"]
    );
    // Artist Widget Controller
    Route::get('/widget/artist',
        [\App\Http\Controllers\Api\Widget\ArtistWidgetController::class, "show"]
    );

    /**
     * Music Controllers
     */
    // Songs Controller
    Route::get('/music/songs',
        [\App\Http\Controllers\Api\Music\SongsController::class, "show"]
    );
    // Song Controller
    Route::get('/music/songs/{path}',
        [\App\Http\Controllers\Api\Music\SongController::class, "show"]
    );
    // Song Search Controller
    Route::get('/music/search/songs/{search}',
        [\App\Http\Controllers\Api\Music\SongSearchController::class, "show"]
    );

    // Genres Controller
    Route::get('/music/genres',
        [\App\Http\Controllers\Api\Music\GenresController::class, "show"]
    );
    // Genre Controller
    Route::get('/music/genres/{name}',
        [\App\Http\Controllers\Api\Music\GenreController::class, "show"]
    );
    // Genre Search Controller
    Route::get('/music/search/genres/{search}',
        [\App\Http\Controllers\Api\Music\GenreSearchController::class, "show"]
    );

    // Albums Controller
    Route::get('/music/albums',
        [\App\Http\Controllers\Api\Music\AlbumsController::class, "show"]
    );
    // Album Controller
    Route::get('/music/albums/{id}',
        [\App\Http\Controllers\Api\Music\AlbumController::class, "show"]
    );
    Route::get('/music/albums/{id}/download',
        [\App\Http\Controllers\Api\Music\AlbumController::class, "download"]
    );
    // Album Search Controller
    Route::get('/music/search/albums/{search}',
        [\App\Http\Controllers\Api\Music\AlbumSearchController::class, "show"]
    );

    // Artists Controller
    Route::get('/music/artists',
        [\App\Http\Controllers\Api\Music\ArtistsController::class, "show"]
    );
    // Artist Controller
    Route::get('/music/artists/{id}',
        [\App\Http\Controllers\Api\Music\ArtistController::class, "show"]
    );
    // Genre Search Controller
    Route::get('/music/search/artists/{search}',
        [\App\Http\Controllers\Api\Music\ArtistSearchController::class, "show"]
    );

    /**
     * Audiobook Controllers
     */
    // Audiobooks Controller
    Route::get('/audiobooks',
        [\App\Http\Controllers\Api\Audiobooks\AudiobooksController::class, "show"]
    );
    // Audiobook Controller
    Route::get('/audiobooks/{name}',
        [\App\Http\Controllers\Api\Audiobooks\AudiobookController::class, "show"]
    );

});


