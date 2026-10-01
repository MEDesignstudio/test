<?php /** Akkordeon für FAQ-Einträge. @var array $items */ ?>
<div class="faq-list">
<?php foreach ($items as $index => $item): ?>
    <details class="faq-item reveal" data-faq data-category="<?= e($item['category'] ?? '') ?>">
        <summary>
            <span class="faq-q"><?= e($item['q']) ?></span>
            <span class="faq-toggle" aria-hidden="true"><i></i><i></i></span>
        </summary>
        <div class="faq-a"><p><?= e($item['a']) ?></p></div>
    </details>
<?php endforeach; ?>
</div>
