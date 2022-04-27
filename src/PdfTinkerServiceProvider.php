<?php

namespace Traincase\LaravelPdfTinker;

use Illuminate\Support\ServiceProvider;
use Traincase\HtmlToPdfTinker\Drivers\DompdfDriver;
use Traincase\HtmlToPdfTinker\Drivers\WkhtmltopdfDriver;
use Traincase\HtmlToPdfTinker\PdfTinkerManager;

class PdfTinkerServiceProvider extends ServiceProvider
{
    public function register()
    {
        /** Bind the manager in the container, to allow addition of drivers */
        $this->app->singleton(PdfTinkerManager::class, function () {
            return (new PdfTinkerManager)
                ->extend('dompdf', function () {
                    $dompdf = new \Dompdf\Dompdf();
                    $dompdf->setBasePath(realpath(base_path('public')));

                    return new DompdfDriver($dompdf);
                })
                ->extend('wkhtmltopdf', function () {
                    return new WkhtmltopdfDriver(new \mikehaertl\wkhtmlto\Pdf);
                });
        });

        /** Make sure we can type hint the Filesystem in our controller. */
        $this->app->bind(\League\Flysystem\Filesystem::class, function () {
            return new \League\Flysystem\Filesystem(
                new \League\Flysystem\Local\LocalFilesystemAdapter(storage_path('/'))
            );
        });
    }

    public function boot()
    {
        $this->loadViewsFrom(__DIR__ . '/../assets/views', 'laravel-pdf-tinker');

        $this->mergeConfigFrom(__DIR__ . '/../config/laravel-pdf-tinker.php', 'laravel-pdf-tinker');

        $this->loadRoutesFrom(__DIR__ . '/Http/routes.php');

        $this->publishes([
            __DIR__ . '/../config/laravel-pdf-tinker.php' => config_path('laravel-pdf-tinker.php'),
        ], 'laravel-pdf-tinker-config');
    }
}
