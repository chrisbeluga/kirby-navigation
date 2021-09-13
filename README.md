# Kirby Navigation Field
A Navigation field for Kirby CMS [Kirby CMS](https://getkirby.com),

## Preview
![](https://github.com/chrisbeluga/kirby-navigation/blob/main/navigation-demo-1.png)
![](https://github.com/chrisbeluga/kirby-navigation/blob/main/navigation-demo-2.png)
![](https://github.com/chrisbeluga/kirby-navigation/blob/main/navigation-demo-3.png)

## Installation & Usage
Copy plugin files to your plugin's directory. Use the following blueprint  anywhere you want the navigation to appear

```
  navigation:
    label: Navigation
    type: navigation
    levels: 5
    help: Description of menu or where it is used
    width: 1/2
```

Custom fields can be added to display alongside the menu too, simple add the fields option to the blueprint like so:

```
  navigation:
    label: Navigation
    type: navigation
    levels: 5
    help: This is the main navigation of the site
    width: 1/2
    fields:
	  custom_field_1:
	    label: Custom Field 1
	    type: text
	    width: 1/2
	  custom_field_2:
	    label: Custom Field 2
	    type: textarea
	    width: 1/2
```

Included is a handy snippet to output your menu up to any level:

```
  <?php
    snippet('navigation', [
      'children' => $site->navigation()
    ]);
  ?>
```

If you would like full control of your menu and would prefer to use a foreach loop to create the menu, that could look something like this:

```
  <?php if($site->navigation()->isNotEmpty()): ?>
    <ul>
      <?php foreach($site->navigation()->toStructure() as $navigation): ?>
        <li>
          <a href="<?php echo $navigation->url(); ?>">
            <?php echo $navigation->text(); ?>
          </a>
          <?php if($navigation->children()->isNotEmpty()): ?>
            <ul>
              <?php foreach($navigation->children()->toStructure() as $children): ?>
                <a href="<?php echo $children->url(); ?>">
                  <?php echo $children->text(); ?>
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
