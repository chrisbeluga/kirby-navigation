<?php

return [
    'toNavigationArray' => function ($field) {
        $items = [];
        foreach ($field->toStructure() as $child) {
            $children = [];

            if ($child->children()->isNotEmpty()) {
                array_push($children, $child->children());
            }

            $page = page($child->uuid()->toString());
            $target = $child->popup()->toBool() ? '_blank' : '_self';
            $isOpen = $page ? $page->isActive() : false;

            array_push($items, [
                'uuid' => $child->uuid()->toString(),
                'url' => $page ? $page->url() : $child->url(),
                'text' => $child->text()->value(),
                'clickable' => $child->clickable()->toBool(),
                'popup' => $child->popup()->toBool(),
                'isOpen' => $isOpen,
                'children' => $children,
            ]);
        }
        return $items;
    },
    'toNavigationMarkup' => function ($field) {
        return snippet('navigation', [
            'children' => $field
        ]);
    }
];
