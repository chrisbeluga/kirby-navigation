<?php

use Kirby\Cms\App as Kirby;

Kirby::plugin('chrisbeluga/navigation', [
    'fields' => [
        'navigation' => [
            'api' => require_once __DIR__ . '/config/api.php',
            'props' => require_once __DIR__ . '/config/props.php',
        ]
    ],
    'translations' => [
        'de' => require_once __DIR__ . '/languages/de.php',
        'en' => require_once __DIR__ . '/languages/en.php',
        'fr' => require_once __DIR__ . '/languages/fr.php',
        'tr' => require_once __DIR__ . '/languages/tr.php',
    ],
    'snippets' => [
        'navigation' => __DIR__ . '/snippets/navigation.php'
    ],
    'fieldMethods' => require_once __DIR__ . '/config/methods.php',
]);
