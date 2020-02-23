<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="{{ $pageDescription }}">
        <link rel="icon" href="../../favicon.ico">

        <title>{{ $pageTitle }} - {{ config('app.name') }}</title>

        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.12.1/css/all.min.css">

        <!-- Core CSS -->
        <link href="{{ route('vendor.laravel-pdf-tinker.css') }}" rel="stylesheet">
    </head>

    <body>
        <nav class="navbar navbar-expand navbar-dark bg-dark">
            <a class="navbar-brand" href="#">{{ config('app.name') }}</a>

            <div class="collapse navbar-collapse">
                <ul class="navbar-nav mr-auto"></ul>
                @stack('navbar_actions')
            </div>
        </nav>
        <div class="@if(isset($pageWidth) && $pageWidth === 'full')container-fluid @else container @endif">
            @yield('content')
        </div>

        <script src="{{ route('vendor.laravel-pdf-tinker.js') }}" type="application/javascript"></script>
        @stack('js')
    </body>
</html>
