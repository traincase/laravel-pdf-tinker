<?php

namespace Traincase\LaravelPdfTinker\Tests;

use Laravel\Dusk\Browser;
use League\Flysystem\FilesystemInterface;
use Traincase\HtmlToPdfTinker\Drivers\Driver;
use Traincase\HtmlToPdfTinker\DTO\PdfToGenerateDTO;
use Traincase\HtmlToPdfTinker\Exceptions\UnsupportedOutputTypeException;

class DriversTest extends BrowserTestCase
{
    /** @test */
    public function it_shows_registered_custom_drivers_in_options()
    {
        $this->tweakApplication(function ($app) {
            $app->resolving(\Traincase\HtmlToPdfTinker\PdfTinkerManager::class, function($manager) {
                $manager->extend('test-driver', function() {
                    return new class extends Driver {
                        public function storeOnFilesystem(FilesystemInterface $filesystem, PdfToGenerateDTO $dto): string
                        {
                            throw new UnsupportedOutputTypeException('It doesnt store on the filesystem');
                        }
                    };
                });
            });
        });

        $this->browse(function (Browser $browser) {
            $browser->visit(route('vendor.laravel-pdf-tinker.playground'))
                ->press('#nav-config-tab')
                ->assertSelectHasOption('#driver', 'test-driver');
        });

        $this->removeApplicationTweaks();
    }

    /** @test */
    public function it_allows_configuration_of_default_driver_options()
    {
        $config = ['foo' => 'bar', 'bool' => true, 'number' => 10];

        $this->tweakApplication(function ($app) use ($config) {
            $app->resolving(\Traincase\HtmlToPdfTinker\PdfTinkerManager::class, function($manager) {
                $manager->extend('test-driver', function() {
                    return new class extends Driver {
                        public function storeOnFilesystem(FilesystemInterface $filesystem, PdfToGenerateDTO $dto): string
                        {
                            throw new UnsupportedOutputTypeException('It doesnt store on the filesystem');
                        }
                    };
                });
            });

            $app['config']->set(
                'laravel-pdf-tinker.default_driver_options.test-driver',
                $config
            );
        });

        $configInInterface = json_encode($config, JSON_PRETTY_PRINT);

        $this->browse(function (Browser $browser) use ($configInInterface) {
            $browser->visit(route('vendor.laravel-pdf-tinker.playground'))
                ->press('#nav-config-tab')
                ->waitFor('#driver')
                ->select('#driver', 'test-driver')
                ->pause(200)
                ->assertValue('#options', $configInInterface);
        });

        $this->removeApplicationTweaks();
    }

    /** @test */
    public function it_refreshes_after_changing_html_content()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('vendor.laravel-pdf-tinker.playground'));

            // Doublecheck we're updating the HTML content
            $currentHtml = $browser->value('#code');
            $newHtml = '<html><head><title>Bare bones</title></head><body>Testing</body></html>';

            $this->assertNotEquals(
                $currentHtml,
                $newHtml
            );

            // What is the current URL being displayed?
            $currentPdfUrl = $browser->attribute('#preview', 'data');

            // Update the html in the code editor
            $browser->script("codeEditor.setValue('$newHtml');");
            $browser->pause(1000);

            // Url should have updated
            $this->assertNotEquals(
                $currentPdfUrl,
                $browser->attribute('#preview', 'data')
            );

            // It should (still) be a PDF
            $this->assertEquals('application/pdf', $browser->attribute('#preview', 'type'));
        });
    }
}
