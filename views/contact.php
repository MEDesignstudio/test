<?php
/** Kontakt. @var array $page */
$org = config('organization', []);
$channels = array_values(array_filter(contactChannels(), fn ($c) => in_array($c['key'], ['phone', 'whatsapp', 'email'], true)));
$address = array_values(array_filter(orgAddressLines(), fn ($l) => !isPlaceholder($l)));
?>
<?= partial('pageHero', [
    'page' => $page,
    'eyebrow' => 'Kontakt',
    'title' => 'Schreib uns. <em>Wir freuen uns.</em>',
    'lead' => 'Ob Frage, Idee oder Partnerschaft – wir melden uns schnell bei dir.',
    'ctas' => [['label' => 'Zum Formular', 'href' => '#formular', 'style' => 'primary', 'icon' => 'arrow-down']],
]) ?>

<section class="section-light">
    <div class="container">
        <div class="contact-cards">
<?php foreach ($channels as $channel): ?>
            <a class="contact-card reveal" href="<?= e($channel['href']) ?>"<?= $channel['external'] ? ' target="_blank" rel="noopener"' : '' ?>>
                <span class="contact-card-icon cfab-<?= e($channel['key']) ?>"><?= icon($channel['icon']) ?></span>
                <strong><?= e($channel['label']) ?></strong>
                <span><?= e($channel['detail']) ?></span>
                <span class="contact-card-cta"><?= $channel['key'] === 'phone' ? 'Jetzt anrufen' : ($channel['key'] === 'whatsapp' ? 'Chat öffnen' : 'Mail schreiben') ?> <?= icon('arrow-right') ?></span>
            </a>
<?php endforeach; ?>
            <div class="contact-card contact-card-static reveal">
                <span class="contact-card-icon cfab-join"><?= icon('map-pin') ?></span>
                <strong><?= e($org['name'] ?? 'GemeinsamKI') ?></strong>
                <span><?= $address ? implode('<br>', array_map('e', $address)) : 'Deutschland' ?></span>
<?php if (!empty($org['openingHours'])): ?>
                <span class="contact-card-cta contact-card-hours"><?= icon('clock') ?><?= e($org['openingHours']) ?></span>
<?php endif; ?>
            </div>
        </div>
    </div>
</section>

<section class="section-soft" id="formular">
    <div class="container split split-top">
        <div class="split-copy sticky-copy">
            <p class="eyebrow eyebrow-dark reveal">Kontaktformular</p>
            <h2 class="reveal" data-split>Lieber <em>schreiben?</em></h2>
            <p class="section-lead reveal">Erzähl uns, worum es geht. Wir antworten in der Regel innerhalb von zwei Werktagen.</p>
            <ul class="check-list reveal">
                <li><?= icon('check') ?>Persönliche Antwort</li>
                <li><?= icon('check') ?>Kein Newsletter ohne dein Okay</li>
                <li><?= icon('check') ?>Verschlüsselte Übertragung</li>
            </ul>
        </div>
        <div class="form-card reveal"><?= partial('contactForm', ['page' => $page]) ?></div>
    </div>
</section>
