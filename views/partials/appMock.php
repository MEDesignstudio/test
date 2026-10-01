<?php
/** Interaktives Mockup der Mac-App (Play/Stop, Zeitplan, Freigabe-Limit). */
$schedules = [
    'immer' => ['label' => 'Immer', 'icon' => 'bolt', 'from' => 0, 'to' => 24, 'text' => 'Rund um die Uhr'],
    'nachts' => ['label' => 'Nachts', 'icon' => 'moon', 'from' => 22, 'to' => 7, 'text' => 'Täglich 22–7 Uhr'],
    'feierabend' => ['label' => 'Feierabend', 'icon' => 'sun', 'from' => 18, 'to' => 8, 'text' => 'Werktags ab 18 Uhr'],
    'eigene' => ['label' => 'Eigene', 'icon' => 'sliders', 'from' => 1, 'to' => 6, 'text' => 'Täglich 1–6 Uhr'],
];
?>
<div class="app-mock" id="appMock" data-app-mock>
    <div class="app-window">
        <div class="app-titlebar">
            <span class="traffic" aria-hidden="true"><i></i><i></i><i></i></span>
            <span class="app-title"><?= img('brand.mark', ['class' => 'app-title-mark', 'alt' => '']) ?>GemeinsamKI</span>
            <span class="app-version">Beta</span>
        </div>

        <div class="app-body">
            <div class="app-status">
                <div class="app-device">
                    <span class="app-device-icon"><?= icon('laptop') ?></span>
                    <span>
                        <strong>Mein MacBook Pro</strong>
                        <small>Apple M4 Pro · 48 GB</small>
                    </span>
                </div>
                <span class="status-pill" data-app-status>
                    <span class="status-dot" aria-hidden="true"></span><span data-app-status-text>Pausiert</span>
                </span>
            </div>

            <div class="app-control">
                <button class="power-button" type="button" data-app-toggle aria-pressed="false" aria-label="Freigabe starten">
                    <span class="power-ripple" aria-hidden="true"></span>
                    <span class="power-ripple power-ripple-2" aria-hidden="true"></span>
                    <span class="power-icon power-play"><?= icon('play') ?></span>
                    <span class="power-icon power-stop"><?= icon('stop') ?></span>
                </button>
                <div class="app-control-copy">
                    <strong data-app-headline>Bereit zum Teilen</strong>
                    <small data-app-sub>Drück Play – dein Mac tritt dem Mesh bei.</small>
                </div>
            </div>

            <div class="app-metrics">
                <div><small>Anfragen beantwortet</small><strong data-app-requests>0</strong></div>
                <div><small>Geteilter Speicher</small><strong><span data-app-ram-out>24</span> GB</strong></div>
                <div><small>Modell</small><strong class="mono">gpt-oss-20b</strong></div>
            </div>

            <div class="app-section">
                <div class="app-section-head">
                    <span><?= icon('clock') ?>Zeitplan</span>
                    <small data-app-schedule-text><?= e($schedules['nachts']['text']) ?></small>
                </div>
                <div class="schedule-chips" role="radiogroup" aria-label="Zeitplan wählen">
<?php foreach ($schedules as $key => $schedule): ?>
                    <button type="button" class="chip<?= $key === 'nachts' ? ' is-active' : '' ?>" role="radio" aria-checked="<?= $key === 'nachts' ? 'true' : 'false' ?>" data-schedule="<?= e($key) ?>" data-from="<?= (int) $schedule['from'] ?>" data-to="<?= (int) $schedule['to'] ?>" data-text="<?= e($schedule['text']) ?>">
                        <?= icon($schedule['icon']) ?><?= e($schedule['label']) ?>
                    </button>
<?php endforeach; ?>
                </div>
            </div>

            <div class="app-section">
                <label class="app-section-head" for="appRam">
                    <span><?= icon('memory') ?>Speicher freigeben</span>
                    <small><span data-app-ram>24</span> von 48 GB</small>
                </label>
                <input class="range" id="appRam" type="range" min="8" max="40" step="4" value="24" data-app-ram-input>
            </div>
        </div>
    </div>

    <div class="clock-card" aria-hidden="true">
        <svg class="clock-ring" viewBox="0 0 120 120">
            <circle class="clock-track" cx="60" cy="60" r="48"></circle>
            <circle class="clock-arc" cx="60" cy="60" r="48" pathLength="1000" data-clock-arc></circle>
<?php for ($h = 0; $h < 24; $h++): $angle = deg2rad($h * 15 - 90); ?>
            <circle class="clock-tick<?= $h % 6 === 0 ? ' is-major' : '' ?>" cx="<?= round(60 + cos($angle) * 58, 2) ?>" cy="<?= round(60 + sin($angle) * 58, 2) ?>" r="<?= $h % 6 === 0 ? 1.8 : 1 ?>"></circle>
<?php endfor; ?>
            <line class="clock-hand" x1="60" y1="60" x2="60" y2="22" data-clock-hand></line>
            <circle class="clock-center" cx="60" cy="60" r="3.5"></circle>
        </svg>
        <div class="clock-labels"><span>0</span><span>6</span><span>12</span><span>18</span></div>
        <p class="clock-caption"><?= icon('moon') ?><span data-clock-caption>Teilt 22–7 Uhr</span></p>
    </div>
</div>
