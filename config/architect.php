<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Architect Sprints
    |--------------------------------------------------------------------------
    |
    | Determine the path where the sprint files will be saved.
    |
    */

    'sprints' => [
        'path' => 'sprints',
    ],

    'compiler' => [
        'properties' => 'snake', // 'camel'
        'namespaces' => [
            'tasks' => 'App\\Tasks',
            'features' => 'App\\Features',
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | Architect Database Driver
    |--------------------------------------------------------------------------
    |
    | Determine the database driver that will be used to store the sprints.
    |
    */

    'database' => [
        'connection' => env('DB_CONNECTION', 'mysql'),
        'table' => 'sprints',
    ],
];
