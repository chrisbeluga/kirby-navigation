<ul>
	<?php foreach($children->toStructure() as $child): ?>
		<li>
			<a
				href="<?php echo $child->url(); ?>"
				title="<?php echo $child->title()->html() ?>"
				<?php echo $child->popup()->toBool() ? 'target="_blank"' : '' ?>>
				<?php echo $child->text()->html() ?>
			</a>
			<?php if($child->children()->isNotEmpty()): ?>
				<?php
					snippet('navigation', [
						'children' => $child->children()
					]);
				?>
			<?php endif ?>
		</li>
	<?php endforeach ?>
</ul>
