<?php /** Sicherheit & Datenschutz. @var array $page */ ?>
<?= partial('pageHero', [
    'page' => $page,
    'eyebrow' => 'Sicherheit',
    'title' => 'Sicher. <em>Von Grund auf.</em>',
    'lead' => 'Dein Mac, deine Daten, deine Regeln. So schützen wir alle im Mesh.',
    'ctas' => [
        ['label' => 'Fragen stellen', 'href' => url('kontakt'), 'style' => 'primary', 'icon' => 'arrow-right'],
        ['label' => 'Datenschutzerklärung', 'href' => url('datenschutz'), 'style' => 'ghost'],
    ],
    'visual' => partial('orbit', ['center' => 'shield']),
]) ?>

<section class="section-light">
    <div class="container">
        <header class="section-head">
            <p class="eyebrow eyebrow-dark reveal">Unsere Grundsätze</p>
            <h2 class="reveal" data-split>Sechs Versprechen <em>an dich.</em></h2>
        </header>
        <div class="card-grid card-grid-3">
            <article class="feature-card reveal"><span class="feature-card-icon"><?= icon('lock') ?></span><h3>Verschlüsselt</h3><p>Alle Verbindungen im Mesh laufen verschlüsselt.</p></article>
            <article class="feature-card reveal"><span class="feature-card-icon"><?= icon('eye-off') ?></span><h3>Nichts gespeichert</h3><p>Inhalte werden auf den rechnenden Macs nicht gespeichert.</p></article>
            <article class="feature-card reveal"><span class="feature-card-icon"><?= icon('shield') ?></span><h3>Deine Dateien bleiben tabu</h3><p>Die App teilt Rechenleistung – keinen Zugriff auf deine Daten.</p></article>
            <article class="feature-card reveal"><span class="feature-card-icon"><?= icon('stop') ?></span><h3>Stop heißt Stop</h3><p>Ein Klick, und dein Mac ist sofort raus.</p></article>
            <article class="feature-card reveal"><span class="feature-card-icon"><?= icon('map-pin') ?></span><h3>Rechnet in Deutschland</h3><p>Das Mesh besteht aus Macs hierzulande.</p></article>
            <article class="feature-card reveal"><span class="feature-card-icon"><?= icon('sparkle') ?></span><h3>Transparent</h3><p>Wir erklären offen, wie das Mesh funktioniert.</p></article>
        </div>
    </div>
</section>

<section class="section-dark flow-section">
    <div class="section-glow section-glow-left" aria-hidden="true"></div>
    <div class="container">
        <header class="section-head center">
            <p class="eyebrow reveal"><?= icon('lock') ?>Geschützter Weg</p>
            <h2 class="reveal" data-split>So reist <em>deine Anfrage.</em></h2>
        </header>
        <ol class="flow flow-secure" data-flow>
            <li class="flow-step reveal"><span class="flow-icon"><?= icon('user') ?></span><strong>Du</strong><small>Deine Frage geht verschlüsselt raus.</small></li>
            <li class="flow-step reveal"><span class="flow-icon"><?= icon('server') ?></span><strong>Hub</strong><small>Leitet weiter – ohne mitzulesen.</small></li>
            <li class="flow-step reveal"><span class="flow-icon"><?= icon('laptop') ?></span><strong>Mac</strong><small>Rechnet und vergisst.</small></li>
            <li class="flow-step reveal"><span class="flow-icon"><?= icon('check') ?></span><strong>Antwort</strong><small>Kommt verschlüsselt zurück.</small></li>
        </ol>
    </div>
</section>

<section class="section-soft">
    <div class="container split">
        <div class="split-copy">
            <p class="eyebrow eyebrow-dark reveal">Auch auf dieser Website</p>
            <h2 class="reveal" data-split>Kein Cookie. <em>Kein Tracker.</em></h2>
            <p class="section-lead reveal">Wir messen nur anonym, welche Seiten gelesen werden – ohne Cookies, ohne Dienste von Dritten und ohne IP-Speicherung.</p>
            <div class="btn-row reveal"><a class="btn btn-dark btn-heart" href="<?= e(url('datenschutz')) ?>">Zur Datenschutzerklärung <?= icon('arrow-right') ?></a></div>
        </div>
        <ul class="spec-list spec-list-stack">
            <li class="reveal"><?= icon('eye-off') ?><span><strong>0 Cookies</strong>für Statistik</span></li>
            <li class="reveal"><?= icon('globe') ?><span><strong>0 Drittanbieter</strong>Schriften &amp; Skripte lokal</span></li>
            <li class="reveal"><?= icon('lock') ?><span><strong>0 IP-Adressen</strong>gespeichert</span></li>
        </ul>
    </div>
</section>

<section class="section-light faq-teaser">
    <div class="container split split-top">
        <div class="split-copy sticky-copy">
            <p class="eyebrow eyebrow-dark reveal">Fragen zur Sicherheit</p>
            <h2 class="reveal" data-split>Gut zu <em>wissen.</em></h2>
        </div>
        <div><?= partial('faqList', ['items' => faqItems('Sicherheit')]) ?></div>
    </div>
</section>

<?= partial('ctaBand', ['page' => $page, 'title' => 'Sicher dabei? <em>Gern!</em>', 'text' => 'Sichere dir deinen Platz in der Beta – wir melden uns, sobald es losgeht.']) ?>
