<?php

	use Kirby\Cms\App as Kirby;

    Kirby::plugin('beluga/navigation', [
        'fields' => [
            'navigation' => [
				'api' => require_once __DIR__ . '/config/api.php',
				'props' => require_once __DIR__ . '/config/props.php',
            ],
        ],
		'translations' => [
	        'en' => require_once __DIR__ . '/languages/en.php',
	        'de' => require_once __DIR__ . '/languages/de.php',
	        'tr' => require_once __DIR__ . '/languages/tr.php',
	        'fr' => require_once __DIR__ . '/languages/fr.php',
	    ],
		'snippets' => [
			'navigation' => __DIR__ . '/snippets/navigation.php'
		],
		'fieldMethods' => require_once __DIR__ . '/config/methods.php',
    ]);
