<?php

use Kirby\Data\Yaml;
use Kirby\Uuid\Uuids;

return function ($items) {
  // Use anonymous recursive function to process child items
  $prepare_item = function($item) use (&$prepare_item) {
    if (!is_array($item) || !isset($item['type'])) {
      throw new Exception('Unexpected data found in the navigation field while saving');
    }
    if ($item['type'] === 'page') {
      // The primary way of page identification is by 'uuid_uri',
      // if not available, then by 'id'.
      // Although page url and page title are saved here,
      // these values will be refreshed when the field data is loaded,
      // 

      // Do not store 'count' value (coming from api.php)
      unset($item['count']);
    }
    // Remove the 'error' value, it will be set again when loading field values
    unset($item['error']);
    // prepare child items, if any
    if (!empty($item['children'])) {
      foreach (array_keys($item['children']) as $key) {
          $item['children'][$key]=$prepare_item($item['children'][$key]);
      }
    }
    return $item;
  };

  // Remove any item with type 'save',
  // indicating field data that was just submitted
    // This 'save' item is always added by props.php
  // This 'save' item is always removed by the save.php
  // This 'save' trick is needed, because the props.php is not only
  // executed when a field is loaded, but also when it is saved, and
  // otherwise it would not be possible to distinguish between the two.
  if (is_array($items) && $items) {
    foreach (array_keys($items) as $key) {
      if (isset($items[$key]['type']) && ($items[$key]['type']==='save')) {
        unset($items[$key]);
      }
    }
    if ($items) {
      // When the 'save' item is deleted, there may be a gap in keys
      // Make sure that array keys have no gaps, to avoid JS problems
      $items=array_values($items);
    }
}

  if (is_array($items) && $items) {
    foreach (array_keys($items) as $key) {
      $items[$key]=$prepare_item($items[$key]);
    }
  }

  // Get the 'multilang' option of the field blueprint
  $blueprint_field=$this->model()->blueprint()->field($this->name());
  if (!empty($blueprint_field) && !empty($blueprint_field['multilang'])) {
    // If 'multilang' is set in the blueprint, then data is stored
    // in external yaml file, so that it can be shared between languages
    $filepathTMP = tempnam(sys_get_temp_dir(), 'navigation');
    if (file_put_contents($filepathTMP, Yaml::encode($items))) {
      if (rename($filepathTMP, $this->model()->root() . '/kirby-navigation---' . $this->name() . '.yml')) {
        // The data was successfully saved to the external file,
        // so change the field data to a simple flag that will tell
        // the load function to load the external file.
        $items=['multilang' => TRUE];
      }
      else {
      }
    }
    else {
    }
    @unlink($filepathTMP);
  }
  return $items;
};
