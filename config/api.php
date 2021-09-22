<?php

return function () {
    return [
        [
            'pattern' => 'listings/(:all)',
            'method' => 'GET',
            'action' => function ($all) {
                $content = [];
                $breadcrumbs = [];
                $getData = $all !== 'site' ? true : false;
                $data = $getData ? page($all) : site();

                if ($data->hasChildren()) {
                    if ($getData) {
                        foreach ($data->children()->first()->parents()->flip() as $parent) {
                            array_push($breadcrumbs,[
                                'id' => $parent->id(),
                                'title' => $parent->title()->value()
                            ]);
                        }
                    }

                    foreach ($data->children() as $item) {
                        array_push($content, [
                            'id' => $item->id(),
                            'url' => $item->url(),
                            'text' => $item->title()->value(),
                            'count' => $item->index()->count(),
                            'children' => []
                        ]);
                    }
                }
                return [
                    'content' => $content,
                    'breadcrumbs' => $breadcrumbs,
                ];
            }
        ],
    ];
};
