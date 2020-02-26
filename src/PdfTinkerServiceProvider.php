<?php

namespace Traincase\LaravelPdfTinker;

use Dompdf\Dompdf;
use Illuminate\Support\ServiceProvider;
use mikehaertl\wkhtmlto\Pdf;
use Traincase\HtmlToPdfTinker\Drivers\DompdfDriver;
use Traincase\HtmlToPdfTinker\Drivers\WkhtmltopdfDriver;
use Traincase\HtmlToPdfTinker\PdfTinkerManager;

class PdfTinkerServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(PdfTinkerManager::class, function() {
            $manager = new PdfTinkerManager();

            $manager->extend('dompdf', function() {
                $dompdf = new Dompdf();
                $dompdf->setBasePath(realpath(base_path('public')));

                return new DompdfDriver($dompdf);
            });

            $manager->extend('wkhtmltopdf', function() {
                return new WkhtmltopdfDriver(new Pdf);
            });

            return $manager;
        });
    }

    public function boot()
    {
        $this->loadViewsFrom(__DIR__ . '/../assets/views', 'laravel-pdf-tinker');

        $this->mergeConfigFrom(__DIR__.'/../config/laravel-pdf-tinker.php', 'laravel-pdf-tinker');

        $this->loadRoutesFrom(__DIR__.'/Http/routes.php');

        $this->publishes([
            __DIR__ . '/../config/laravel-pdf-tinker.php' => config_path('laravel-pdf-tinker.php'),
        ], 'laravel-pdf-tinker-config');
    }
}
