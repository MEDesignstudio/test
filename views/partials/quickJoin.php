<?php /** Kompakte Anmeldung (E-Mail + Rolle). @var array $page */ ?>
<form class="form quick-join js-ajax-form" action="<?= e(url('controller.php')) ?>" method="post" novalidate>
    <?= formHiddenFields('quickRegister', $page['route']) ?>
    <?= formNotice('quickRegister') ?>
    <div class="quick-join-row">
        <label class="sr-only" for="quickEmail">E-Mail-Adresse</label>
        <input id="quickEmail" name="email" type="email" autocomplete="email" placeholder="Deine E-Mail-Adresse" required maxlength="120">
        <label class="sr-only" for="quickRole">Ich möchte</label>
        <select id="quickRole" name="role">
<?php foreach (JOIN_ROLES as $key => $role): ?>
            <option value="<?= e($key) ?>"<?= $key === 'beides' ? ' selected' : '' ?>><?= e($role['label']) ?></option>
<?php endforeach; ?>
        </select>
        <button class="btn btn-primary btn-heart magnetic" type="submit">Platz sichern <?= icon('arrow-right') ?></button>
    </div>
    <label class="check check-light"><input type="checkbox" name="privacy" value="1" required><span>Ich bin mit der Verarbeitung gemäß <a href="<?= e(url('datenschutz')) ?>">Datenschutzerklärung</a> einverstanden.</span></label>
    <div class="form-message" role="status" aria-live="polite"></div>
</form>
