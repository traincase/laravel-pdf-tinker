<?php

namespace Traincase\LaravelPdfTinker\Tests;

use Orchestra\Testbench\Dusk\TestCase;

class BrowserTestCase extends TestCase
{
    protected static $baseServeHost = '127.0.0.1';
    protected static $baseServePort = 9000;

    protected function setUp(): void
    {
        \Orchestra\Testbench\Dusk\Options::withoutUI();

        parent::setUp();
    }

    protected function getPackageProviders($app)
    {
        return ['Traincase\LaravelPdfTinker\PdfTinkerServiceProvider'];
    }
}
