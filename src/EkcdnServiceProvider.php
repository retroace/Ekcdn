<?php

namespace Retroace\Storage;

use Retroace\Storage\Adapter\HttpsAdapter;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;
use League\Flysystem\Filesystem;

class EkcdnServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
        Storage::extend('ekcdn-storage', function ($app, $config) {
            return new Filesystem(new HttpsAdapter($config['domainUrl'], true, [], [
                "headers" =>[
                    'apiKey' => config('filesystems.disks.ekcdn.apiKey')
                ],
                "url" => config('filesystems.disks.ekcdn.url')
            ]));
        });
    }
}
