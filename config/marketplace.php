<?php

return [
    'locales' => ['sq', 'en'],
    'default_currency' => 'EUR',
    'listing_image_disk' => env('LISTING_IMAGE_DISK', 'public'),

    'auctions' => [
        'durations_hours' => [24, 72, 168],
        'anti_sniping_window_seconds' => 120,
        'anti_sniping_extension_seconds' => 120,
    ],
];
