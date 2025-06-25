<?php

use Kirby\Data\Yaml;

return [
  'value' => function ($value = []) {
    $items=Yaml::decode($value);

    // Check whether there is an item with type 'save',
    // which indicates that the data was just submitted, and 
    // in this case no changes should be done.
    // This 'save' item is always added by props.php
    // This 'save' item is always removed by the save.php
    // This 'save' trick is needed, because the props.php is not only
    // executed when a field is loaded, but also when it is saved, and
    // otherwise it would not be possible to distinguish between the two.
    $save_in_progress=FALSE;
    if (is_array($items) && $items) {
      foreach ($items as $item) {
        if (isset($item['type']) && ($item['type']==='save')) {
          // saving is in progress, do nothing.
          $save_in_progress=TRUE;
          break;
        }
      }
    }
    if (!$save_in_progress) {
      // Refresh items to get the current multilang URL and page titles
      require __DIR__ . '/../includes/refresh_item.inc.php';
      $items=$refresh_items($items, $this->name(), $this->model());
      // Add a 'save' item. When the 'props' values are sent to Panel,
      // the Field.vue will hide this item, but it will send it back,
      // and then the props.php will detect it, and save.php will ignore it.
      // Use a constant uuid value for this item, to avoid diff problems
      $items[]=['type' => 'save', 'uuid' => 'save', 'children' => []];
    }
    return $items;
  },
  // Notify the Vue code which textfields should be editable by default.
  // Notes:
  // - The 'Popup' will be automatically hidden, if 'Target' is visible
  // - If a certain value is set in the field, then Vue will show 
  //   the editable textfield even if the corresponding option is false
  // - These options can be set in /site/config/config.php
  'edit_title' => function() {
    return (bool) option('chrisbeluga.navigation.edit_title', true);
  },
  'edit_popup' => function() {
    return (bool) option('chrisbeluga.navigation.edit_popup', true);
  },
  'edit_target' => function() {
    return (bool) option('chrisbeluga.navigation.edit_target', false);
  },
  'edit_class' => function() {
    return (bool) option('chrisbeluga.navigation.edit_class', false);
  },
  'edit_anchor' => function() {
    return (bool) option('chrisbeluga.navigation.edit_anchor', false);
  }
];
