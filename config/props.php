<?php

use Kirby\Data\Yaml;

return [
    'value' => function ($value = []) {
        return Yaml::decode($value);
    },
];
