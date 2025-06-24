<?php

Kirby::plugin('chrisbeluga/navigation', [
    'fields' => [
        'navigation' => [
            'api' => require_once __DIR__ . '/config/api.php',
            'props' => require_once __DIR__ . '/config/props.php',
            'save' => require_once __DIR__ . '/config/save.php',
        ]
    ],
    'translations' => [
        'de' => require_once __DIR__ . '/languages/de.php',
        'en' => require_once __DIR__ . '/languages/en.php',
        'fr' => require_once __DIR__ . '/languages/fr.php',
        'tr' => require_once __DIR__ . '/languages/tr.php',
    ],
    'snippets' => [
        'navigation' => __DIR__ . '/snippets/navigation.php',
        'navigation_item' => __DIR__ . '/snippets/navigation_item.php',
    ],
    'fieldMethods' => require_once __DIR__ . '/config/methods.php',
    'options' => [
        'edit_title' => TRUE,
        'edit_popup' => TRUE,
        'edit_target' => FALSE,
        'edit_class' => FALSE,
        'edit_anchor' => FALSE,
    ],
]);
