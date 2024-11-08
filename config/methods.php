<?php
use Kirby\Uuid\Uuids;
return [
  'toNavigationArray' => function ($field) {
    // Refresh items to get the current multilang URL and page titles
    require __DIR__ . '/../includes/refresh_item.inc.php';
    $items=$refresh_items($field->yaml(), $field->key(), $field->model());
    $lang_code=kirby()->languages()->isNotEmpty() ? kirby()->language()->code() : 'default';
      
    // Use anonymous recursive function to process child items
    $process_item = function($item, $depth=1) use (&$process_item, $lang_code) {
      $item['depth']=$depth;
      // To help markup generation, set 'url', 'text' and 'title'
      // according to the current language
      if ($item['type']=='page') {
        $item['url']=$item[$lang_code . '_page_url'];
        $item['text']=$item[$lang_code . '_link_text'];
        $item['title']=$item[$lang_code . '_link_title'];
        if ($item['text']=='') {
        $item['text']=$item[$lang_code . '_page_title'];
        }
        if ($item['title']=='') {
        $item['title']=$item[$lang_code . '_page_title'];
        }
      }
      elseif ($item['type']=='custom') {
        $item['text']=$item[$lang_code . '_link_text'];
        $item['title']=$item[$lang_code . '_link_title'];
      }
      else {
        $item['error']=TRUE;
      }
      if ($item['title']==$item['text']) {
        // No need to have a title
        $item['title']='';
      }
      // Values used by previous plugin version
      $item['isOpen']=kirby()->url('current') === $item['url'];
      // To help markup generation and customization, put all attributes into an array
      $item['attributes']=[];
      if ($item['title']!='') {
        $item['attributes']['title']=$item['title'];
      }
      if (!empty($item['popup'])) {
        $item['attributes']['target']='_blank';
      }
      if ($item['url'] === kirby()->url('current')) {
        $item['attributes']['aria-current']='page';
      }
      // process child items as well, if any
      if (!empty($item['children'])) {
        foreach (array_keys($item['children']) as $key) {
          $item['children'][$key]=$process_item($item['children'][$key], $depth+1);
        }
      }
      return $item;
    };
    if (is_array($items) && $items) {
      foreach (array_keys($items) as $key) {
        $items[$key]=$process_item($items[$key]);
      }
    }
    return $items;
  },
  'toNavigationMarkup' => function ($field) {
    // Refresh items to get the current multilang URL and page titles
    // and set item values needed for markup generation
    $items=$field->toNavigationArray();
    // Generate HTML
    return snippet('navigation', [
      'children' => $items
    ]);
  }
];
