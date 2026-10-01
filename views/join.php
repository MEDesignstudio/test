<?php /** Mitmachen / Beta-Anmeldung. @var array $page */ ?>
<section class="page-hero join-hero section-dark has-visual">
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
            <p class="eyebrow reveal"><span class="pulse-dot" aria-hidden="true"></span>Beta-Anmeldung</p>
            <h1 class="page-title reveal" data-split>Sei von <em>Anfang an</em> dabei.</h1>
            <p class="lead reveal">Als Bereitsteller, Nutzer oder beides. Dauert zwei Minuten – versprochen.</p>
            <ul class="perk-list">
                <li class="reveal"><span class="perk-icon"><?= icon('rocket') ?></span><span><strong>Früher Zugang</strong>Teste Mesh, Chat und Plugin als Erste:r.</span></li>
                <li class="reveal"><span class="perk-icon"><?= icon('chat') ?></span><span><strong>Mitreden</strong>Dein Feedback formt GemeinsamKI.</span></li>
                <li class="reveal"><span class="perk-icon"><?= icon('heart') ?></span><span><strong>Gemeinschaft</strong>Gemeinsam KI für Deutschland bauen.</span></li>
            </ul>
        </div>
        <div class="page-hero-visual reveal reveal-scale">
            <div class="form-card" id="anmelden">
                <?= partial('joinForm', ['page' => $page]) ?>
            </div>
        </div>
    </div>
</section>

<section class="section-light">
    <div class="container">
        <header class="section-head center">
            <p class="eyebrow eyebrow-dark reveal">Für wen?</p>
            <h2 class="reveal" data-split>Für alle, die <em>KI teilen</em> wollen.</h2>
        </header>
        <div class="card-grid card-grid-3">
            <article class="feature-card feature-card-lg reveal">
                <span class="feature-card-icon"><?= icon('laptop') ?></span>
                <h3>Mac-Besitzer</h3>
                <p>Dein Mac langweilt sich nachts? Lass ihn mitrechnen.</p>
            </article>
            <article class="feature-card feature-card-lg reveal">
                <span class="feature-card-icon"><?= icon('code') ?></span>
                <h3>Entwickler:innen</h3>
                <p>Starke Modelle direkt in VS Code – aus Deutschland.</p>
            </article>
            <article class="feature-card feature-card-lg reveal">
                <span class="feature-card-icon"><?= icon('users') ?></span>
                <h3>Vereine, Schulen &amp; Firmen</h3>
                <p>Bringt eure Macs ein und nutzt gemeinsam KI.</p>
            </article>
        </div>
    </div>
</section>

<section class="section-soft faq-teaser">
    <div class="container split split-top">
        <div class="split-copy sticky-copy">
            <p class="eyebrow eyebrow-dark reveal">Noch unsicher?</p>
            <h2 class="reveal" data-split>Häufige <em>Fragen.</em></h2>
            <div class="btn-row reveal">
                <a class="btn btn-dark btn-heart" href="<?= e(url('faq')) ?>">Alle Fragen <?= icon('arrow-right') ?></a>
                <a class="btn btn-outline btn-heart" href="<?= e(url('kontakt')) ?>">Kontakt</a>
            </div>
        </div>
        <div><?= partial('faqList', ['items' => faqFeatured(5)]) ?></div>
    </div>
</section>
