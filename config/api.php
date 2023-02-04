<?php

return function () {
    return [
        [
            'pattern' => 'listings/(:all)',
            'method' => 'GET',
            'action' => function ($uuid) {
                // Expects a page uuid stripped of 'page'
                $content = [];
                $breadcrumbs = [];
                $getData = $uuid !== 'site';
                $data = $getData ? page('page://' . $uuid) : site();

                if ($data->hasChildren()) {
                    if ($getData) {
                        foreach ($data->children()->first()->parents()->flip() as $parent) {
                            array_push($breadcrumbs,[
                                'uuid' => $parent->uuid()->toString(),
                                'text' => $parent->title()->value(),
                            ]);
                        }
                    }

                    foreach ($data->children() as $item) {
                        $uuid = $item->uuid()->toString();
                        array_push($content, [
                            'uuid' => $uuid,
                            'text' => $item->title()->value(),
                            'count' => $item->index()->count(),
                            'children' => [],
                        ]);
                    }
                }
                return [
                    'content' => $content,
                    'breadcrumbs' => $breadcrumbs,
                ];
            }
        ]
    ];
};
