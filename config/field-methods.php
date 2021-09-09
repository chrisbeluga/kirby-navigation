<?php

	return [
		'toNavigationMenu' => function($field) {
			return Yaml::decode($field->value());
		},
	];
