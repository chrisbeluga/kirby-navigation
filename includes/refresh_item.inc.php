<?php
use Kirby\Data\Yaml;
use Kirby\Http\Uri;
use Kirby\Uuid\Uuids;
/**
* This file should be included wherever it is needed with the
* 'require' keyword (not 'include', and not 'require_once'!)
* Then it should be called like this to refresh an array of items
* and all its children:
* $items = $refresh_items($items, $field_name, $field_model);

* The purpose of these functions is to make it possible that only
* fixed values are saved in the field, that never (or rarely) change
* (for example id, or uuid_uri) and the values that may change 
* (for example page title, page url) are calculated by these
* functions, when needed.
*
* A secondary purpose of these functions is to make multilanguage
* navigation field editing as convinient as possible.
*/

// Use anonymous recursive function to process child items
$refresh_item = function($item, $kirby) use (&$refresh_item) {
  if (!is_array($item)) {
    throw new Exception('Unexpected data found in the navigation field');
  }
  if ($multilang = $kirby->languages()->isNotEmpty()) {
    $language_code=$kirby->language()->code();
    $default_language_code=$kirby->defaultLanguage()->code();
  }
  else {
    $language_code='default';
    $default_language_code='default';
  }
  // Upgrade from old data, to remain compatible with old plugin versions
  if (!isset($item['type'])) {
    // Add a 'type' value, that did not exist in the old plugin version
    // Previous plugin versions stored Kirby links with 'id' values, 
    // and stored Custom links without that
    $item['type'] = isset($item['id']) ? 'page' : 'custom';
    if ($item['type']=='page') {
      // Previous plugin versions used 'text' and 'title' values.
      // The new version uses 'LANG_link_text', 'LANG_link_title',
      // and 'LANG_page_title', where 'LANG' is the language code.
      // The new version outputs the 'LANG_page_title' as link HTML,
      // if the 'LANG_link_text' value is empty.
      if (isset($item['text'])) {
        $item[$default_language_code . '_link_text']=$item['text'];
        unset($item['text']);
      }
      if (isset($item['title'])) {
        $item[$default_language_code . '_link_title']=$item['title'];
        unset($item['title']);
      }
      // Previous plugin versions used 'url' value.
      // The new version uses 'LANG_page_url',
      // where 'LANG' is the language code.
      if (isset($item['url'])) {
        $item[$default_language_code . '_page_url']=$item['url'];
        unset($item['url']);
      }
    }
    elseif ($item['type']=='custom') {
      // Previous plugin versions used 'text' and 'title' values.
      // The new version uses 'LANG_link_text', 'LANG_link_title',
      // where 'LANG' is the language code.
      if (isset($item['text'])) {
        $item[$default_language_code . '_link_text']=$item['text'];
        unset($item['text']);
      }
      if (isset($item['title'])) {
        $item[$default_language_code . '_link_title']=$item['title'];
        unset($item['title']);
      }
    }
  }
  if ($item['type']=='page') {
    // Fetch page by the permanent 'uuid_uri', if possible, otherwise by 'id'
    $page=null;
    if (!empty($item['uuid_uri']) && Uuids::enabled()) {
      $page=$kirby->page($item['uuid_uri']);
      // Refresh the 'id' to handle any changes
      $item['id'] = $page->id();
    }
    if (!$page) {
      if ($page=$kirby->page($item['id'])) {
        if (empty($item['uuid_uri']) && Uuids::enabled()) {
          // Add a 'uuid_uri' value, that did not exist in the old plugin version
          $item['uuid_uri'] = $page->uuid()->toString();
        }
      }
    }
    if ($page) {
      // Refresh the 'url' and 'page_title' (in the current language) 
      // to handle any changes
      // Remember, that a navigation field built on e.g. localhost
      // should work perfectly when copied to the public site.
      $item[$language_code . '_page_url'] = $multilang ? $page->url($language_code) : $page->url();
      $item[$language_code . '_page_title'] = $multilang ? ($page->content($language_code)->title()->value() ?? ''): ($page->content()->title()->value() ?? '');
    }
    else {
      // This page no longer exists.
      // Set 'error' so that the item icon in panel will be 'question'
      $item['error']=TRUE;

      // Adjust the old url to the current site url using the latest known 'id' value
      if ($multilang) {
        $kirby->currentLanguage()->url() . '/' . $item['id'];
      }
      else {
        $item[$language_code . '_page_url']=Uri::current(['path' => $item['id'], 'query' => '',])->toString();
      }
    }
    // if no translation exists yet, add default values
    if (!isset($item[$language_code . '_link_text'])) {
      $item[$language_code . '_link_text']='';
    }
    if (!isset($item[$language_code . '_link_title'])) {
      $item[$language_code . '_link_title']='';
    }
  }
  elseif ($item['type']=='custom') {
    // Validate the URL
    if (empty($item['url']) || !V::url($item['url'])) {
      $item['error'] = TRUE;
    }
    // if no translation exists yet, add default values
    if (!isset($item[$language_code . '_link_text'])) {
      $item[$language_code . '_link_text']='';
    }
    if (!isset($item[$language_code . '_link_title'])) {
      $item[$language_code . '_link_title']='';
    }
  }
  // refresh child items, if any
  if (!empty($item['children'])) {
    foreach (array_keys($item['children']) as $key) {
      $item['children'][$key]=$refresh_item($item['children'][$key], $kirby);
    }
  }
  return $item;
};

$refresh_items = function($items, $field_name, $field_model) use ($refresh_item) {
  $kirby=kirby();
  // If 'multilang' is set in the field data, then the real data is stored
  // in external yaml file, so that it can be shared between languages
  // It is better to be prepared that the 'multilang' field option is
  // enabled/disabled while
  // - there is field data in primary or secondary languages
  // - there is ['multilang' => TRUE] data in primary field data,
  //   but there is real field data in secondary language
  // - there is real field data in primary language, but there is 
  //   ['multilang' => TRUE] data in secondary language
  // - the blueprint contains 'multilang: true', the primary language
  //   data is still stored in the page content file, not in external file
  //   (because it was not saved yet), but the field is edited in the
  //   secondary language in Panel.
  // - and all kinds of similar cases.
  //
  $multilang_site=$kirby->languages()->isNotEmpty();
  $multilang_blueprint=FALSE; 
  $multilang_field=FALSE;
  $multilang_load=FALSE;
  // Get the 'multilang' option of the field blueprint
  $field_blueprint=$field_model->blueprint()->field($field_name);
  if (!empty($field_blueprint) && !empty($field_blueprint['multilang'])) {
    $multilang_blueprint=TRUE;
  }
  if (!empty($items['multilang'])) {
    $multilang_field=TRUE;
  }

  if (!$multilang_blueprint && !$multilang_field) {
    // normal operation: use $items
  }
  elseif ($multilang_blueprint && $multilang_field) {
    // multilang operation with previous save: load from external file
    $multilang_load=TRUE;
  }
  elseif ($multilang_blueprint && !$multilang_field) {
    // multilang operation without previous save:
    // - if site is multilang, and this is primary language: use $items
    // - if site is multilang, and this is secondary language: 
    //   load data from the primary language
    // - if site has no languages: use $items
    if ($kirby->multilang()) {
      if ($kirby->language()->isDefault()) {
        // good, this is easy!
      }
      else {
        // The field data should be loaded from the primary language.
        $model=$field_model;
        $defaultContentTranslation=$model->translation($kirby->defaultLanguage()->code());
        $defaultContent=$defaultContentTranslation->content();
        $fieldContent=$defaultContent[$field_name];
        $items=Yaml::decode($fieldContent);

        // Furthermore, the primary data may be ['multilang' => TRUE],
        // instead of the real field data. Load the external file then.
        if (is_array($items) && !empty($items['multilang'])) {
          $multilang_load=TRUE;
        }
      }
    }
    else {
      // good, this is easy!
    }
  }
  else {
    // not multilang operation, but data happens to be saved externally,
    // so load it from there
    $multilang_load=TRUE;
  }
  if ($multilang_load) {
    $filepath=$field_model->root() . '/kirby-navigation---' . $field_name . '.yml';
    if (!file_exists($filepath)) {
      throw new Exception('Failed to load the navigation field data.' );
    }
    $contents=file_get_contents($filepath);
    if ($contents===FALSE) {
      throw new Exception('Failed to load the navigation field data.');
    }
    $items=Yaml::decode($contents);
    if (!is_array($items)) {
      $items=[];
    }
  }

  if (is_array($items) && $items) {
    foreach (array_keys($items) as $key) {
      $items[$key]=$refresh_item($items[$key], $kirby);
    }
  }
  return $items;
};

