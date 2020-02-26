<?php

namespace Traincase\LaravelPdfTinker\Http\Controllers;

use Exception;
use Illuminate\Support\Str;
use League\Flysystem\FileNotFoundException;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemInterface;
use Symfony\Component\HttpFoundation\Response;
use Traincase\HtmlToPdfTinker\DTO\PdfToGenerateDTO;
use Traincase\LaravelPdfTinker\Http\Requests\PreviewRequest;
use Traincase\HtmlToPdfTinker\PdfTinkerManager;

class PlaygroundController
{
    /**
     * @param PdfTinkerManager $manager
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(PdfTinkerManager $manager)
    {
        return view('laravel-pdf-tinker::playground', [
            'drivers' => $manager->getRegisteredDrivers(),
        ]);
    }

    public function preview(PreviewRequest $request, Filesystem $filesystem, PdfTinkerManager $tinkerManager)
    {
        try {
            $filename = Str::random('10');

            $options = json_decode($request->input('options', '{}'), true);
            if (!is_array($options)) {
                throw new \Exception('Driver options are not in valid json format.');
            }

            $tinkerManager->resolve($request->input('driver'))
                ->storeOnFilesystem($filesystem, new PdfToGenerateDTO([
                    'orientation' => $request->input('mode', 'portrait'),
                    'html' => $request->input('html'),
                    'options' => $options,
                    'filename' => "{$filename}.pdf",
                    'path' => 'vendor/laravel-pdf-tinker',
                ]));

            return response()->json([
                'url' => route('vendor.laravel-pdf-tinker.download', ['alias' => $filename])
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @param string $alias
     * @param FilesystemInterface $filesystem
     * @return Response
     */
    public function download(string $alias, Filesystem $filesystem)
    {
        try {
            return new Response(
                $filesystem->readAndDelete("vendor/laravel-pdf-tinker/{$alias}.pdf"),
                200,
                [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => "inline; filename=pdf-tinker-{$alias}.pdf",
                ]
            );
        } catch (FileNotFoundException $exception) {
            return response('PDF not found', 404);
        }
    }

    /**
     * Serve our own css, so people don't have to publish/build assets
     */
    public function css()
    {
        return new Response(
            file_get_contents(__DIR__ . '/../../../build/playground.css'),
            200,
            [
                'Content-Type' => 'text/css',
            ]
        );
    }

    /**
     * Serve our own js, so people don't have to publish/build assets
     */
    public function js()
    {
        return new Response(
            file_get_contents(__DIR__ . '/../../../build/playground.js'),
            200,
            [
                'Content-Type' => 'text/javascript',
            ]
        );
    }
}
