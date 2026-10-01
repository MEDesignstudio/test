<?php
/**
 * Seitenkopf für Unterseiten.
 * @var array $page
 * @var string $eyebrow
 * @var string $title   (darf <em> enthalten – nur statischer Text aus Views)
 * @var string $lead
 * @var array  $ctas    [['label'=>..,'href'=>..,'style'=>'primary|ghost','icon'=>..]]
 * @var string $visual  optionales HTML für die rechte Seite
 */
$ctas = $ctas ?? [];
$visual = $visual ?? '';
?>
<section class="page-hero section-dark<?= $visual ? ' has-visual' : '' ?>">
    <canvas class="mesh-canvas mesh-canvas-soft" data-mesh="soft" aria-hidden="true"></canvas>
    <div class="hero-glow" aria-hidden="true"></div>
    <div class="container page-hero-inner">
        <div class="page-hero-copy">
            <nav class="breadcrumb reveal" aria-label="Brotkrümelnavigation">
                <ol>
                    <li><a href="<?= e(url()) ?>"><?= icon('home') ?><span class="sr-only">Start</span></a></li>
                    <li aria-current="page"><?= e($page['nav']) ?></li>
                </ol>
            </nav>
            <p class="eyebrow reveal"><span class="pulse-dot" aria-hidden="true"></span><?= e($eyebrow) ?></p>
            <h1 class="page-title reveal" data-split><?= $title ?></h1>
            <p class="lead reveal"><?= e($lead) ?></p>
<?php if ($ctas): ?>
            <div class="btn-row reveal">
<?php foreach ($ctas as $cta): ?>
                <a class="btn btn-<?= e($cta['style'] ?? 'primary') ?> btn-heart<?= ($cta['style'] ?? 'primary') === 'primary' ? ' magnetic' : '' ?>" href="<?= e($cta['href']) ?>">
                    <?= e($cta['label']) ?><?= !empty($cta['icon']) ? icon($cta['icon']) : '' ?>
                </a>
<?php endforeach; ?>
            </div>
<?php endif; ?>
        </div>
<?php if ($visual): ?>
        <div class="page-hero-visual reveal reveal-scale"><?= $visual ?></div>
<?php endif; ?>
    </div>
</section>
