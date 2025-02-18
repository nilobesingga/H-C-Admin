<?php

return [
    'pdf' => [
        'enabled' => true,
        'binary'  =>  env('APP_ENV') == "local" ? '/usr/local/bin/wkhtmltopdf': base_path('vendor/h4cc/wkhtmltopdf-amd64/bin/wkhtmltopdf-amd64'),
        'timeout' => false,
        'options' => array(
            'enable-local-file-access' => true,
            'margin-top'    => 15,
            'margin-right'  => 10,
            'margin-bottom' => 15,
            'margin-left'   => 10,
        ),
        'env'     => [],
    ],
];
