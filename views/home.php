<?php
/** Startseite. @var array $page */
$railSections = [
    'start' => 'Start', 'idee' => 'Die Idee', 'so-gehts' => 'In drei Schritten', 'app' => 'Die Mac-App',
    'mac-check' => 'Mac-Check', 'mesh' => 'Live-Vorschau', 'nutzen' => 'Mitnutzen', 'warum' => 'Warum', 'fragen' => 'Fragen', 'dabei-sein' => 'Mitmachen',
];
?>
<nav class="mesh-rail" aria-label="Abschnitte der Startseite" data-mesh-rail>
    <ol>
<?php foreach ($railSections as $id => $label): ?>
        <li><a href="#<?= e($id) ?>" data-rail="<?= e($id) ?>"><span class="rail-node" aria-hidden="true"></span><span class="rail-label"><?= e($label) ?></span></a></li>
<?php endforeach; ?>
    </ol>
    <span class="rail-progress" aria-hidden="true"><i data-rail-fill></i></span>
</nav>

<!-- HERO ================================================================= -->
<section class="hero section-dark" id="start">
    <canvas class="mesh-canvas" data-mesh="hero" aria-hidden="true"></canvas>
    <div class="hero-glow" aria-hidden="true"></div>
    <div class="container hero-inner">
        <div class="hero-copy">
            <p class="eyebrow reveal"><span class="pulse-dot" aria-hidden="true"></span>Beta startet bald · Made in Germany</p>
            <h1 class="hero-title">
                <span class="hero-line"><span>Dein Mac.</span></span>
                <span class="hero-line"><span class="text-gradient">Unser KI-Netz.</span></span>
            </h1>
            <p class="lead reveal">Verbinde deinen Mac mit dem GemeinsamKI-Mesh, gib freie Rechenleistung frei – und nutze dafür starke KI-Modelle. Im Chat oder direkt in VS Code.</p>
            <div class="btn-row reveal">
                <a class="btn btn-primary btn-lg btn-heart magnetic" href="<?= e(url('mitmachen')) ?>"><span class="btn-dot" aria-hidden="true"></span>Mac anschließen</a>
                <a class="btn btn-ghost btn-lg btn-heart" href="#so-gehts"><span class="btn-play" aria-hidden="true"><?= icon('play') ?></span>So funktioniert's</a>
            </div>
            <ol class="hero-steps reveal" aria-label="<?= e(config('site.tagline')) ?>" data-hero-steps>
                <li><?= icon('download') ?>Anschließen</li>
                <li><?= icon('play') ?>Freigeben</li>
                <li><?= icon('sparkle') ?>Mitnutzen</li>
            </ol>
        </div>

        <div class="hero-visual" aria-hidden="true">
            <div class="float-card fc-join">
                <span class="identicon identicon-sm" data-identicon="MacBook Air M4"></span>
                <span><strong>MacBook Air M4</strong><small><span class="status-dot"></span>24 GB · online</small></span>
            </div>
            <div class="float-card fc-request">
                <span class="fc-icon"><?= icon('bolt') ?></span>
                <span><strong>Anfrage beantwortet</strong><small>Mac Studio · 0,6 s</small></span>
            </div>
            <div class="float-card fc-night">
                <span class="fc-icon fc-icon-play"><?= icon('moon') ?></span>
                <span><strong>Teilt nachts</strong><small>22–7 Uhr · automatisch</small></span>
            </div>
        </div>
    </div>
    <a class="scroll-cue" href="#idee"><span class="sr-only">Weiter zur Idee</span><span class="scroll-mouse" aria-hidden="true"><i></i></span></a>
</section>

<!-- DIE IDEE ============================================================= -->
<section class="statement section-light" id="idee">
    <div class="container">
        <p class="eyebrow eyebrow-dark reveal">Die Idee</p>
        <p class="statement-text" data-scrub-words data-keys="Rechenzentrum,KI,uns,allen,gehört">Millionen Macs in Deutschland langweilen sich die meiste Zeit. Zusammen wären sie ein riesiges Rechenzentrum. Wir verbinden sie – zu einer KI, die uns allen gehört.</p>
        <div class="fact-row">
            <div class="fact reveal"><strong><span data-count="0">0</span></strong><span>neue Rechenzentren nötig</span></div>
            <div class="fact reveal"><strong><span data-count="100">100</span>&nbsp;%</strong><span>Rechenleistung aus Deutschland</span></div>
            <div class="fact reveal"><strong><span data-count="1">1</span>&nbsp;Klick</strong><span>zum Mitmachen</span></div>
        </div>
    </div>
</section>

<!-- DREI SCHRITTE ======================================================== -->
<section class="steps section-soft" id="so-gehts">
    <div class="container">
        <header class="section-head center">
            <p class="eyebrow eyebrow-dark reveal">In drei Schritten</p>
            <h2 class="reveal" data-split>Anschließen. Freigeben. <em>Mitnutzen.</em></h2>
            <p class="section-lead reveal">Kein Technik-Studium nötig. In wenigen Minuten ist dein Mac Teil des Mesh.</p>
        </header>

        <div class="steps-track" data-steps-track>
            <svg class="steps-line" viewBox="0 0 1000 120" preserveAspectRatio="none" aria-hidden="true">
                <path class="steps-line-bg" d="M40 60 C 220 0, 300 120, 500 60 S 780 0, 960 60"></path>
                <path class="steps-line-fg" d="M40 60 C 220 0, 300 120, 500 60 S 780 0, 960 60" pathLength="100" data-steps-path></path>
            </svg>

            <article class="step-card reveal" id="schritt-1">
                <div class="step-visual step-visual-download" aria-hidden="true">
                    <span class="mini-laptop"><span class="mini-screen"><span class="mini-bar"><i></i></span></span></span>
                </div>
                <span class="step-num">01</span>
                <h3>Anschließen</h3>
                <p>App laden, Mac verbinden. In zwei Minuten erledigt.</p>
            </article>

            <article class="step-card reveal" id="schritt-2">
                <div class="step-visual step-visual-play" aria-hidden="true">
                    <span class="mini-play"><?= icon('play') ?></span>
                </div>
                <span class="step-num">02</span>
                <h3>Freigeben</h3>
                <p>Play drücken – oder Zeitplan setzen. Zum Beispiel nur nachts.</p>
            </article>

            <article class="step-card reveal" id="schritt-3">
                <div class="step-visual step-visual-use" aria-hidden="true">
                    <span class="mini-chip mini-chip-a"><?= icon('chat') ?>Chat</span>
                    <span class="mini-chip mini-chip-b"><?= icon('code') ?>VS Code</span>
                </div>
                <span class="step-num">03</span>
                <h3>Mitnutzen</h3>
                <p>Alle Modelle im Mesh nutzen. Im Chat oder in deinem Editor.</p>
            </article>
        </div>

        <p class="center reveal"><a class="link-arrow link-arrow-dark" href="<?= e(url('so-funktionierts')) ?>">Genauer erklärt <?= icon('arrow-right') ?></a></p>
    </div>
</section>

<!-- MAC-APP ============================================================== -->
<section class="app-showcase section-dark" id="app">
    <div class="section-glow section-glow-left" aria-hidden="true"></div>
    <div class="container split">
        <div class="split-copy">
            <p class="eyebrow reveal"><?= icon('laptop') ?>Die Mac-App</p>
            <h2 class="reveal" data-split>Ein Klick – und dein Mac <em>denkt mit.</em></h2>
            <p class="section-lead reveal">Play startet die Freigabe, Stop beendet sie. Oder du legst fest, wann dein Mac teilt – etwa nachts, wenn du schläfst.</p>
            <ul class="feature-list">
                <li class="reveal"><span class="feature-icon"><?= icon('play') ?></span><span><strong>Play &amp; Stop</strong>Sofort an, sofort aus.</span></li>
                <li class="reveal"><span class="feature-icon"><?= icon('clock') ?></span><span><strong>Zeitplan</strong>Nachts, nach Feierabend oder wann du willst.</span></li>
                <li class="reveal"><span class="feature-icon"><?= icon('sliders') ?></span><span><strong>Deine Grenzen</strong>Du bestimmst, wie viel Speicher geteilt wird.</span></li>
            </ul>
            <p class="reveal"><a class="link-arrow" href="<?= e(url('mac-app')) ?>">Alles zur Mac-App <?= icon('arrow-right') ?></a></p>
        </div>
        <div class="split-visual reveal reveal-scale">
            <p class="try-hint"><?= icon('sparkle') ?>Probier's aus: Drück Play!</p>
            <?= partial('appMock') ?>
        </div>
    </div>
</section>

<!-- MAC-CHECK ============================================================ -->
<section class="mac-check-section section-light" id="mac-check">
    <div class="container">
        <header class="section-head center">
            <p class="eyebrow eyebrow-dark reveal">Mac-Check</p>
            <h2 class="reveal" data-split>Was steckt in <em>deinem</em> Mac?</h2>
            <p class="section-lead reveal">Chip und Speicher wählen – wir zeigen dir, was dein Mac im Mesh bewegen kann.</p>
        </header>
        <div class="reveal"><?= partial('macCheck') ?></div>
    </div>
</section>

<!-- LIVE-VORSCHAU ======================================================== -->
<section class="dash-section section-dark" id="mesh">
    <div class="section-glow section-glow-right" aria-hidden="true"></div>
    <div class="container">
        <header class="section-head center">
            <p class="eyebrow reveal"><span class="pulse-dot" aria-hidden="true"></span>Live-Vorschau</p>
            <h2 class="reveal" data-split>So sieht das <em>Mesh</em> aus.</h2>
            <p class="section-lead reveal">Alle Geräte, alle Modelle, alle Anfragen – auf einen Blick. Hier mit Beispieldaten.</p>
        </header>
        <div class="reveal reveal-scale"><?= partial('meshDashboard') ?></div>
    </div>
</section>

<!-- MITNUTZEN ============================================================ -->
<section class="use-section section-light" id="nutzen">
    <div class="container">
        <header class="section-head">
            <p class="eyebrow eyebrow-dark reveal">Mitnutzen</p>
            <h2 class="reveal" data-split>Das ganze Mesh. <em>Da, wo du arbeitest.</em></h2>
        </header>
        <div class="use-grid">
            <article class="use-card tilt reveal">
                <div class="use-card-head">
                    <span class="use-icon"><?= icon('chat') ?></span>
                    <div>
                        <h3>Chat</h3>
                        <p>Frag das Mesh im Browser – bald auf chat.gemeinsamki.de.</p>
                    </div>
                </div>
                <?= partial('chatMini') ?>
                <a class="link-arrow link-arrow-dark" href="<?= e(url('chat')) ?>">Chat ausprobieren <?= icon('arrow-right') ?></a>
            </article>
            <article class="use-card tilt reveal">
                <div class="use-card-head">
                    <span class="use-icon"><?= icon('code') ?></span>
                    <div>
                        <h3>VS-Code-Plugin</h3>
                        <p>KI-Vorschläge direkt im Editor – von Macs aus der Community.</p>
                    </div>
                </div>
                <?= partial('editorMock') ?>
                <a class="link-arrow link-arrow-dark" href="<?= e(url('vscode-plugin')) ?>">Zum Plugin <?= icon('arrow-right') ?></a>
            </article>
        </div>
    </div>
</section>

<!-- WERTE ================================================================ -->
<section class="values section-soft" id="warum">
    <div class="container">
        <header class="section-head center">
            <p class="eyebrow eyebrow-dark reveal">Warum GemeinsamKI</p>
            <h2 class="reveal" data-split>KI, die <em>bei uns</em> bleibt.</h2>
        </header>
        <div class="value-grid">
            <article class="value-card reveal">
                <span class="value-icon"><?= icon('map-pin') ?></span>
                <h3>Rechnet in Deutschland</h3>
                <p>Anfragen laufen auf Macs hier – nicht in fremden Clouds.</p>
            </article>
            <article class="value-card reveal">
                <span class="value-icon"><?= icon('sliders') ?></span>
                <h3>Du hast die Kontrolle</h3>
                <p>Play, Stop, Zeitplan. Jederzeit, mit einem Klick.</p>
            </article>
            <article class="value-card reveal">
                <span class="value-icon"><?= icon('leaf') ?></span>
                <h3>Nutzt, was schon da ist</h3>
                <p>Freie Leistung statt neuer Rechenzentren.</p>
            </article>
            <article class="value-card reveal">
                <span class="value-icon"><?= icon('heart') ?></span>
                <h3>Gehört uns allen</h3>
                <p>Gemeinschaftlich betrieben, offen für alle.</p>
            </article>
        </div>
    </div>
</section>

<!-- FAQ ================================================================== -->
<section class="faq-teaser section-light" id="fragen">
    <div class="container split split-top">
        <div class="split-copy sticky-copy">
            <p class="eyebrow eyebrow-dark reveal">Fragen?</p>
            <h2 class="reveal" data-split>Kurz <em>gefragt.</em></h2>
            <p class="section-lead reveal">Die wichtigsten Antworten auf einen Blick. Alles Weitere findest du im FAQ.</p>
            <div class="btn-row reveal">
                <a class="btn btn-dark btn-heart" href="<?= e(url('faq')) ?>">Alle Fragen <?= icon('arrow-right') ?></a>
                <a class="btn btn-outline btn-heart" href="<?= e(url('kontakt')) ?>">Frag uns direkt</a>
            </div>
        </div>
        <div><?= partial('faqList', ['items' => faqFeatured(5)]) ?></div>
    </div>
</section>

<?= partial('ctaBand', ['page' => $page, 'title' => 'Bereit, deinen Mac <em>zu teilen?</em>', 'text' => 'Sichere dir einen Platz in der Beta. Wir melden uns, sobald es losgeht – ohne Spam, versprochen.']) ?>
