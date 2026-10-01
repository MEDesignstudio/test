<?php
/** Mission. @var array $page */
$org = config('organization', []);
$roadmap = [
    ['state' => 'aktiv', 'label' => 'Jetzt', 'title' => 'Beta mit ersten Macs', 'text' => 'Wir testen das Mesh gemeinsam mit den ersten Bereitsteller:innen.'],
    ['state' => 'next', 'label' => 'Als Nächstes', 'title' => 'Chat für alle', 'text' => 'chat.gemeinsamki.de öffnet für alle Teilnehmenden.'],
    ['state' => 'geplant', 'label' => 'Danach', 'title' => 'VS-Code-Plugin', 'text' => 'Das Mesh direkt im Editor – für alle Entwickler:innen.'],
    ['state' => 'geplant', 'label' => 'Vision', 'title' => 'Offen für alle', 'text' => 'Vereine, Schulen und Unternehmen bringen ihre Macs ein.'],
];
?>
<?= partial('pageHero', [
    'page' => $page,
    'eyebrow' => 'Mission',
    'title' => 'KI in Deutschland <em>bündeln.</em>',
    'lead' => 'Die wichtigste Technologie unserer Zeit sollte nicht nur wenigen gehören.',
    'ctas' => [
        ['label' => 'Mitmachen', 'href' => url('mitmachen'), 'style' => 'primary', 'icon' => 'arrow-right'],
        ['label' => 'Unterstützen', 'href' => url('unterstuetzen'), 'style' => 'ghost'],
    ],
    'visual' => partial('orbit', ['center' => 'heart']),
]) ?>

<section class="statement section-light">
    <div class="container">
        <p class="eyebrow eyebrow-dark reveal">Warum wir das machen</p>
        <p class="statement-text" data-scrub-words data-keys="bündeln,KI,hier,uns,allen,gehört">Überall stehen starke Macs, die die meiste Zeit nichts tun. Gleichzeitig wandern unsere Daten in ferne Rechenzentren. Wir drehen das um: Wir bündeln, was schon da ist – zu einer KI, die hier läuft und uns allen gehört.</p>
    </div>
</section>

<section class="section-soft">
    <div class="container">
        <header class="section-head">
            <p class="eyebrow eyebrow-dark reveal">Unsere Grundsätze</p>
            <h2 class="reveal" data-split>Woran wir <em>glauben.</em></h2>
        </header>
        <div class="principles">
            <article class="principle reveal"><span class="principle-num">01</span><h3>Gemeinsam statt einsam</h3><p>Viele kleine Beiträge ergeben etwas Großes.</p></article>
            <article class="principle reveal"><span class="principle-num">02</span><h3>Hier statt irgendwo</h3><p>KI, die in Deutschland rechnet.</p></article>
            <article class="principle reveal"><span class="principle-num">03</span><h3>Freiwillig statt Pflicht</h3><p>Du entscheidest, wann und wie viel du teilst.</p></article>
            <article class="principle reveal"><span class="principle-num">04</span><h3>Vorhanden statt neu</h3><p>Freie Leistung nutzen, statt neu zu bauen.</p></article>
            <article class="principle reveal"><span class="principle-num">05</span><h3>Offen statt verschlossen</h3><p>Transparent in Technik und Entscheidungen.</p></article>
            <article class="principle reveal"><span class="principle-num">06</span><h3>Menschen statt Konzerne</h3><p>Ein Netz, das uns allen gehört.</p></article>
        </div>
    </div>
</section>

<section class="section-dark">
    <div class="section-glow section-glow-right" aria-hidden="true"></div>
    <div class="container">
        <header class="section-head center">
            <p class="eyebrow reveal"><?= icon('rocket') ?>Fahrplan</p>
            <h2 class="reveal" data-split>Schritt für Schritt <em>zum Mesh.</em></h2>
        </header>
        <ol class="timeline" data-timeline>
<?php foreach ($roadmap as $item): ?>
            <li class="timeline-item is-<?= e($item['state']) ?> reveal">
                <span class="timeline-node" aria-hidden="true"></span>
                <small><?= e($item['label']) ?></small>
                <h3><?= e($item['title']) ?></h3>
                <p><?= e($item['text']) ?></p>
            </li>
<?php endforeach; ?>
        </ol>
    </div>
</section>

<section class="section-light">
    <div class="container split">
        <div class="split-copy">
            <p class="eyebrow eyebrow-dark reveal">Wer dahintersteht</p>
            <h2 class="reveal" data-split>Eine Initiative <em>aus Deutschland.</em></h2>
            <p class="section-lead reveal">Hinter <?= e($org['name'] ?? 'GemeinsamKI') ?> stehen Menschen, die KI gemeinsam gestalten wollen. Lust mitzumachen? Wir suchen Entwickler:innen, Gestalter:innen und Botschafter:innen.</p>
            <div class="btn-row reveal">
                <a class="btn btn-dark btn-heart" href="<?= e(url('kontakt')) ?>">Mitgestalten <?= icon('arrow-right') ?></a>
            </div>
        </div>
        <div class="split-visual reveal reveal-scale">
            <div class="org-card">
                <?= img('brand.logo', ['class' => 'org-card-logo']) ?>
                <dl>
<?php if (!isPlaceholder($org['legalName'] ?? '')): ?>
                    <div><dt>Träger</dt><dd><?= e($org['legalName']) ?></dd></div>
<?php endif; ?>
                    <div><dt>Sitz</dt><dd><?= e(implode(', ', array_filter(orgAddressLines(), fn ($l) => !isPlaceholder($l))) ?: 'Deutschland') ?></dd></div>
                    <div><dt>Gegründet</dt><dd><?= e($org['foundingDate'] ?? '') ?></dd></div>
                    <div><dt>Domains</dt><dd class="mono">gemeinsamki.de · gemeinsamki.org</dd></div>
                </dl>
            </div>
        </div>
    </div>
</section>

<?= partial('ctaBand', ['page' => $page, 'title' => 'Bündeln wir <em>gemeinsam.</em>', 'text' => 'Jeder Mac, jede Stimme, jede Idee zählt. Sichere dir deinen Platz.']) ?>
