# Laravel PDF Tinker
**This package is meant as a tool for local development. Do not include this package in your production build.**

[![Latest Version on Packagist](https://img.shields.io/packagist/v/traincase/laravel-pdf-tinker.svg?style=flat-square)](https://packagist.org/packages/traincase/laravel-pdf-tinker)
[![Quality Score](https://img.shields.io/scrutinizer/g/traincase/laravel-pdf-tinker.svg?style=flat-square)](https://scrutinizer-ci.com/g/traincase/laravel-pdf-tinker)

Laravel PDF Tinker gives you a quick interface to debug and fiddle with the creation of HTML templates that will be converted into PDF's.

If you're one of those guys that frequently has to create custom templates for every client (order confirmations, goods deliveries, invoices etc.) this package is for you.


## Installation
Since this package is only meant for during local development install this package as a development dependency.

`composer require traincase/laravel-pdf-tinker --dev`

The package will automatically register it's service provider, if you [opted out of auto-discovery](https://laravel.com/docs/packages#package-discovery) add something along the lines of the following to a registered service provider (for example the `AppServiceProvider`):
 
```php
// Put this inside the 'register' method
if ($this->app->isLocal()) {
    $this->register(\Traincase\LaravelPdfTinker\PdfTinkerServiceProvider::class);
}
```

Don't forget to check [the drivers section](#drivers) to see if the driver you're planning to use requires any additional set-up.

## Usage
You can find most options straight in the interface of the playground.
Chances are high you don't have to edit the configuration.
Nevertheless, there are a few things you can configure.

### Configuration

#### Route prefix
The service provider will register a few routes necessary to set up and run the playground.
By default the routes are all prefixed with `vendor/laravel-pdf-tinker`. 
This means the playground is available at `your-app.tld/vendor/laravel-pdf-tinker/playground`.

Do you have an unlikely collision with your routes? No worries!

You may publish the configuration file (`php artisan vendor:publish`) and edit the `route_prefix` key in `config/laravel-pdf-tinker.php` to use a different prefix of your liking.

#### Driver options
If you published the configuration file you can also update the `default_driver_options.<your-driver-alias>` key to set the default options that will be set in the interface (under Configuration > Options).
This will just be used when you enter the playground, you can always update the configuration inside the playground.
Any changes will not be persisted once you leave the page, so if you tinker a lot you might want to override the default options.

### Drivers
Currently there are two drivers available. 

#### DomPDF
[DomPdf](https://github.com/dompdf/dompdf) is the easiest driver to get started.
Everything should already be in place after following the installation instructions. 

Happy tinkering! 

#### Wkhtmltopdf
The wkhtmltopdf driver needs the `wkhtmltopdf` executable to be available in your PATH.
This means that if you run `wkhtmltopdf --version` in a terminal it should output something like the following:

```bash
$ wkhtmltopdf --version
wkhtmltopdf 0.12.5 (with patched qt)
```
If you're using mac&homebrew you can run `brew cask install wkhtmltopdf`. 
On any other system download the binaries for your system from the [wkhtmltopdf website](https://wkhtmltopdf.org/downloads.html).

#### Creating your own drivers
It's possible to create a driver of the HTML2PDF generation library of your choice. Simply create your driver and register it with the `PdfTinkerManager`.

1. First things first, this is the time to create your driver. Your driver should extend `\Traincase\HtmlToPdfTinker\Drivers\Driver`. You can take an example from `Traincase\LaravelPdfTinker\DompdfDriver`.
2. Register it with the `PdfTinkerManager`. Add the following lines to one of your registered service providers in the `boot` method.
    ```php
    public function boot()
    {
        if ($this->app->isLocal()) {
            $manager = $this->app->make(\Traincase\HtmlToPdfTinker\PdfTinkerManager::class);
   
            $manager->extend('your-driver-alias', function() {
                // Need to fetch some things from the container? Sure.
                $dependency = $this->app->make(\SomeFancyClass::class);
   
                return new \App\Drivers\YourFancyDriver($dependency);
            });
        }
    }
    ```
3. Your driver will automatically show up in the interface in the drivers dropdown, you're free to tinker!

### Ready, set...
If you followed all the installation instructions go ahead and tinker away! Open up your browser and navigate to `your-app.tld/vendor/laravel-pdf-tinker/playground`.
