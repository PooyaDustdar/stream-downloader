<?php 
namespace Pdustdar\StreamDownloader;

use Illuminate\Support\ServiceProvider;

class StreamDownloaderServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(StreamDownloader::class,function ($app)
        {
            return new StreamDownloader;
        });

    }

    public function boot()
    {


    }
}
