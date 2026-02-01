<?php

if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php')) {
    require_once($_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php');
}

$settings = [
    'exception_handling' =>
        [
            'value' =>
                [
                    'debug' => true,
                    'handled_errors_types' => 4437,
                    'exception_errors_types' => 4437,
                    'ignore_silence' => false,
                    'assertion_throws_exception' => true,
                    'assertion_error_type' => 256,
                ],
            'readonly' => false,
        ],
    'routing' => [
        'value' => [
            'config' => ['api.php'],
        ],
        'readonly' => true,
    ]
];


return $settings;
