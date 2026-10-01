<?php /** Mac-Check: Was kann dein Mac zum Mesh beitragen? (Berechnung in main.js) */ ?>
<div class="mac-check" data-mac-check data-chips="<?= e(json_encode(MAC_CHIPS)) ?>" data-ram="<?= e(json_encode(MAC_RAM)) ?>" data-join="<?= e(url('mitmachen')) ?>">
    <div class="mac-check-controls">
        <div class="control-block">
            <p class="field-label"><?= icon('chip') ?>Chip-Generation</p>
            <div class="segmented" role="radiogroup" aria-label="Chip-Generation" data-gen-group>
<?php foreach (['M1', 'M2', 'M3', 'M4', 'M5'] as $gen): ?>
                <button type="button" role="radio" aria-checked="<?= $gen === 'M4' ? 'true' : 'false' ?>" class="<?= $gen === 'M4' ? 'is-active' : '' ?>" data-gen="<?= $gen ?>"><?= $gen ?></button>
<?php endforeach; ?>
            </div>
        </div>
        <div class="control-block">
            <p class="field-label"><?= icon('layers') ?>Variante</p>
            <div class="segmented" role="radiogroup" aria-label="Variante" data-variant-group>
<?php foreach (['' => 'Basis', 'Pro' => 'Pro', 'Max' => 'Max', 'Ultra' => 'Ultra'] as $value => $label): ?>
                <button type="button" role="radio" aria-checked="<?= $value === 'Pro' ? 'true' : 'false' ?>" class="<?= $value === 'Pro' ? 'is-active' : '' ?>" data-variant="<?= e($value) ?>"><?= e($label) ?></button>
<?php endforeach; ?>
            </div>
        </div>
        <div class="control-block">
            <label class="field-label" for="macCheckRam"><?= icon('memory') ?>Arbeitsspeicher <output class="range-output" data-ram-output>48 GB</output></label>
            <input class="range" id="macCheckRam" type="range" min="0" max="<?= count(MAC_RAM) - 1 ?>" step="1" value="6" data-ram-input>
            <div class="range-scale" aria-hidden="true"><span>8 GB</span><span>64 GB</span><span>512 GB</span></div>
        </div>
    </div>

    <div class="mac-check-result" aria-live="polite">
        <div class="level-head">
            <span class="level-badge" data-level-badge><?= icon('bolt') ?><span data-level-name>Kraftwerk</span></span>
            <span class="level-step">Level <span data-level-num>3</span>/5</span>
        </div>
        <p class="result-big"><span class="result-prefix">bis ca.</span> <strong data-max-model>55</strong> <span>Mrd. Parameter</span></p>
        <p class="result-sub">So große Modelle kann dein Mac im Mesh ausführen <small>(4-Bit, grobe Schätzung)</small>.</p>
        <ul class="result-facts">
            <li><?= icon('gauge') ?><span><strong data-gpu>bis 20</strong> GPU-Kerne</span></li>
            <li><?= icon('sparkle') ?><span>z. B. <strong data-examples>Qwen3 32B, Gemma 3 27B</strong></span></li>
        </ul>
        <ol class="level-track" aria-hidden="true">
            <li>Funke</li><li>Knoten</li><li>Kraftwerk</li><li>Turbine</li><li>Supernova</li>
        </ol>
        <a class="btn btn-primary btn-heart magnetic" href="<?= e(url('mitmachen')) ?>" data-join-link>Mit diesem Mac mitmachen <?= icon('arrow-right') ?></a>
    </div>
</div>
