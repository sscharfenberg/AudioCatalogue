<?php

use Illuminate\Support\Facades\Route;


Route::prefix('api')->group(function () {

    /**************************************************************************
     * Widget Controllers
     *************************************************************************/

    // Global Widget Controller
    Route::get('/widget/global',
        [\App\Http\Controllers\Api\Widget\GlobalWidgetController::class, 'show']
    );
    // Audiobook Widget Controller
    Route::get('/widget/audiobook',
        [\App\Http\Controllers\Api\Widget\AudiobookWidgetController::class, 'show']
    );

    /**************************************************************************
     * Music Controllers
     *************************************************************************/

    /**
     * SongController
     */
    // list songs
    Route::get('/music/songs',
        [\App\Http\Controllers\Api\Music\SongController::class, 'list']
    );
    // show a specific song
    Route::get('/music/songs/{path}',
        [\App\Http\Controllers\Api\Music\SongController::class, 'show']
    );
    // search for songs
    Route::get('/music/search/songs/{search}',
        [\App\Http\Controllers\Api\Music\SongController::class, 'search']
    );
    // load song widget
    Route::get('/widget/song',
        [\App\Http\Controllers\Api\Music\SongController::class, 'widget']
    );

    /**
     * GenreController
     */
    // list genres
    Route::get('/music/genres',
        [\App\Http\Controllers\Api\Music\GenreController::class, 'list']
    );
    // show a specific genre
    Route::get('/music/genres/{name}',
        [\App\Http\Controllers\Api\Music\GenreController::class, 'show']
    );
    // search genres
    Route::get('/music/search/genres/{search}',
        [\App\Http\Controllers\Api\Music\GenreController::class, 'search']
    );
    // load genre widget
    Route::get('/widget/genre',
        [\App\Http\Controllers\Api\Music\GenreController::class, 'widget']
    );

    /**
     * AlbumController
     */
    // list albums
    Route::get('/music/albums',
        [\App\Http\Controllers\Api\Music\AlbumController::class, 'list']
    );
    // show a specific album
    Route::get('/music/albums/{id}',
        [\App\Http\Controllers\Api\Music\AlbumController::class, 'show']
    );
    // download a specific album
    Route::get('/music/albums/{id}/download',
        [\App\Http\Controllers\Api\Music\AlbumController::class, 'download']
    );
    // search albums
    Route::get('/music/search/albums/{search}',
        [\App\Http\Controllers\Api\Music\AlbumController::class, 'search']
    );
    // load album widget
    Route::get('/widget/album',
        [\App\Http\Controllers\Api\Music\AlbumController::class, 'widget']
    );

    /**
     * ArtistController
     */
    // list artists
    Route::get('/music/artists',
        [\App\Http\Controllers\Api\Music\ArtistController::class, 'list']
    );
    // show a specific artist
    Route::get('/music/artists/{id}',
        [\App\Http\Controllers\Api\Music\ArtistController::class, 'show']
    );
    // search artists
    Route::get('/music/search/artists/{search}',
        [\App\Http\Controllers\Api\Music\ArtistController::class, 'search']
    );
    // load artist widget
    Route::get('/widget/artist',
        [\App\Http\Controllers\Api\Music\ArtistController::class, 'widget']
    );

    /**************************************************************************
     * Audiobook Controllers
     *************************************************************************/

    // list audiobooks
    Route::get('/audiobooks',
        [\App\Http\Controllers\Api\Audiobooks\AudiobookController::class, 'list']
    );
    // show a specific audiobook
    Route::get('/audiobooks/{name}',
        [\App\Http\Controllers\Api\Audiobooks\AudiobookController::class, 'show']
    );
    // play a specific track of an audiobook
    Route::get('/audiobooks/play/{path}',
        [\App\Http\Controllers\Api\Audiobooks\AudiobookController::class, 'play']
    );
    // search audiobooks
    Route::get('/audiobooks/search/{search}',
        [\App\Http\Controllers\Api\Audiobooks\AudiobookController::class, 'search']
    );
    // load audiobooks widget
    Route::get('/widget/audiobook',
        [\App\Http\Controllers\Api\Audiobooks\AudiobookController::class, 'widget']
    );

    /**************************************************************************
     * Playlist Controllers
     *************************************************************************/

    // Show Playlists
    Route::get('/playlists',
        [\App\Http\Controllers\Api\Playlists\PlaylistController::class, 'list']
    );
    // new Playlist
    Route::post('/playlists/new',
        [\App\Http\Controllers\Api\Playlists\PlaylistController::class, 'create']
    );
    // Sort Playlists
    Route::post('/playlists/sort',
        [\App\Http\Controllers\Api\Playlists\PlaylistController::class, 'sort']
    );
    // Edit Playlist
    Route::post('/playlists/edit',
        [\App\Http\Controllers\Api\Playlists\PlaylistController::class, 'edit']
    );
    // Delete Playlist
    Route::post('/playlists/delete',
        [\App\Http\Controllers\Api\Playlists\PlaylistController::class, 'delete']
    );
    // Add Song to Playlist
    Route::post('/playlists/add-song',
        [\App\Http\Controllers\Api\Playlists\PlaylistController::class, 'addSong']
    );

});


