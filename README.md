# Kirby Navigation Field
A Navigation field for Kirby CMS [Kirby CMS](https://getkirby.com),

## Preview
![](https://github.com/chrisbeluga/kirby-navigation/blob/main/navigation-demo-1.gif)

## Installation & Usage
Copy plugin files to your plugin's directory. Use the following blueprint anywhere you want the navigation to appear:

```
  navigation:
    label: Navigation
    type: navigation
    levels: 5
    help: Description of menu or where it is used
    width: 1/2
```

Included is a handy snippet to output your menu up to any level, this can be copied and customised to however you want the menu to appear:

```
  <?php
    snippet('navigation', [
      'children' => $site->navigation()
    ]);
  ?>
```

if using the site as a headless CMS or would like to consume your menu in JS you can use the following field method to return as an array of menu items:

```
  site()->navigation()->toNavigationMenu()
```

If you would like full control of your menu and would prefer to use a foreach loop to create the menu, that could look something like this:

```
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
