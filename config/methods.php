<?php

return [
    'toNavigationArray' => function ($field) {
        $items = [];
        foreach ($field->toStructure() as $child) {
            $children = [];

            if ($child->children()->isNotEmpty()) {
                array_push($children, $child->children());
            }

            array_push($items, [
                'id' => $child->id(),
                'url' => $child->url()->value(),
                'text' => $child->text()->value(),
                'title' => $child->title()->value(),
                'popup' => $child->popup()->toBool(),
                'isOpen' => kirby()->url('current') === $child->url()->value(),
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
