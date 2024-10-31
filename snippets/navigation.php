<?php

/** @var \Kirby\Cms\Field $children */
/** 
 * This snippet should be called initially with the "refreshed" 
 * nested array of the navigation field items. See methods.php
 * for details.
 * 
 * Then this snippet calls itself to process all children items.
 * 
 * Although the field data is supposed to come from trusted users,
 * the data is considered 'untrusted', and is escaped properly.
 * For example, it is enough to have an accidental quote character
 * in the title and the generated HTML would be incorrect.
 */

if (!isset($children) || !is_array($children) || !$children) {
  return;
}
if (isset($children['multilang'])) {
  // This data was not "refreshed" before calling this snippet.
  return;
}

if ($multilang = $kirby->languages()->isNotEmpty()) {
  $lang_code=$kirby->language()->code();
  $default_lang_code=$kirby->defaultLanguage()->code();
}
else {
  $lang_code='default';
  $default_lang_code='default';
}

//echo"<pre>"; print_r($children); echo "</pre>";return;
?>
<ul>
  <?php foreach ($children as $item): ?>
    <?php 
      // Avoid invalid links in the HTML
      $error=!empty($item['error']);
      // Language specific values
      if ($item['type']=='page') {
        $url=$item[$lang_code . '_page_url'];
        $text=$item[$lang_code . '_link_text'];
        $title=$item[$lang_code . '_link_title'];
        if ($text=='') {
          $text=$item[$lang_code . '_page_title'];
        }
        if ($title=='') {
          $title=$item[$lang_code . '_page_title'];
        }
      }
      elseif ($item['type']=='custom') {
        $url=$item['url'];
        $text=$item[$lang_code . '_link_text'];
        $title=$item[$lang_code . '_link_title'];
      }
      else {
        $error=TRUE;
      }
      if ($title==$text) {
          // No need to have a title
          $title='';
      }
      // Common values
      $target_attr=empty($item['popup']) ? '' : 'target="_blank"';
      $current_attr=($url === kirby()->url('current')) ? 'aria-current' : '';
    ?>
    <?php if (!$error): ?>
      <li>
        <a href="<?php echo Str::esc($url, 'attr') ?>"
          title="<?php echo Str::esc($title, 'attr') ?>"
          <?php echo $target_attr; ?>
          <?php echo $current_attr; ?>
        >
          <?php echo Str::esc($text, 'html') ?>
        </a>
        <?php if (!empty($item['children'])): ?>
          <?php snippet('navigation', ['children' => $item['children']]); ?>
        <?php endif ?>
      </li>
    <?php endif ?>
  <?php endforeach; ?>
</ul>
