<?php

    return function () {
			return [
				[
					'pattern' => 'listings/(:all)',
					'method' => 'GET',
					'action'  => function($all) {
						$content = array();
						$breadcrumbs = array();
						$getData = $all != 'site' ? true : false;
						$data = $getData ? page($all) : site();
						if($data->hasChildren()) {
							if($getData) {
								foreach($data->children()->first()->parents()->flip() as $parent) {
									array_push($breadcrumbs, array(
										'id' => $parent->id(),
										'uid' => $parent->uid(),
										'title' => $parent->title()->value()
									));
								}
							}
							foreach($data->children() as $item) {
								array_push($content, array(
									'id' => $item->id(),
									'uid' => $item->uid(),
									'url' => $item->url(),
									'text' => $item->title()->value(),
									'count' => $item->index()->count(),
									'children' => array()
								));
							}
						}
						return array(
							'content' => $content,
							'breadcrumbs' => $breadcrumbs,
						);
					}
				],
			];
		};
