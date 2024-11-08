<?php

/** @var array $children */
/** 
 * Call this snippet with the "refreshed" nested array
 * of the navigation field items (and not with the field data).
 * See toNavigationMarkup() in methods.php for details.
 * 
 * This snippet recursively processes all child items.
 * To generate the markup for each link item, it calls
 * the 'navigation_item' snippet, allowing for easy customization.
 * 
 * To customize this snippet, copy it to your /site/snippets/ folder
 * and edit the copy.
 */

if (!isset($children) || !is_array($children) || !$children) {
  return;
}
if (isset($children['multilang'])) {
  // This data was not "refreshed" before calling this snippet.
  return;
}
?>
<ul>
  <?php foreach ($children as $item): ?>
    <li>
      <?php snippet('navigation_item', $item); ?>
      <?php if (!empty($item['children'])): ?>
        <?php snippet('navigation', ['children' => $item['children']]); ?>
      <?php endif ?>
    </li>
  <?php endforeach; ?>
</ul>
