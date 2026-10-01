<?php
/** Dekorative Orbit-Grafik (Hub mit kreisenden Macs). @var string|null $center Icon im Zentrum */
$center = $center ?? null;
$nodes = [
    ['ring' => 1, 'icon' => 'laptop'], ['ring' => 1, 'icon' => 'chip'],
    ['ring' => 2, 'icon' => 'laptop'], ['ring' => 2, 'icon' => 'code'], ['ring' => 2, 'icon' => 'chat'],
    ['ring' => 3, 'icon' => 'laptop'], ['ring' => 3, 'icon' => 'server'], ['ring' => 3, 'icon' => 'laptop'], ['ring' => 3, 'icon' => 'memory'],
];
?>
<div class="orbit" aria-hidden="true">
<?php for ($ring = 1; $ring <= 3; $ring++): $ringNodes = array_values(array_filter($nodes, fn ($n) => $n['ring'] === $ring)); ?>
    <div class="orbit-ring orbit-ring-<?= $ring ?>">
<?php foreach ($ringNodes as $i => $node): ?>
        <span class="orbit-node orbit-pos-<?= count($ringNodes) ?>-<?= $i + 1 ?>"><span class="orbit-node-inner"><?= icon($node['icon']) ?></span></span>
<?php endforeach; ?>
    </div>
<?php endfor; ?>
    <div class="orbit-hub">
<?php if ($center): ?>
        <?= icon($center) ?>
<?php else: ?>
        <span class="orbit-hub-label">HUB</span>
<?php endif; ?>
    </div>
</div>
