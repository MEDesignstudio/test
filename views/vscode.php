<?php
/** VS-Code-Plugin. @var array $page */
$marketplace = (string) config('site.vscodeMarketplaceUrl', '');
$primaryCta = $marketplace !== ''
    ? ['label' => 'Im Marketplace öffnen', 'href' => $marketplace, 'style' => 'primary', 'icon' => 'external']
    : ['label' => 'Für die Beta anmelden', 'href' => url('mitmachen', ['rolle' => 'nutzen'], 'anmelden'), 'style' => 'primary', 'icon' => 'arrow-right'];
?>
<?= partial('pageHero', [
    'page' => $page,
    'eyebrow' => 'VS-Code-Plugin',
    'title' => 'Dein Editor. <em>Unser Mesh.</em>',
    'lead' => 'Vorschläge, Erklärungen und Refactoring – berechnet von Macs aus der Community.',
    'ctas' => [$primaryCta, ['label' => 'So startest du', 'href' => '#start-plugin', 'style' => 'ghost']],
    'visual' => partial('editorMock'),
]) ?>

<section class="section-light">
    <div class="container">
        <header class="section-head">
            <p class="eyebrow eyebrow-dark reveal">Funktionen</p>
            <h2 class="reveal" data-split>KI, die <em>mitprogrammiert.</em></h2>
        </header>
        <div class="card-grid">
            <article class="feature-card reveal"><span class="feature-card-icon"><?= icon('sparkle') ?></span><h3>Vervollständigung</h3><p>Vorschläge direkt im Code. Mit Tab übernehmen.</p></article>
            <article class="feature-card reveal"><span class="feature-card-icon"><?= icon('chat') ?></span><h3>Chat</h3><p>Fragen zum Code – in der Seitenleiste.</p></article>
            <article class="feature-card reveal"><span class="feature-card-icon"><?= icon('book') ?></span><h3>Erklären</h3><p>Markieren, fragen, verstehen.</p></article>
            <article class="feature-card reveal"><span class="feature-card-icon"><?= icon('refresh') ?></span><h3>Refactoring</h3><p>Aufräumen lassen – du entscheidest.</p></article>
            <article class="feature-card reveal"><span class="feature-card-icon"><?= icon('layers') ?></span><h3>Modell wählen</h3><p>Jedes Modell im Mesh steht dir offen.</p></article>
            <article class="feature-card reveal"><span class="feature-card-icon"><?= icon('map-pin') ?></span><h3>Rechnet hier</h3><p>Auf Macs in Deutschland statt in fernen Clouds.</p></article>
        </div>
    </div>
</section>

<section class="section-soft" id="start-plugin">
    <div class="container split">
        <div class="split-copy">
            <p class="eyebrow eyebrow-dark reveal">In drei Schritten</p>
            <h2 class="reveal" data-split>So <em>startest</em> du.</h2>
            <ol class="number-steps">
                <li class="reveal"><span>1</span><div><strong>Plugin installieren</strong>Im VS Code Marketplace nach „GemeinsamKI“ suchen.</div></li>
                <li class="reveal"><span>2</span><div><strong>Verbinden</strong>Mit deinem GemeinsamKI-Zugang anmelden.</div></li>
                <li class="reveal"><span>3</span><div><strong>Loslegen</strong>Tippen, Tab drücken, fertig.</div></li>
            </ol>
        </div>
        <div class="split-visual reveal reveal-scale">
            <div class="code-card">
                <div class="code-card-head"><span class="traffic"><i></i><i></i><i></i></span><span>settings.json</span><button class="code-copy" type="button" data-copy="#settingsSnippet" aria-label="Code kopieren"><?= icon('copy') ?></button></div>
<pre id="settingsSnippet"><code>{
  <span class="tk-t">"gemeinsamki.model"</span>: <span class="tk-n">"qwen/qwen3.6-35b-a3b"</span>,
  <span class="tk-t">"gemeinsamki.inlineSuggestions"</span>: <span class="tk-k">true</span>,
  <span class="tk-t">"gemeinsamki.language"</span>: <span class="tk-n">"de"</span>
}</code></pre>
                <p class="code-card-note">Beispielkonfiguration – die finalen Einstellungen folgen zum Start.</p>
            </div>
        </div>
    </div>
</section>

<?= partial('ctaBand', ['page' => $page, 'title' => 'Code mit dem <em>ganzen Mesh.</em>', 'text' => 'Melde dich für die Beta an und gehör zu den Ersten, die das Plugin testen.']) ?>
