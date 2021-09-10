<ul>
	<?php foreach($children->toStructure() as $child): ?>
		<li>
			<a
				href="<?php echo $child->url()->isNotEmpty() ? $child->url()->value() : ''; ?>"
				<?php echo $child->popup()->toBool() ? 'target="_blank"' : '' ?>
				<?php echo $child->title()->isNotEmpty() ? 'title="'.$child->title()->html().'"' : '' ?>>
				<?php if($child->text()->isNotEmpty()): ?>
					<?php echo $child->text()->html() ?>
				<?php endif ?>
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
