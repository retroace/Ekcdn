<?php

/**
 * Custom file system driver to be merged with laravel's default
 * filesystem. Define custom placeholder fot the api key and
 * domain url.
 */
return [
    'driver' => 'ekcdn-storage',
    'url' => env('EKCDN_ASSET_PREFIX_URL'),
    'apiKey' => env('EKCDN_API_KEY'),
    'domainUrl' =>  env('EKCDN_STORAGE_URL')
];
