<?php

namespace ChrisBeluga\Navigation;

use Exception;
use Kirby\Data\Yaml;
use Kirby\Form\Field\InputField;
use Kirby\Reflection\Attributes\Prop;

class NavigationField extends InputField
{
  /**
   * Maximum nesting depth of the link items
   */
  #[Prop]
  protected int|null $levels = null;

  protected mixed $value = null;

  public function api(): array
  {
    return (require __DIR__ . '/../config/api.php')();
  }

  public function fill(mixed $value): static
  {
    $items=Yaml::decode($value);

    // Check whether there is an item with type 'save',
    // which indicates that the data was just submitted, and
    // in this case no changes should be done.
    // This 'save' item is always added by fill()
    // This 'save' item is always removed by toStoredValue()
    // This 'save' trick is needed, because fill() is not only
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
      // Add a 'save' item. When the field value is sent to Panel,
      // the Field.vue will hide this item, but it will send it back,
      // and then fill() will detect it, and toStoredValue() will ignore it.
      // Use a constant uuid value for this item, to avoid diff problems
      $items[]=['type' => 'save', 'uuid' => 'save', 'children' => []];
    }
    return parent::fill($items);
  }

  public function props(): array
  {
    return [
      ...parent::props(),
      // Notify the Vue code which textfields should be editable by default.
      // Notes:
      // - The 'Popup' will be automatically hidden, if 'Target' is visible
      // - If a certain value is set in the field, then Vue will show
      //   the editable textfield even if the corresponding option is false
      // - These options can be set in /site/config/config.php
      'edit_title' => (bool) option('chrisbeluga.navigation.edit_title', true),
      'edit_popup' => (bool) option('chrisbeluga.navigation.edit_popup', true),
      'edit_target' => (bool) option('chrisbeluga.navigation.edit_target', false),
      'edit_class' => (bool) option('chrisbeluga.navigation.edit_class', false),
      'edit_anchor' => (bool) option('chrisbeluga.navigation.edit_anchor', false),
    ];
  }

  public function toStoredValue(): mixed
  {
    $items=$this->value;

    // Use anonymous recursive function to process child items
    $prepare_item = function($item) use (&$prepare_item) {
      if (!is_array($item) || !isset($item['type'])) {
        throw new Exception('Unexpected data found in the navigation field while saving');
      }
      if ($item['type'] === 'page') {
        // Do not store 'count' value (coming from api.php)
        unset($item['count']);
        // The primary way of page identification is by 'uuid_uri',
        // if not available, then by 'id'.
        // Although page url and page title are saved here,
        // these values will be refreshed when the field data is loaded.
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
    // This 'save' item is always added by fill()
    // This 'save' item is always removed by toStoredValue()
    // This 'save' trick is needed, because fill() is not only
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
  }
}
