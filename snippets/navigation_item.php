<?php
/*
 * This snippet is typically called by the 'navigation' snippet.
 * It generates the markup for a single link item.
 * 
 * Important variables:
 * string $url  Link url
 * string $text  Link text
 * array $attributes Link attributes (e.g. title, target, aria-current)
 * 
 * Other variables:
 * string $type Link type (usually 'page' or 'custom')
 * string $title  Link title (also included in $attributes)
 * int $depth  Nesting level (starting with 1)
 * bool $isOpen Flag indicating whether the link URL is the current URL
 * bool $error  Flag indicating whether the link has issues
 * 
 * To customize this snippet, copy it to your /site/snippets/ folder
 * and edit the copy.
 * 
 * For example, to add class="navigation-item navigation-item-X"
 * to each link item, where X is the depth level of the given link,
 * you can add the following line to your copy of navigation_item.php: 
 * $attributes['class']='navigation-item navigation-item-' . $depth;
 * 
 */
?>
<?php
use Kirby\Cms\Html;
echo Html::tag('a', $text ?? '', array_merge(['href' => $url], $attributes ?? []));

