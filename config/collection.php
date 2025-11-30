<?php

return [

    /*
     * Server settings. paths and filenames
     */
    'server' => [
        'music' => [
            'path' => '/home/samba/share/music/',
            'csv' => 'music.csv'
        ],
        'audiobooks' => [
            'path' => '/home/samba/share/audiobooks/',
            'csv' => 'audiobooks.csv'
        ],
        'to_delete' => [
            "Thumbs.db",
            "._*",
            "AlbumArt*",
            "*.gp5",
            ".DS_Store",
            ".@__*",
            ".smbdelete*"
        ],
        'download_album_threshold' => 1024 * 1024 * 200, // 200MiB
    ],

    /*
     * Database settings, if you need to increase field lengths for these special mp3s.
     */
    'db' => [
        // Music
        'artists' => [
            'name' => 255,
        ],
        'albums' => [
            'name' => 255,
        ],
        'genres' => [
            'name' => 128,
        ],
        'songs' => [
            'name' => 255,
            'publisher' => 128,
            'composer' => 255,
            'channel' => [
                'stereo',
                'dual_mono',
                'joint_stereo',
                'mono'
            ],
            'path' => 512
        ],
        // Audiobooks
        'authors' => [
            'name' => 128,
        ],
        'narrators' => [
            'name' => 128,
        ],
        'audiobooks' => [
            'name' => 128,
        ],
        'tracks' => [
            'name' => 128,
            'channel' => [
                'stereo',
                'dual_mono',
                'joint_stereo',
                'mono'
            ],
            'path' => 512
        ]
    ],

    'coverFile' => [
        'name' => 'Folder.jpg'
    ],

    'stats' => [
        'genres' => [
            'num_top' => 4
        ],
        'albums' => [
            'random' => 4
        ],
        'songs' => [
            'random' => 4
        ],
        'artists' => [
            'random' => 4
        ]
    ],

    'search_max' => [
        'genres' => 10,
        'artists' => 10,
        'albums' => 10,
        'songs' => 10,
    ]

];
