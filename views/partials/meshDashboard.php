<?php
/** Live-Vorschau des Mesh-Dashboards (Simulation mit Beispieldaten, gesteuert über main.js). */
$devices = [
    ['id' => 'air', 'name' => 'MacBook Air M4', 'kind' => 'MacBook Air', 'chip' => 'Apple M4', 'ram' => 24, 'gpu' => 10, 'cpu' => 10, 'model' => 'openai/gpt-oss-20b', 'type' => 'LLM', 'quant' => 'MXFP4 · 128k', 'requests' => 71, 'online' => true],
    ['id' => 'max', 'name' => 'MacBook Pro M5 Max', 'kind' => 'MacBook Pro', 'chip' => 'Apple M5 Max', 'ram' => 64, 'gpu' => 40, 'cpu' => 18, 'model' => 'qwen/qwen3.6-35b-a3b', 'type' => 'VLM', 'quant' => '4bit · 256k', 'requests' => 128, 'online' => true],
    ['id' => 'studio', 'name' => 'Mac Studio M2 Ultra', 'kind' => 'Mac Studio', 'chip' => 'Apple M2 Ultra', 'ram' => 192, 'gpu' => 76, 'cpu' => 24, 'model' => 'openai/gpt-oss-120b', 'type' => 'LLM', 'quant' => 'MXFP4 · 128k', 'requests' => 342, 'online' => true],
    ['id' => 'mini', 'name' => 'Mac mini M4', 'kind' => 'Mac mini', 'chip' => 'Apple M4', 'ram' => 16, 'gpu' => 10, 'cpu' => 10, 'model' => 'google/gemma-3-12b', 'type' => 'LLM', 'quant' => 'Q4_K_M · 32k', 'requests' => 12, 'online' => false],
];
?>
<div class="dash" data-dashboard>
    <div class="dash-top">
        <span class="dash-brand"><?= img('brand.mark', ['class' => 'dash-mark', 'alt' => '']) ?><strong>GemeinsamKI</strong><small>Mesh</small></span>
        <span class="dash-tabs" aria-hidden="true"><b>Mesh</b><span>Chat</span><span>Verwaltung</span></span>
        <span class="dash-live"><span class="pulse-dot" aria-hidden="true"></span>Live-Vorschau · Beispieldaten</span>
    </div>

    <div class="dash-stats">
        <div class="dash-stat"><strong data-stat="devices">3</strong><small>Geräte online</small></div>
        <div class="dash-stat"><strong data-stat="models">3</strong><small>Modelle geladen</small></div>
        <div class="dash-stat"><strong data-stat="gpu">126</strong><small>GPU-Kerne im Mesh</small></div>
        <div class="dash-stat"><strong data-stat="requests">0</strong><small>Anfragen aktiv</small></div>
        <div class="dash-stat"><strong data-stat="ram">280</strong><small>GB RAM im Mesh</small></div>
    </div>

    <div class="dash-grid">
        <div class="dash-panel dash-devices">
            <div class="dash-panel-head"><span>Geräte</span><small data-stat-label>3 online · 4 bekannt</small></div>
            <ul class="dash-device-list">
<?php foreach ($devices as $device): ?>
                <li class="dash-device<?= $device['online'] ? ' is-online' : '' ?>" data-device="<?= e($device['id']) ?>" data-name="<?= e($device['name']) ?>" data-ram="<?= (int) $device['ram'] ?>" data-gpu="<?= (int) $device['gpu'] ?>" data-model="<?= e($device['model']) ?>" data-type="<?= e($device['type']) ?>">
                    <div class="dash-device-head">
                        <span class="identicon" data-identicon="<?= e($device['name']) ?>" aria-hidden="true"></span>
                        <span class="dash-device-name"><?= icon('laptop') ?><strong><?= e($device['name']) ?></strong><small><?= e($device['kind']) ?> · macOS 26</small></span>
                        <span class="dash-badge" data-device-state><?= $device['online'] ? 'online' : 'offline' ?></span>
                    </div>
                    <div class="dash-device-specs">
                        <span><strong><?= e($device['chip']) ?></strong><small>Chip</small></span>
                        <span><strong><?= (int) $device['ram'] ?> GB</strong><small>RAM</small><i class="dash-bar"><b data-device-load></b></i></span>
                        <span><strong><?= (int) $device['gpu'] ?> <em>/ <?= (int) $device['cpu'] ?></em></strong><small>GPU / CPU</small></span>
                    </div>
                    <div class="dash-device-model">
                        <span class="status-dot" aria-hidden="true"></span><code><?= e($device['model']) ?></code>
                        <span class="dash-tag"><?= e($device['type']) ?></span><small><?= e($device['quant']) ?></small>
                    </div>
                    <div class="dash-device-foot"><small>LM Studio · 1 Modell geladen</small><small><span data-device-requests><?= (int) $device['requests'] ?></span> Anfragen</small></div>
                </li>
<?php endforeach; ?>
            </ul>
        </div>

        <div class="dash-map" data-dash-map>
            <svg class="dash-map-svg" viewBox="0 0 600 460" preserveAspectRatio="xMidYMid meet" aria-hidden="true">
                <defs>
                    <radialGradient id="hubGlow" cx="50%" cy="50%" r="50%">
                        <stop offset="0%" stop-color="#8F88FF" stop-opacity=".95"></stop>
                        <stop offset="55%" stop-color="#685EFF" stop-opacity=".35"></stop>
                        <stop offset="100%" stop-color="#685EFF" stop-opacity="0"></stop>
                    </radialGradient>
                </defs>
                <g class="dash-orbits">
                    <circle cx="300" cy="230" r="70"></circle>
                    <circle cx="300" cy="230" r="110"></circle>
                    <circle cx="300" cy="230" r="150"></circle>
                </g>
                <g data-map-links></g>
                <g data-map-packets></g>
                <circle class="hub-glow" cx="300" cy="230" r="110" fill="url(#hubGlow)"></circle>
                <circle class="hub-core" cx="300" cy="230" r="38"></circle>
                <text class="hub-label" x="300" y="235">HUB</text>
                <g data-map-nodes></g>
            </svg>
            <p class="dash-toast" data-dash-toast aria-live="polite"></p>
        </div>

        <div class="dash-panel dash-models">
            <div class="dash-panel-head"><span>Modelle im Mesh</span><small><span data-stat="models2">3</span> geladen</small></div>
            <ul class="dash-model-list" data-model-list>
<?php foreach ($devices as $device): ?>
                <li class="dash-model<?= $device['online'] ? ' is-online' : '' ?>" data-model-of="<?= e($device['id']) ?>">
                    <span class="dash-model-type"><?= e($device['type']) ?></span>
                    <span class="dash-model-text"><code><?= e($device['model']) ?></code><small>geladen auf <strong><?= e($device['name']) ?></strong></small></span>
                    <a class="dash-model-chat" href="<?= e(url('chat', ['modell' => $device['model']])) ?>" tabindex="-1">Chat <?= icon('arrow-right') ?></a>
                </li>
<?php endforeach; ?>
            </ul>
        </div>
    </div>
</div>
