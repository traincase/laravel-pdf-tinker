<?php

namespace Traincase\LaravelPdfTinker\Http\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemException;
use Traincase\HtmlToPdfTinker\DTO\PdfToGenerateDTO;
use Traincase\LaravelPdfTinker\Http\Requests\PreviewRequest;
use Traincase\HtmlToPdfTinker\PdfTinkerManager;

class PlaygroundController
{
    public function index(PdfTinkerManager $manager): Response
    {
        return response()->view('laravel-pdf-tinker::playground', [
            'drivers' => $manager->getRegisteredDrivers(),
        ]);
    }

    public function preview(
        PreviewRequest $request,
        Filesystem $filesystem,
        PdfTinkerManager $tinkerManager
    ): JsonResponse {
        try {
            $filename = Str::random(10);

            $options = json_decode($request->input('options', '{}'), true);

            if (!is_array($options)) {
                throw new \Exception('Driver options are not in valid json format.');
            }

            $tinkerManager
                ->resolve($request->input('driver'))
                ->storeOnFilesystem(
                    $filesystem,
                    PdfToGenerateDTO::fromArray([
                        'orientation' => $request->input('mode', 'portrait'),
                        'html' => $request->input('html'),
                        'options' => $options,
                        'filename' => "{$filename}.pdf",
                        'path' => 'vendor/laravel-pdf-tinker',
                    ])
                );

            return response()->json([
                'url' => route('vendor.laravel-pdf-tinker.download', ['alias' => $filename])
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function download(string $alias, Filesystem $filesystem): Response
    {
        try {
            $contents = $filesystem->read($location = "vendor/laravel-pdf-tinker/{$alias}.pdf");

            $filesystem->delete($location);

            return new Response($contents, 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => "inline; filename=pdf-tinker-{$alias}.pdf",
            ]);
        } catch (FilesystemException $exception) {
            return response('PDF not found', 404);
        }
    }

    /**
     * Serve our own css, so people don't have to publish/build assets
     */
    public function css()
    {
        return new Response(file_get_contents(__DIR__ . '/../../../build/playground.css'), 200, [
            'Content-Type' => 'text/css',
        ]);
    }

    /**
     * Serve our own js, so people don't have to publish/build assets
     */
    public function js()
    {
        return new Response(file_get_contents(__DIR__ . '/../../../build/playground.js'), 200, [
            'Content-Type' => 'text/javascript',
        ]);
    }
}
