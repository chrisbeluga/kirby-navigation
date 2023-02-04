<?php
/** @var \Kirby\Cms\Field $children */
?>

<ul>
    <?php foreach ($children->toStructure() as $child): ?>
        <?php
            $page = page($child->uuid());
            $url = $page ? $page->url() : $child->url();
            $target = $child->popup()->toBool() ? '_blank' : '_self';
            $isOpen = $page ? $page->isActive() : false;
        ?>
        <li>
            <?php if($child->clickable()->toBool() === true): ?>
                <a href="<?= $url ?>"
                title="<?= $child->text()->value() ?>"
                target="<?= $target ?>"
                <?= $isOpen ? ' aria-current="page"' : '' ?>
                >
                    <?= $child->text()->value() ?>
                </a>
            <?php else: ?>
                <span>
                    <?= $child->text()->value() ?>
                </span>
            <?php endif; ?>
            <?php if ($child->children()->isNotEmpty()): ?>
                <?php snippet('navigation', ['children' => $child->children()]); ?>
            <?php endif ?>
        </li>
    <?php endforeach; ?>
</ul>
