<?php

return [
    'default' => 'mongodb',
    'connections' => [
        'mongodb' => [
            'driver' => 'mongodb',
            'queue' => env('QUEUE_CONNECTION', 'mongodb'),
            'connection' => 'mongodb',
            'table' => 'jobs',
            'queue' => 'default',
            'expire' => 60,
            'retry_after' => 60,
        ],
    ],
    'failed' => [
        'database' => env('DB_CONNECTION', 'mongodb'),
        'table' => 'failed_jobs',
    ],
];