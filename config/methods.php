<?php

	return [
		'toNavigationMenu' => function($field) {

			$items = array();

			foreach($field->toStructure() as $child) {

				$children = array();

				if($child->children()->isNotEmpty()) {
					array_push($children, $child->children()->toNavigationMenu($field));
				}

				$fieldCustomData = array();
				$fieldDefaultData = array(
					'children' => $children,
				);

				foreach($child->content()->not('children')->data() as $key => $value) {
					$fieldCustomData[$key] = $child->{$key}();
				}

				array_push($items, (object)array_merge((array)$fieldDefaultData, (array)$fieldCustomData));

				/*
				$children = array();

				if($child->children()->isNotEmpty()) {
					array_push($children, $child->children()->toNavigationMenu($field));
				}

				array_push($items, array(
					'id' => $child->id(),
					'url' => $child->url()->value(),
					'text' => $child->text()->kirbytext(),
					'title' => $child->title()->value(),
					'popup' => $child->popup()->toBool(),
					'children' => $children,
				));*/

			}

			return $items;
		}
	];
