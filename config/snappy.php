<?php

return [
    'pdf' => [
        'enabled' => true,
        'binary'  =>  env('APP_ENV'),
        'timeout' => 180,
        'enable-local-file-access' => true,
        'options' => array(
            'margin-top'    => 15,
            'margin-right'  => 10,
            'margin-bottom' => 15,
            'margin-left'   => 10,
        ),
        'env'     => [],
    ],
];
