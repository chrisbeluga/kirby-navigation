<?php

	return [
		'value' => function ($value = []) {
			return Yaml::decode($value);
		},
	];
