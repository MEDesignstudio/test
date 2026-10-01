<?php
/** Mac-App. @var array $page */
$downloadUrl = (string) config('site.downloadUrl', '');
$primaryCta = $downloadUrl !== ''
    ? ['label' => 'Für macOS laden', 'href' => $downloadUrl, 'style' => 'primary', 'icon' => 'download']
    : ['label' => 'Beta-Zugang sichern', 'href' => url('mitmachen', ['rolle' => 'bereitstellen'], 'anmelden'), 'style' => 'primary', 'icon' => 'arrow-right'];
?>
<?= partial('pageHero', [
    'page' => $page,
    'eyebrow' => 'Die Mac-App',
    'title' => 'Play. Stop. <em>Fertig.</em>',
    'lead' => 'Die GemeinsamKI-App macht deinen Mac mit einem Klick zum Teil des Mesh – und hört genauso schnell wieder auf.',
    'ctas' => [$primaryCta, ['label' => 'Voraussetzungen', 'href' => '#voraussetzungen', 'style' => 'ghost']],
    'visual' => partial('appMock'),
]) ?>

<section class="section-light">
    <div class="container">
        <header class="section-head">
            <p class="eyebrow eyebrow-dark reveal">Funktionen</p>
            <h2 class="reveal" data-split>Alles, was du brauchst. <em>Nichts, was stört.</em></h2>
        </header>
        <div class="bento">
            <article class="bento-card bento-wide bento-dark reveal">
                <span class="feature-card-icon"><?= icon('play') ?></span>
                <h3>Play &amp; Stop</h3>
                <p>Ein Klick startet die Freigabe, einer beendet sie. Sofort.</p>
                <div class="bento-power" aria-hidden="true"><span class="mini-play"><?= icon('play') ?></span><span class="bento-wave"><i></i><i></i><i></i><i></i><i></i><i></i><i></i><i></i></span></div>
            </article>
            <article class="bento-card reveal">
                <span class="feature-card-icon"><?= icon('clock') ?></span>
                <h3>Zeitplan</h3>
                <p>Nachts, nach Feierabend, am Wochenende – oder eigene Zeiten.</p>
            </article>
            <article class="bento-card reveal">
                <span class="feature-card-icon"><?= icon('sliders') ?></span>
                <h3>Deine Grenzen</h3>
                <p>Du legst fest, wie viel Speicher dein Mac teilen darf.</p>
            </article>
            <article class="bento-card reveal">
                <span class="feature-card-icon"><?= icon('eye') ?></span>
                <h3>Menüleiste</h3>
                <p>Status immer im Blick – ganz oben rechts.</p>
            </article>
            <article class="bento-card bento-wide bento-brand reveal">
                <span class="feature-card-icon"><?= icon('star') ?></span>
                <h3>Dein Beitrag</h3>
                <p>Sieh, wie viele Anfragen dein Mac schon beantwortet hat.</p>
                <div class="bento-counter" aria-hidden="true"><strong data-count="1284">1284</strong><small>Anfragen diesen Monat (Beispiel)</small></div>
            </article>
            <article class="bento-card reveal">
                <span class="feature-card-icon"><?= icon('layers') ?></span>
                <h3>Bewährte Technik</h3>
                <p>Modelle laufen lokal über LM Studio.</p>
            </article>
        </div>
    </div>
</section>

<section class="section-soft" id="voraussetzungen">
    <div class="container">
        <header class="section-head center">
            <p class="eyebrow eyebrow-dark reveal">Voraussetzungen &amp; Mac-Check</p>
            <h2 class="reveal" data-split>Passt dein Mac? <em>Finde es heraus.</em></h2>
        </header>
        <ul class="spec-list">
            <li class="reveal"><?= icon('chip') ?><span><strong>Apple Silicon</strong>M1 bis M5</span></li>
            <li class="reveal"><?= icon('memory') ?><span><strong>Ab 16 GB</strong>empfohlen</span></li>
            <li class="reveal"><?= icon('laptop') ?><span><strong>Aktuelles macOS</strong>mit Updates</span></li>
            <li class="reveal"><?= icon('bolt') ?><span><strong>Netzbetrieb</strong>empfohlen</span></li>
        </ul>
        <div class="reveal"><?= partial('macCheck') ?></div>
    </div>
</section>

<section class="section-light faq-teaser">
    <div class="container split split-top">
        <div class="split-copy sticky-copy">
            <p class="eyebrow eyebrow-dark reveal">Fragen zur App</p>
            <h2 class="reveal" data-split>Gut zu <em>wissen.</em></h2>
            <div class="btn-row reveal"><a class="btn btn-dark btn-heart" href="<?= e(url('faq')) ?>">Alle Fragen <?= icon('arrow-right') ?></a></div>
        </div>
        <div><?= partial('faqList', ['items' => faqItems('Mac & App')]) ?></div>
    </div>
</section>

<?= partial('ctaBand', ['page' => $page, 'title' => 'Bereit für <em>Play?</em>', 'text' => 'Sichere dir einen Platz in der Beta und gehör zu den ersten Macs im Mesh.']) ?>
