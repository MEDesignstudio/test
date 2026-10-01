<?php
/** Chat mit dem Mesh (Demo-Modus, bis mesh.chat.enabled aktiv ist). @var array $page */
$live = (bool) config('mesh.chat.enabled', false);
$models = (array) config('mesh.demoModels', []);
$selected = (string) ($_GET['modell'] ?? config('mesh.chat.defaultModel', ''));
$chatUrl = (string) config('site.chatUrl', '');
$suggestions = ['Was ist GemeinsamKI?', 'Welche Macs können mitmachen?', 'Wie sicher sind meine Daten?', 'Kann mein Mac nur nachts teilen?'];
?>
<section class="page-hero chat-hero section-dark has-visual">
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
            <p class="eyebrow reveal"><span class="pulse-dot" aria-hidden="true"></span><?= $live ? 'Live im Mesh' : 'Vorschau · Demo-Modus' ?></p>
            <h1 class="page-title reveal" data-split>Frag das <em>Mesh.</em></h1>
            <p class="lead reveal">Jede Antwort kommt von einem Mac aus der Community. Bald auch unter <strong class="mono"><?= e(preg_replace('#^https?://#', '', $chatUrl)) ?></strong>.</p>
            <ul class="chat-facts reveal">
                <li><?= icon('layers') ?>Modell frei wählen</li>
                <li><?= icon('map-pin') ?>Rechnet in Deutschland</li>
                <li><?= icon('eye-off') ?>Keine Werbung</li>
            </ul>
        </div>

        <div class="page-hero-visual reveal reveal-scale">
            <div class="chat-app" id="chatApp" data-chat-app data-live="<?= $live ? '1' : '0' ?>" data-token="<?= e(formToken()) ?>">
                <div class="chat-app-head">
                    <span class="chat-app-brand"><?= img('brand.mark', ['alt' => '', 'class' => 'chat-app-mark']) ?><span><strong>GemeinsamKI Chat</strong><small><span class="status-dot"></span><?= $live ? 'verbunden' : 'Demo-Modus' ?></small></span></span>
                    <label class="sr-only" for="chatModel">Modell</label>
                    <select class="chat-model" id="chatModel" data-chat-model>
<?php foreach ($models as $model): ?>
                        <option value="<?= e($model['id']) ?>"<?= $model['id'] === $selected ? ' selected' : '' ?>><?= e($model['label'] ?? $model['id']) ?></option>
<?php endforeach; ?>
                    </select>
                </div>
                <div class="chat-app-log" data-chat-log aria-live="polite">
                    <div class="chat-msg chat-msg-ai">
                        <span class="chat-avatar"><?= img('brand.mark', ['alt' => '']) ?></span>
                        <div class="chat-bubble">Hallo! Schön, dass du da bist. <?= $live ? 'Was möchtest du wissen?' : 'Ich bin die Demo des Mesh-Chats – frag mich alles rund um GemeinsamKI.' ?></div>
                    </div>
                </div>
                <div class="chat-suggestions" data-chat-suggestions>
<?php foreach ($suggestions as $suggestion): ?>
                    <button type="button" class="chip" data-suggestion><?= e($suggestion) ?></button>
<?php endforeach; ?>
                </div>
                <form class="chat-app-input" data-chat-form>
                    <label class="sr-only" for="chatInput">Deine Nachricht</label>
                    <textarea id="chatInput" rows="1" maxlength="2000" placeholder="Schreib eine Nachricht …" data-chat-input></textarea>
                    <button class="chat-send" type="submit" aria-label="Senden"><?= icon('send') ?></button>
                </form>
                <p class="chat-app-note"><?= icon('shield') ?>Bitte keine sensiblen Daten eingeben.<?= $live ? '' : ' Im Demo-Modus antworten wir mit Infos aus unseren FAQ.' ?></p>
            </div>
        </div>
    </div>
</section>

<section class="section-light">
    <div class="container">
        <header class="section-head center">
            <p class="eyebrow eyebrow-dark reveal">Bald für alle</p>
            <h2 class="reveal" data-split>Ein Chat, der <em>uns gehört.</em></h2>
        </header>
        <div class="card-grid card-grid-3">
            <article class="feature-card reveal"><span class="feature-card-icon"><?= icon('layers') ?></span><h3>Freie Modellwahl</h3><p>Jedes Modell, das im Mesh geladen ist.</p></article>
            <article class="feature-card reveal"><span class="feature-card-icon"><?= icon('users') ?></span><h3>Von der Community</h3><p>Betrieben von Macs aus ganz Deutschland.</p></article>
            <article class="feature-card reveal"><span class="feature-card-icon"><?= icon('lock') ?></span><h3>Datensparsam</h3><p>Verschlüsselt übertragen, nicht gespeichert.</p></article>
        </div>
    </div>
</section>

<?= partial('ctaBand', ['page' => $page, 'title' => 'Mehr Macs. <em>Mehr KI.</em>', 'text' => 'Jeder Mac im Mesh macht den Chat schneller und vielfältiger. Sei dabei!']) ?>
