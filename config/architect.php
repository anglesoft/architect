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
