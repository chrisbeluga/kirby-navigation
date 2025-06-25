# Kirby Navigation Field

A Navigation field for [Kirby CMS](https://getkirby.com).

## Preview

![](https://github.com/chrisbeluga/kirby-navigation/blob/main/navigation-demo-1.gif)

## Installation & Usage

Copy plugin files to your plugin's directory or install via composer with `composer require belugadigital/kirby-navigation`
Note that this Composer package name (belugadigital/kirby-navigation) differs from the GitHub repository URL (chrisbeluga/kirby-navigation).

## Kirby compatibility table

| Kirby version | Compatible plugin version |
|:--------------|:--------------------------|
| ^5.0          | ^5.0                      |
| ^4.8          | ^4.2                      |
| ^4.0          | ^4.0                      |
| ^3.7          | ^3.0                      |
| ^3.6          | ^2.0                      |
| ^3.5          | ^1.0                      |

## Usage

Add the following blueprint to wherever you would like the navigation field to appear:

```yaml
fields:
  navigation:
    label: Navigation
    type: navigation
    levels: 10
    help: Description of menu or where it is used
    width: 1/2
    multilang: false
```

Or use the following minimalist blueprint without extra options:

```yaml
fields:
  navigation:
    label: Navigation
    type: navigation
```

The following example shows how you can output a menu from a template file, regardless of how many levels deep the menu is. This example assumes that the "site" blueprint contains a navigation field called "navigation":

```php
<?php echo $site->navigation()->toNavigationMarkup(); ?>
```

If using the site as a headless CMS or would like to consume your menu in JS you can use the following field method to return a nested array of menu items:

```php
<?php $site->navigation()->toNavigationArray(); ?>
 ```

Or when using Kirby Query language

```json
{
  "query": "site",
  "select": {
    "title": "site.title",
    "navigation": "site.navigation.toNavigationArray"
  }
}
```

If you want full control over your menu and want to customize the markup, you can copy the navigation.php and navigation_item.php files from the plugin's snippets directory to your /site/snippets directory, and customize them there.
This is the recommended way of markup customization.
For example, to add class="navigation-item navigation-item-X" to each link item, where X is the depth level of the given link, you can add the following line to your copy of navigation_item.php: 

```php
$attributes['class']='navigation-item navigation-item-' . $depth;
```

If you prefer to use a foreach to create the menu, or if you are upgrading from an older version of this plugin, the foreach loop could look something like this:

```php
  <?php if ($items=$site->navigation()->toNavigationStructure()): ?>
    <ul>
      <?php foreach($items as $item): ?>
        <li>
          <a href="<?php echo $item->url(); ?>" <?php e($item->isOpen()->value(), 'aria-current="page"') ?> <?php e($item->isChildOpen()->value(), 'class="active"') ?>>
            <?php echo Str::esc($item->text(), 'html') ?>
          </a>
          <?php if($item->children()->isNotEmpty()): ?>
            <ul>
              <?php foreach($item->children()->toStructure() as $child): ?>
                <li>
                  <a href="<?php echo $child->url() ?>" <?php e($child->isOpen()->value(), 'aria-current="page"') ?>>
                    <?php echo Str::esc($child->text(), 'html') ?>
                  </a>
                </li>
              <?php endforeach ?>
            </ul>
          <?php endif ?>
        </li>
      <?php endforeach ?>
    </ul>
  <?php endif ?>
```

As you can see, $item->isOpen()->value() can be used in this foreach() to check whether the given menu item is the current page, and $item->isChildOpen()->value() can be used to check whether any of the child menu items is the current page.

## Nesting limit

Nesting limit is set to 10 by default. To adjust the maximum number of levels, use the "levels" option in the blueprint.

## Multi-language support

The plugin supports multiple languages in two ways.
- In "normal" mode, the navigation field functions like any other content field: you need to add the necessary links to the field for each language, and set the link text and link title for each language (translating). For example, if you have 3 languages and a navigation field with 5 links, you will need to add the 5 links 3 times.
- In "multilang" mode, add the "multilang: true" option to the field blueprint. This allows you to add the necessary links only once for all languages, and edit the link text and link title separately for each language. For example, if you have 3 languages and a navigation field with 5 links, you will only need to add the 5 links once, and they will be shared across all languages. You can still translate the link text and link title, if needed.

This means that if you simply add 5 Kirby pages to the field in multilang mode and don't edit the links, the link text will be displayed in the language of the current page, and will link to the corresponding page URL in that language.

The plugin allows you to add "Kirby Pages" and "Custom Links" to the navigation field. For "Kirby Pages", the page title will be the default value of the link text, if no custom link text is entered. This means that if you simply add 5 Kirby pages to the field in multilang mode and don't edit the links, you will see the language-specific page title and page URL in the generated markup.

## What's new in version 4.0?

Changes worth mentioning:
- It works with Kirby 4
- New feature: Multi-language support, as described earlier
- New feature: The plugin now uses permanent page IDs to identify pages, instead of using the 'id' (slug) as identification. This allows pages to be renamed or moved without breaking the page links.
- Data: the structure of the field content has been changed to support permanent page IDs and multiple languages. However, the 4.0 version of the plugin is backwards compatible with the field content saved by the 3.7 version of the plugin.
- UI: The "edit" button has been moved out of the options dropdown menus.
- UI: Better icons are now used for editing the links.
- UI: The 'id' and 'url' values of 'Kirby page' links are no longer editable.
- Markup: the current language and the actual values of the page title and page URL are taken into account when generating the markup for the field in the template.
- Markup: link text and link attributes are properly escaped to prevent potential issues

## What's new in version 4.1?

New features:
- The 'class' and 'target' values of links are now editable
- The 'anchor' values of 'Kirby page' links are now editable
- The 'title' textfield can be hidden, if you do not need it
- The 'popup' toggle can be hidden, if you do not need it

Different sites have different needs, so the editable fields are configurable via /site/config/config.php.

Here are the available options that you can use in your config.php, and their default values:

```php
return [
  'chrisbeluga.navigation.edit_title' => TRUE,
  'chrisbeluga.navigation.edit_popup' => TRUE,
  'chrisbeluga.navigation.edit_target' => FALSE,
  'chrisbeluga.navigation.edit_class' => FALSE,
  'chrisbeluga.navigation.edit_anchor' => FALSE,
];
```

For example, if you want to customize the 'target' value of your links, then set 'chrisbeluga.navigation.edit_target' to TRUE. This will replace the simple 'Popup' toggle with a 'Target' textfield, allowing you to set a link target, such as '_parent' or '_top'.

If you want to add an anchor value to your 'Kirby page' links, for example to have an URL such as /en/contact#locations, set 'chrisbeluga.navigation.edit_anchor' to TRUE. You can enter 'locations' as anchor, and '#locations' will be appended to the page URL of the link.

If you use the recommended way to output the navigation markup from your template (such as $site->navigation()->toNavigationMarkup() in case of a field called 'navigation'), then any target, class and anchor values will be included automatically in the generated markup.

## What's new in version 5.0?

Works with Kirby 5.0

## Contributing

Pull requests are welcome. For major changes, please open an issue first to discuss what you would like to change.

## License

[MIT](https://choosealicense.com/licenses/mit/)
