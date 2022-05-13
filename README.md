# Kirby Navigation Field
A Navigation field for Kirby CMS [Kirby CMS](https://getkirby.com),

## Preview
![](https://github.com/chrisbeluga/kirby-navigation/blob/main/navigation-demo-1.gif)

## Installation & Usage
**Note:** For Kirby 3.6+ use the [`v2`](https://github.com/chrisbeluga/kirby-navigation/tree/v2) branch.

Copy plugin files to your plugin's directory. Use the following blueprint anywhere you want the navigation to appear:

## Usage
Add the following blueprint to wherever you would like the navigation field to appear.

```yaml
navigation:
  label: Navigation
  type: navigation
  levels: 5
  help: Description of menu or where it is used
  width: 1/2
```

Two Field methods are included which will output the menu regardless of how many levels deep you go:

```php
<?php echo $site->navigation()->toNavigationMarkup(); ?>
```

If using the site as a headless CMS or would like to consume your menu in JS you can use the following field method to return an array of menu items:

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

If you would like full control of your menu and would prefer to use a foreach to create the menu, that could look something like this:

```php
  <?php if($site->navigation()->isNotEmpty()): ?>
    <ul>
      <?php foreach($site->navigation()->toStructure() as $navigation): ?>
        <li>
          <a href="<?php echo $navigation->url(); ?>">
            <?php echo $navigation->text() ?>
          </a>
          <?php if($navigation->children()->isNotEmpty()): ?>
            <ul>
              <?php foreach($navigation->children()->toStructure() as $children): ?>
                <a href="<?php echo $children->url() ?>">
                  <?php echo $children->text() ?>
                </a>
              <?php endforeach ?>
            </ul>
          <?php endif ?>
        </li>
      <?php endforeach ?>
    </ul>
  <?php endif ?>
```

## Info
Nesting limit is set as default to 5, to allow further levels adjust the levels option in the blueprint

## Contributing
Pull requests are welcome. For major changes, please open an issue first to discuss what you would like to change.

## License
[MIT](https://choosealicense.com/licenses/mit/)
