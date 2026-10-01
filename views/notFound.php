<?php /** 404 – Seite nicht gefunden. @var array $page */ ?>
<section class="notfound section-dark">
    <canvas class="mesh-canvas" data-mesh="soft" aria-hidden="true"></canvas>
    <div class="hero-glow" aria-hidden="true"></div>
    <div class="container notfound-inner">
        <p class="notfound-code" aria-hidden="true"><span>4</span><span class="notfound-node"><span class="identicon" data-identicon="offline-404"></span></span><span>4</span></p>
        <p class="eyebrow"><span class="pulse-dot pulse-dot-off" aria-hidden="true"></span>Status: offline</p>
        <h1 class="page-title">Dieser Knoten ist <em>offline.</em></h1>
        <p class="lead">Die Seite gibt es nicht (mehr). Aber das Mesh ist groß – hier geht's weiter:</p>
        <div class="btn-row center">
            <a class="btn btn-primary btn-heart magnetic" href="<?= e(url()) ?>"><?= icon('home') ?> Zur Startseite</a>
            <a class="btn btn-ghost btn-heart" href="<?= e(url('kontakt')) ?>">Fehler melden</a>
        </div>
        <ul class="notfound-links">
<?php foreach (['so-funktionierts', 'mac-app', 'chat', 'mitmachen', 'faq'] as $route): ?>
            <li><a class="link-arrow" href="<?= e(url($route)) ?>"><?= e(ROUTES[$route]['nav']) ?> <?= icon('arrow-right') ?></a></li>
<?php endforeach; ?>
        </ul>
    </div>
</section>
