<?php
/** Mehrstufige Beta-Anmeldung. @var array $page */
$preChip = array_key_exists((string) ($_GET['chip'] ?? ''), MAC_CHIPS) ? (string) $_GET['chip'] : '';
$preRam = in_array((int) ($_GET['ram'] ?? 0), MAC_RAM, true) ? (int) $_GET['ram'] : 0;
$preRole = array_key_exists((string) ($_GET['rolle'] ?? ''), JOIN_ROLES) ? (string) $_GET['rolle'] : 'beides';
?>
<form class="form form-steps js-ajax-form" id="joinForm" action="<?= e(url('controller.php')) ?>" method="post" novalidate data-steps>
    <?= formHiddenFields('register', $page['route']) ?>
    <?= formNotice('register') ?>

    <ol class="step-progress" aria-hidden="true">
        <li class="is-active"><span class="step-node">1</span><small>Rolle</small></li>
        <li><span class="step-node">2</span><small>Dein Mac</small></li>
        <li><span class="step-node">3</span><small>Kontakt</small></li>
    </ol>

    <fieldset class="form-step is-active" data-step="1">
        <legend class="form-step-title">Wie möchtest du mitmachen?</legend>
        <div class="choice-cards">
<?php foreach (JOIN_ROLES as $key => $role): ?>
            <label class="choice-card">
                <input type="radio" name="role" value="<?= e($key) ?>"<?= $key === $preRole ? ' checked' : '' ?> required>
                <span class="choice-card-inner">
                    <span class="choice-icon"><?= icon($role['icon']) ?></span>
                    <strong><?= e($role['label']) ?></strong>
                    <small><?= e($role['desc']) ?></small>
                    <span class="choice-check"><?= icon('check') ?></span>
                </span>
            </label>
<?php endforeach; ?>
        </div>
        <div class="form-nav">
            <span></span>
            <button class="btn btn-primary btn-heart" type="button" data-step-next>Weiter <?= icon('arrow-right') ?></button>
        </div>
    </fieldset>

    <fieldset class="form-step" data-step="2">
        <legend class="form-step-title">Erzähl uns von deinem Mac</legend>
        <p class="form-hint">Optional – hilft uns, das Mesh zu planen.</p>
        <div class="form-grid">
            <div class="field">
                <label for="joinChip">Chip</label>
                <select id="joinChip" name="chip">
                    <option value="">Bitte wählen</option>
<?php foreach (array_keys(MAC_CHIPS) as $chip): ?>
                    <option value="<?= e($chip) ?>"<?= $chip === $preChip ? ' selected' : '' ?>>Apple <?= e($chip) ?></option>
<?php endforeach; ?>
                    <option value="intel">Intel-Mac</option>
                    <option value="keiner">Ich habe keinen Mac</option>
                </select>
            </div>
            <div class="field">
                <label for="joinRam">Arbeitsspeicher</label>
                <select id="joinRam" name="ram">
                    <option value="">Bitte wählen</option>
<?php foreach (MAC_RAM as $ram): ?>
                    <option value="<?= $ram ?>"<?= $ram === $preRam ? ' selected' : '' ?>><?= $ram ?> GB</option>
<?php endforeach; ?>
                </select>
            </div>
        </div>
        <p class="field-label">Wann darf dein Mac rechnen?</p>
        <div class="chip-group">
<?php foreach (JOIN_SCHEDULES as $key => $label): ?>
            <label class="chip chip-input"><input type="radio" name="schedule" value="<?= e($key) ?>"<?= $key === 'nachts' ? ' checked' : '' ?>><span><?= e($label) ?></span></label>
<?php endforeach; ?>
        </div>
        <p class="field-label">Wie möchtest du das Mesh nutzen?</p>
        <div class="chip-group">
            <label class="chip chip-input"><input type="checkbox" name="usage[]" value="chat" checked><span><?= icon('chat') ?>Chat</span></label>
            <label class="chip chip-input"><input type="checkbox" name="usage[]" value="vscode"><span><?= icon('code') ?>VS Code</span></label>
            <label class="chip chip-input"><input type="checkbox" name="usage[]" value="api"><span><?= icon('terminal') ?>API</span></label>
        </div>
        <div class="form-nav">
            <button class="btn btn-ghost" type="button" data-step-prev>Zurück</button>
            <button class="btn btn-primary btn-heart" type="button" data-step-next>Weiter <?= icon('arrow-right') ?></button>
        </div>
    </fieldset>

    <fieldset class="form-step" data-step="3">
        <legend class="form-step-title">Wohin dürfen wir dir schreiben?</legend>
        <div class="form-grid">
            <div class="field">
                <label for="joinName">Vorname</label>
                <input id="joinName" name="name" type="text" autocomplete="given-name" maxlength="80" placeholder="Alex">
            </div>
            <div class="field">
                <label for="joinEmail">E-Mail <span class="req" aria-hidden="true">*</span></label>
                <input id="joinEmail" name="email" type="email" autocomplete="email" maxlength="120" placeholder="alex@beispiel.de" required>
            </div>
            <div class="field field-wide">
                <label for="joinCity">Postleitzahl oder Ort</label>
                <input id="joinCity" name="city" type="text" autocomplete="postal-code" maxlength="60" placeholder="z. B. 80331 oder München">
            </div>
            <div class="field field-wide">
                <label for="joinMessage">Noch etwas, das wir wissen sollten?</label>
                <textarea id="joinMessage" name="message" rows="3" maxlength="1500" placeholder="Ideen, Fragen, Wünsche …"></textarea>
            </div>
        </div>
        <label class="check"><input type="checkbox" name="newsletter" value="1"><span>Haltet mich über den Start und Neuigkeiten auf dem Laufenden.</span></label>
        <label class="check"><input type="checkbox" name="privacy" value="1" required><span>Ich habe die <a href="<?= e(url('datenschutz')) ?>" target="_blank">Datenschutzerklärung</a> gelesen und bin mit der Verarbeitung meiner Angaben einverstanden. <span class="req" aria-hidden="true">*</span></span></label>
        <div class="form-nav">
            <button class="btn btn-ghost" type="button" data-step-prev>Zurück</button>
            <button class="btn btn-primary btn-heart magnetic" type="submit"><?= icon('rocket') ?> Beta-Platz sichern</button>
        </div>
    </fieldset>

    <div class="form-message" role="status" aria-live="polite"></div>
</form>
