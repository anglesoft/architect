<?php

return [

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
