<?php
use Kirby\Uuid\Uuids;
/** 
 * Good to know:
 * - stored Kirby links always have 'id' values, Custom links never have that
 * - stored Kirby links may have 'uuid_uri' values, Custom links never have that
 * - The uuid_uri can identify a page even after the page slug changes,
 *   so it is the preferred way of identification.
 * - If uuid_uri value is not available for any reason, the plugin uses the 'id' value 
 *   to find the desired page
 * - The 'uuid' value used elsewhere in the plugin is not the same as 'uuid_uri' value.
 *   The 'uuid' value is kept for compatibility reasons.
 */

return function () {
  return [
    [
      'pattern' => 'listings/(:alpha)/(:all)',
      'method' => 'GET',
      'action' => function ($language_code, $path) {
        $content = [];
        $breadcrumbs = [];
        $getData = $path !== 'site' ? true : false;
        $data = $getData ? page($path) : site();

        $multilang = $this->kirby()->languages()->isNotEmpty();
        if ($multilang) {
          if ($language_code=='default') {
            // default language, use item title and url without translation
            $multilang=false;
          }
          elseif (!$this->kirby()->languages()->find($language_code)) {
            // invalid language, do nothing, just return an empty array
            $data=null;
          }
        }
        if (($data != null) && $data->hasChildren()) {
          if ($getData) {
            foreach ($data->children()->first()->parents()->flip() as $parent) {
              if ($multilang && ($parent->content($language_code)->title()->value() != null)) {
                $title=$parent->content($language_code)->title()->value();
              }
              else {
                $title = $parent->title()->value();
              }
              array_push($breadcrumbs,[
                'id' => $parent->id(),
                'title' => $title,
              ]);
            }
          }

          foreach ($data->children() as $item) {
            $title = $item->title()->value();
            if ($multilang && ($item->content($language_code)->title()->value() != null)) {
              $title=$item->content($language_code)->title()->value();
            }
            array_push($content, [
              // Values for page identification
              'type' => 'page',
              'uuid_uri' => Uuids::enabled() ? $item->uuid()->toString() : '',
              'id' => $item->id(),
              // Language-specific values that may change due to editing
              $language_code . '_page_url' => $multilang ? $item->url($language_code) : $item->url(),
              $language_code . '_page_title' => $title,
              // Default values of page links that prop.php also provides
              $language_code . '_link_text' => '',
              $language_code . '_link_title' => '',
              'children' => [],
              'target' => '',
              // Temporary helper values that will not be saved
              'count' => $item->index()->count(),
            ]);
          }
        }
        return [
          'content' => $content,
          'breadcrumbs' => $breadcrumbs,
        ];
      }
    ],
  ];
};
