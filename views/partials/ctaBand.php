<?php
/**
 * Abschluss-CTA mit Schnellanmeldung.
 * @var array $page @var string $title @var string $text
 */
$count = waitlistCount();
?>
<section class="cta-band section-dark" id="dabei-sein">
    <canvas class="mesh-canvas mesh-canvas-soft" data-mesh="soft" aria-hidden="true"></canvas>
    <div class="cta-orb" aria-hidden="true"></div>
    <div class="container cta-inner">
        <p class="eyebrow reveal"><span class="pulse-dot" aria-hidden="true"></span>Beta-Plätze verfügbar</p>
        <h2 class="cta-title reveal" data-split><?= $title ?></h2>
        <p class="lead reveal"><?= e($text) ?></p>
        <div class="reveal"><?= partial('quickJoin', ['page' => $page]) ?></div>
        <p class="cta-foot reveal">
<?php if ($count !== null): ?>
            <span class="cta-count"><?= icon('users') ?>Schon <strong><?= formatNumber($count) ?></strong> Menschen dabei</span>
<?php endif; ?>
            <a class="link-arrow" href="<?= e(url('unterstuetzen')) ?>">Oder das Projekt unterstützen <?= icon('arrow-right') ?></a>
        </p>
    </div>
</section>
