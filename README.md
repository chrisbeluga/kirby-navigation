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

A simple example of a two level nested menu could go something like this:

```
<?php if($site->navigation_field()->isNotEmpty()): ?>
	<ul>
	<?php foreach($site->navigation_field()->toStructure() as $navigation): ?>
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
