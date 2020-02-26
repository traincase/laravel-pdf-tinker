<?php

use Illuminate\Support\Facades\Route;
use Traincase\LaravelPdfTinker\Http\Controllers\PlaygroundController;

Route::group([
    'prefix' => config('laravel-pdf-tinker.route_prefix'),
    'as' => 'vendor.laravel-pdf-tinker.'
], function () {
    Route::get('/playground', [PlaygroundController::class, 'index'])
        ->name('playground');

    Route::post('/preview', [PlaygroundController::class, 'preview'])
        ->name('preview');

    Route::get('/pdf/{alias}', [PlaygroundController::class, 'download'])
        ->name('download');

    Route::get('/css', [PlaygroundController::class, 'css'])
        ->name('css');

    Route::get('/js', [PlaygroundController::class, 'js'])
        ->name('js');
});
