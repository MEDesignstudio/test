<?php
/** Unterstützen / Spenden. @var array $page */
$bank = config('bank', []);
$bankComplete = !isPlaceholder($bank['iban'] ?? '[x]') && !empty($bank['iban']);
ob_start();
?>
<div class="bank-card" aria-label="Spendenkonto">
    <div class="bank-card-top">
        <?= img('brand.wordmarkLight', ['class' => 'bank-card-logo', 'alt' => 'GemeinsamKI']) ?>
        <span class="bank-chip" aria-hidden="true"></span>
    </div>
    <p class="bank-card-label">Spendenkonto</p>
    <p class="bank-card-iban mono"><?= e($bankComplete ? $bank['iban'] : 'DE•• •••• •••• •••• •••• ••') ?></p>
    <div class="bank-card-foot">
        <span><small>Kontoinhaber</small><?= e(isPlaceholder($bank['accountHolder'] ?? '') ? config('organization.name') : $bank['accountHolder']) ?></span>
        <span><small>Verwendungszweck</small><?= e($bank['reference'] ?? 'Spende') ?></span>
    </div>
    <span class="bank-card-heart" aria-hidden="true"><?= icon('heart') ?></span>
</div>
<?php $bankCard = ob_get_clean(); ?>
<?= partial('pageHero', [
    'page' => $page,
    'eyebrow' => 'Unterstützen',
    'title' => 'Hilf mit, KI zu <em>teilen.</em>',
    'lead' => 'Jeder Beitrag macht das Mesh stärker – ob Spende, Fördermitgliedschaft, Sponsoring oder Hardware.',
    'ctas' => [
        ['label' => 'Jetzt unterstützen', 'href' => '#spenden', 'style' => 'primary', 'icon' => 'heart'],
        ['label' => 'Mitentwickeln', 'href' => url('kontakt'), 'style' => 'ghost'],
    ],
    'visual' => $bankCard,
]) ?>

<section class="section-light">
    <div class="container">
        <header class="section-head">
            <p class="eyebrow eyebrow-dark reveal">So kannst du helfen</p>
            <h2 class="reveal" data-split>Fünf Wege, <em>Gutes zu tun.</em></h2>
        </header>
        <div class="card-grid card-grid-5">
            <article class="feature-card reveal"><span class="feature-card-icon"><?= icon('heart') ?></span><h3>Spenden</h3><p>Einmalig, für Server und Entwicklung.</p></article>
            <article class="feature-card reveal"><span class="feature-card-icon"><?= icon('users') ?></span><h3>Fördern</h3><p>Regelmäßig als Fördermitglied.</p></article>
            <article class="feature-card reveal"><span class="feature-card-icon"><?= icon('star') ?></span><h3>Sponsern</h3><p>Als Unternehmen sichtbar unterstützen.</p></article>
            <article class="feature-card reveal"><span class="feature-card-icon"><?= icon('laptop') ?></span><h3>Hardware</h3><p>Ausgemusterte Macs dem Mesh schenken.</p></article>
            <article class="feature-card reveal"><span class="feature-card-icon"><?= icon('code') ?></span><h3>Mitbauen</h3><p>Code, Design oder Ideen beisteuern.</p></article>
        </div>
    </div>
</section>

<section class="section-soft" id="spenden">
    <div class="container split split-top">
        <div class="split-copy sticky-copy">
            <p class="eyebrow eyebrow-dark reveal">Anfrage stellen</p>
            <h2 class="reveal" data-split>Dein Beitrag. <em>Unser Dank.</em></h2>
            <p class="section-lead reveal">Sag uns, wie du helfen möchtest. Wir melden uns persönlich mit allen Details.</p>
<?php if ($bankComplete): ?>
            <dl class="bank-details reveal">
                <div><dt>Kontoinhaber</dt><dd><?= e($bank['accountHolder']) ?></dd></div>
                <div><dt>IBAN</dt><dd class="mono"><?= e($bank['iban']) ?> <button class="code-copy" type="button" data-copy-text="<?= e(str_replace(' ', '', $bank['iban'])) ?>" aria-label="IBAN kopieren"><?= icon('copy') ?></button></dd></div>
<?php if (!isPlaceholder($bank['bic'] ?? '')): ?>
                <div><dt>BIC</dt><dd class="mono"><?= e($bank['bic']) ?></dd></div>
<?php endif; ?>
                <div><dt>Bank</dt><dd><?= e($bank['bankName']) ?></dd></div>
                <div><dt>Zweck</dt><dd><?= e($bank['reference']) ?></dd></div>
            </dl>
<?php endif; ?>
            <p class="note reveal"><?= icon('info-circle') ?><?= e(config('organization.charitable') && !isPlaceholder(config('organization.charitable')) ? config('organization.charitable') : 'Infos zur Spendenquittung erhältst du mit unserer Antwort.') ?></p>
        </div>
        <div class="form-card reveal"><?= partial('donateForm', ['page' => $page]) ?></div>
    </div>
</section>

<section class="section-dark">
    <div class="section-glow section-glow-right" aria-hidden="true"></div>
    <div class="container">
        <header class="section-head center">
            <p class="eyebrow reveal"><?= icon('sparkle') ?>Wofür?</p>
            <h2 class="reveal" data-split>Hier wirkt <em>dein Beitrag.</em></h2>
        </header>
        <div class="card-grid card-grid-3 card-grid-dark">
            <article class="feature-card reveal"><span class="feature-card-icon"><?= icon('server') ?></span><h3>Infrastruktur</h3><p>Hub-Server, die das Mesh zusammenhalten.</p></article>
            <article class="feature-card reveal"><span class="feature-card-icon"><?= icon('code') ?></span><h3>Entwicklung</h3><p>App, Chat und Plugin – stabil und sicher.</p></article>
            <article class="feature-card reveal"><span class="feature-card-icon"><?= icon('users') ?></span><h3>Community</h3><p>Treffen, Workshops und Hilfe beim Einstieg.</p></article>
        </div>
    </div>
</section>
