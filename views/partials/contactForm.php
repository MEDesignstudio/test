<?php /** Kontaktformular. @var array $page */ ?>
<form class="form js-ajax-form" action="<?= e(url('controller.php')) ?>" method="post" novalidate>
    <?= formHiddenFields('contact', $page['route']) ?>
    <?= formNotice('contact') ?>
    <p class="field-label">Worum geht's?</p>
    <div class="chip-group">
<?php foreach (CONTACT_TOPICS as $key => $label): ?>
        <label class="chip chip-input"><input type="radio" name="topic" value="<?= e($key) ?>"<?= $key === 'frage' ? ' checked' : '' ?>><span><?= e($label) ?></span></label>
<?php endforeach; ?>
    </div>
    <div class="form-grid">
        <div class="field">
            <label for="contactName">Name <span class="req" aria-hidden="true">*</span></label>
            <input id="contactName" name="name" type="text" autocomplete="name" maxlength="80" required>
        </div>
        <div class="field">
            <label for="contactEmail">E-Mail <span class="req" aria-hidden="true">*</span></label>
            <input id="contactEmail" name="email" type="email" autocomplete="email" maxlength="120" required>
        </div>
        <div class="field">
            <label for="contactPhone">Telefon</label>
            <input id="contactPhone" name="phone" type="tel" autocomplete="tel" maxlength="40">
        </div>
        <div class="field">
            <label for="contactOrg">Organisation</label>
            <input id="contactOrg" name="organization" type="text" autocomplete="organization" maxlength="100">
        </div>
        <div class="field field-wide">
            <label for="contactMessage">Nachricht <span class="req" aria-hidden="true">*</span></label>
            <textarea id="contactMessage" name="message" rows="5" maxlength="3000" required placeholder="Wie können wir helfen?"></textarea>
        </div>
    </div>
    <label class="check"><input type="checkbox" name="privacy" value="1" required><span>Ich habe die <a href="<?= e(url('datenschutz')) ?>" target="_blank">Datenschutzerklärung</a> gelesen und bin einverstanden. <span class="req" aria-hidden="true">*</span></span></label>
    <div class="form-nav">
        <small class="form-hint"><span class="req">*</span> Pflichtfelder</small>
        <button class="btn btn-primary btn-heart magnetic" type="submit"><?= icon('send') ?> Nachricht senden</button>
    </div>
    <div class="form-message" role="status" aria-live="polite"></div>
</form>
