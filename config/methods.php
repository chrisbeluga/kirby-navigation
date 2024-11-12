<?php
use Kirby\Data\Yaml;
return [
  // This method is the preferred way to get all the link items
  // (with all child links) as a nested array.
  // Do not use $field->toArray() in case of the navigation field.
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
  // This method is the preferred way to output the markup of a
  // navigation field from your template files.
  'toNavigationMarkup' => function ($field) {
    // Refresh items to get the current multilang URL and page titles
    // and set item values needed for markup generation
    $items=$field->toNavigationArray();
    // Generate HTML
    return snippet('navigation', [
      'children' => $items
    ]);
  },
  // This method is provided only for compatibility reasons,
  // to help the users of older plugin versions
  'toNavigationStructure' => function ($field) {
    // Refresh items to get the current multilang URL and page titles
    // and set item values needed for markup generation
    $items=$field->toNavigationArray();
    // Use anonymous recursive function to process child items
    $process_item = function($key, $item) use (&$process_item) {
      // Preserve any original 'id' value as 'link_id'
      $items['link_id']=$item['id'] ?? '';
      // Overwrite the 'id' (slug) value in link items,
      // because the Structure class also uses it as index
      $item['id']=$key;
      // process child items as well, if any
      if (!empty($item['children'])) {
        foreach (array_keys($item['children']) as $child_key) {
          $item['children'][$child_key]=$process_item($child_key, $item['children'][$child_key]);
        }
      }
      return $item;
    };
    if (is_array($items) && $items) {
      foreach ($items as $key => $item) {
        $items[$key]=$process_item($key, $item);
      }
    }
    $modified_field=$field->value(Yaml::encode($items));
    return $modified_field->toStructure();
  },
];
