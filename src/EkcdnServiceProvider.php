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
        $this->mergeConfigFrom(
            __DIR__.'/config/filesystems.php',
            'filesystems.disks.ekcdn'
        );
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

        Storage::extend('ekcdn-storage', function () {
            return new Filesystem(new HttpsAdapter(config('filesystems.disks.ekcdn.domainUrl'), true, [], [
                "headers" =>[
                    'apiKey' => config('filesystems.disks.ekcdn.apiKey')
                ],
                "url" => config('filesystems.disks.ekcdn.url')
            ]));
        });
    }
}
