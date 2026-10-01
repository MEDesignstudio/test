<?php /** Spenden-/Förderanfrage. @var array $page */ ?>
<form class="form js-ajax-form" action="<?= e(url('controller.php')) ?>" method="post" novalidate data-donate-form>
    <?= formHiddenFields('donate', $page['route']) ?>
    <?= formNotice('donate') ?>
    <p class="field-label">Wie möchtest du helfen?</p>
    <div class="choice-cards choice-cards-4">
<?php foreach (DONATION_TYPES as $key => $type): ?>
        <label class="choice-card choice-card-sm">
            <input type="radio" name="type" value="<?= e($key) ?>"<?= $key === 'spende' ? ' checked' : '' ?>>
            <span class="choice-card-inner">
                <span class="choice-icon"><?= icon($type['icon']) ?></span>
                <strong><?= e($type['label']) ?></strong>
                <span class="choice-check"><?= icon('check') ?></span>
            </span>
        </label>
<?php endforeach; ?>
    </div>

    <div class="donate-amount" data-amount-group>
        <p class="field-label">Betrag</p>
        <div class="chip-group">
<?php foreach (DONATION_AMOUNTS as $amount): ?>
            <label class="chip chip-input"><input type="radio" name="amountPreset" value="<?= $amount ?>"<?= $amount === 50 ? ' checked' : '' ?>><span><?= $amount ?> €</span></label>
<?php endforeach; ?>
            <label class="chip chip-input"><input type="radio" name="amountPreset" value="frei"><span>Freier Betrag</span></label>
        </div>
        <div class="form-grid">
            <div class="field" data-amount-free>
                <label for="donateAmount">Dein Betrag in €</label>
                <input id="donateAmount" name="amount" type="number" min="1" max="1000000" step="1" inputmode="numeric" placeholder="z. B. 75">
            </div>
            <div class="field">
                <label for="donateInterval">Rhythmus</label>
                <select id="donateInterval" name="interval">
                    <option value="einmalig">Einmalig</option>
                    <option value="monatlich">Monatlich</option>
                    <option value="jaehrlich">Jährlich</option>
                </select>
            </div>
        </div>
    </div>

    <div class="form-grid">
        <div class="field">
            <label for="donateName">Name <span class="req" aria-hidden="true">*</span></label>
            <input id="donateName" name="name" type="text" autocomplete="name" maxlength="80" required>
        </div>
        <div class="field">
            <label for="donateEmail">E-Mail <span class="req" aria-hidden="true">*</span></label>
            <input id="donateEmail" name="email" type="email" autocomplete="email" maxlength="120" required>
        </div>
        <div class="field field-wide">
            <label for="donateOrg">Unternehmen / Organisation</label>
            <input id="donateOrg" name="organization" type="text" autocomplete="organization" maxlength="100">
        </div>
        <div class="field field-wide">
            <label for="donateMessage">Nachricht</label>
            <textarea id="donateMessage" name="message" rows="3" maxlength="2000" placeholder="z. B. welche Hardware du spenden möchtest"></textarea>
        </div>
    </div>
    <label class="check"><input type="checkbox" name="receipt" value="1"><span>Ich wünsche eine Zuwendungsbestätigung.</span></label>
    <label class="check"><input type="checkbox" name="privacy" value="1" required><span>Ich habe die <a href="<?= e(url('datenschutz')) ?>" target="_blank">Datenschutzerklärung</a> gelesen und bin einverstanden. <span class="req" aria-hidden="true">*</span></span></label>
    <div class="form-nav">
        <small class="form-hint">Wir melden uns mit allen Details – es wird nichts automatisch abgebucht.</small>
        <button class="btn btn-primary btn-heart magnetic" type="submit"><?= icon('heart') ?> Anfrage senden</button>
    </div>
    <div class="form-message" role="status" aria-live="polite"></div>
</form>
