<?php

return [
    /**
     * The default options for each driver. These will be
     * inserted into the options field in the config,
     * when the developer enters the playground.
     */
    'default_driver_options' => [
        'wkhtmltopdf' => [
            'margin-top' => 0,
            'margin-right' => 0,
            'margin-bottom' => 0,
            'margin-left' => 0
        ],

        'dompdf' => [
            'isHtml5ParserEnabled' => false,
        ]
    ],

    /**
     * When routes are not enabled, you will have to manually create a playground.
     */
    'route_prefix' => '/vendor/laravel-pdf-tinker',
];
