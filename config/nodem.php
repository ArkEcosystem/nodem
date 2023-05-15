<?php

declare(strict_types=1);

return [

    'height_mismatch' => [
        'threshold' => [
            'default' => env('NODEM_HEIGHT_MISMATCH_THRESHOLD_DEFAULT', 5),
            'relay'   => env('NODEM_HEIGHT_MISMATCH_THRESHOLD_RELAY', 7),
        ],
    ],

];
