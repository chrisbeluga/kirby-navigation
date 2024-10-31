<?php
use Kirby\Uuid\Uuids;
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
                'uuid_uri' => Uuids::enabled() ? $child->uuid()->value() : '',
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
        // Refresh items to get the current multilang URL and page titles
        require __DIR__ . '/../includes/refresh_item.inc.php';
        $items=$refresh_items($field->yaml(), $field->key(), $field->model());
        // Generate HTML
        return snippet('navigation', [
            'children' => $items
        ]);
    }
];
