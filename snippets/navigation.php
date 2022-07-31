<?php
/** @var \Kirby\Cms\Field $children */
?>
<ul>
    <?php foreach ($children->toStructure() as $child): ?>
        <li>
            <a href="<?php echo $child->url(); ?>"
               title="<?php echo $child->title()->html() ?>"
               target="<?php e($child->popup()->toBool(), '_blank', '_self') ?>"
               <?php e($child->url()->value() === kirby()->url('current'), ' aria-current') ?>
            >
                <?php echo $child->text()->html() ?>
            </a>
            <?php if ($child->children()->isNotEmpty()): ?>
                <?php snippet('navigation', ['children' => $child->children()]); ?>
            <?php endif ?>
        </li>
    <?php endforeach; ?>
</ul>
