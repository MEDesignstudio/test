<?php
/** So funktioniert's. @var array $page */
$days = ['Mo', 'Di', 'Mi', 'Do', 'Fr', 'Sa', 'So'];
?>
<?= partial('pageHero', [
    'page' => $page,
    'eyebrow' => "So funktioniert's",
    'title' => 'Drei Schritte. <em>Ein Netz.</em>',
    'lead' => 'Vom Download bis zur ersten KI-Antwort in wenigen Minuten. Hier siehst du, was dabei passiert.',
    'ctas' => [
        ['label' => 'Jetzt mitmachen', 'href' => url('mitmachen'), 'style' => 'primary', 'icon' => 'arrow-right'],
        ['label' => 'Zur Mac-App', 'href' => url('mac-app'), 'style' => 'ghost'],
    ],
    'visual' => partial('orbit'),
]) ?>

<section class="section-light">
    <div class="container">
        <div class="story">
            <article class="story-row" id="schritt-1">
                <div class="story-copy">
                    <span class="story-num reveal">01</span>
                    <h2 class="reveal" data-split>Anschließen</h2>
                    <p class="section-lead reveal">App laden, anmelden, fertig. Die Einrichtung führt dich Schritt für Schritt.</p>
                    <ul class="check-list reveal">
                        <li><?= icon('check') ?>Für Macs mit Apple Silicon (M1–M5)</li>
                        <li><?= icon('check') ?>Geführte Einrichtung</li>
                        <li><?= icon('check') ?>Kein Terminal nötig</li>
                    </ul>
                </div>
                <div class="story-visual reveal reveal-scale">
                    <div class="install-card">
                        <div class="install-ring" data-install-ring>
                            <svg viewBox="0 0 120 120" aria-hidden="true"><circle class="install-track" cx="60" cy="60" r="50"></circle><circle class="install-progress" cx="60" cy="60" r="50" pathLength="100"></circle></svg>
                            <span class="install-icon"><?= img('brand.mark', ['alt' => '']) ?></span>
                        </div>
                        <strong>GemeinsamKI wird eingerichtet</strong>
                        <ol class="install-steps">
                            <li><?= icon('check') ?>App installiert</li>
                            <li><?= icon('check') ?>Mac erkannt: M4 Pro · 48 GB</li>
                            <li><?= icon('check') ?>Mit dem Mesh verbunden</li>
                        </ol>
                    </div>
                </div>
            </article>

            <article class="story-row" id="schritt-2">
                <div class="story-copy">
                    <span class="story-num reveal">02</span>
                    <h2 class="reveal" data-split>Freigeben</h2>
                    <p class="section-lead reveal">Drück Play – oder lass den Zeitplan entscheiden. Dein Mac teilt nur, wann du willst.</p>
                    <ul class="check-list reveal">
                        <li><?= icon('check') ?>Play &amp; Stop jederzeit</li>
                        <li><?= icon('check') ?>Zeitplan, z. B. nur nachts</li>
                        <li><?= icon('check') ?>Speicher-Limit selbst festlegen</li>
                    </ul>
                </div>
                <div class="story-visual reveal reveal-scale">
                    <div class="week-plan" data-week-plan>
                        <div class="week-plan-head"><strong><?= icon('calendar') ?>Dein Wochenplan</strong><span class="week-legend"><i></i>teilt</span></div>
                        <div class="week-grid">
<?php foreach ($days as $d => $day): ?>
                            <span class="week-day"><?= $day ?></span>
<?php for ($h = 0; $h < 24; $h++): $active = ($h >= 22 || $h < 7) || ($d >= 5 && $h >= 10 && $h < 18); ?>
                            <span class="week-cell<?= $active ? ' is-on' : '' ?>"></span>
<?php endfor; ?>
<?php endforeach; ?>
                        </div>
                        <div class="week-scale"><span>0 Uhr</span><span>6</span><span>12</span><span>18</span><span>24</span></div>
                    </div>
                </div>
            </article>

            <article class="story-row" id="schritt-3">
                <div class="story-copy">
                    <span class="story-num reveal">03</span>
                    <h2 class="reveal" data-split>Mitnutzen</h2>
                    <p class="section-lead reveal">Nutze alle Modelle im Mesh – im Chat oder direkt in VS Code.</p>
                    <ul class="check-list reveal">
                        <li><?= icon('check') ?>Chat im Browser</li>
                        <li><?= icon('check') ?>Plugin für VS Code</li>
                        <li><?= icon('check') ?>Modell frei wählen</li>
                    </ul>
                </div>
                <div class="story-visual reveal reveal-scale"><?= partial('chatMini') ?></div>
            </article>
        </div>
    </div>
</section>

<section class="section-dark flow-section">
    <div class="section-glow section-glow-left" aria-hidden="true"></div>
    <div class="container">
        <header class="section-head center">
            <p class="eyebrow reveal"><?= icon('activity') ?>Hinter den Kulissen</p>
            <h2 class="reveal" data-split>Was passiert bei <em>einer Anfrage?</em></h2>
        </header>
        <ol class="flow" data-flow>
            <li class="flow-step reveal"><span class="flow-icon"><?= icon('chat') ?></span><strong>Du fragst</strong><small>Im Chat oder in VS Code.</small></li>
            <li class="flow-step reveal"><span class="flow-icon"><?= icon('mesh') ?></span><strong>Der Hub verteilt</strong><small>Er findet einen freien Mac mit passendem Modell.</small></li>
            <li class="flow-step reveal"><span class="flow-icon"><?= icon('laptop') ?></span><strong>Ein Mac rechnet</strong><small>Lokal, auf Apple Silicon.</small></li>
            <li class="flow-step reveal"><span class="flow-icon"><?= icon('sparkle') ?></span><strong>Antwort kommt</strong><small>Verschlüsselt, direkt zu dir.</small></li>
        </ol>
    </div>
</section>

<section class="section-soft">
    <div class="container">
        <header class="section-head">
            <p class="eyebrow eyebrow-dark reveal">Kurz erklärt</p>
            <h2 class="reveal" data-split>Die wichtigsten <em>Begriffe.</em></h2>
        </header>
        <div class="card-grid card-grid-5">
            <article class="feature-card reveal"><span class="feature-card-icon"><?= icon('mesh') ?></span><h3>Mesh</h3><p>Das Netz aus allen verbundenen Macs.</p></article>
            <article class="feature-card reveal"><span class="feature-card-icon"><?= icon('server') ?></span><h3>Hub</h3><p>Die Zentrale, die Anfragen verteilt.</p></article>
            <article class="feature-card reveal"><span class="feature-card-icon"><?= icon('laptop') ?></span><h3>Knoten</h3><p>Ein Mac, der gerade Leistung teilt.</p></article>
            <article class="feature-card reveal"><span class="feature-card-icon"><?= icon('layers') ?></span><h3>Modell</h3><p>Die KI, die auf einem Mac läuft – z. B. GPT-OSS.</p></article>
            <article class="feature-card reveal"><span class="feature-card-icon"><?= icon('clock') ?></span><h3>Zeitplan</h3><p>Wann dein Mac teilen darf.</p></article>
        </div>
    </div>
</section>

<?= partial('ctaBand', ['page' => $page, 'title' => 'Klingt einfach? <em>Ist es auch.</em>', 'text' => 'Sichere dir jetzt deinen Platz in der Beta – wir melden uns, sobald es losgeht.']) ?>
