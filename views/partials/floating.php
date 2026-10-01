<?php /** @var array $page */ ?>
<a class="to-top" id="toTop" href="#inhalt" aria-label="Zurück nach oben">
    <svg class="to-top-ring" viewBox="0 0 52 52" aria-hidden="true">
        <circle class="to-top-track" cx="26" cy="26" r="23"></circle>
        <circle class="to-top-progress" id="toTopProgress" cx="26" cy="26" r="23" pathLength="100"></circle>
    </svg>
    <span class="to-top-icon"><?= icon('arrow-up') ?></span>
    <span class="to-top-done"><?= icon('check') ?></span>
    <span class="to-top-burst" aria-hidden="true"><i></i><i></i><i></i><i></i><i></i><i></i><i></i><i></i></span>
</a>

<div class="contact-fab" id="contactFab">
    <div class="contact-fab-panel" id="contactPanel" role="dialog" aria-label="Kontakt aufnehmen" aria-hidden="true" inert>
        <div class="contact-fab-header">
            <span>Kontakt aufnehmen</span>
            <button class="contact-fab-close" id="contactPanelClose" type="button" aria-label="Schließen"><?= icon('x') ?></button>
        </div>
        <p class="contact-fab-intro">Wir freuen uns auf dich – such dir einfach deinen Weg aus.</p>
        <div class="contact-fab-links">
<?php foreach (contactChannels() as $channel): ?>
            <a href="<?= e($channel['href']) ?>" class="contact-fab-item"<?= $channel['external'] ? ' target="_blank" rel="noopener"' : '' ?>>
                <span class="contact-fab-icon cfab-<?= e($channel['key']) ?>"><?= icon($channel['icon']) ?></span>
                <span class="contact-fab-text"><strong><?= e($channel['label']) ?></strong><small><?= e($channel['detail']) ?></small></span>
                <?= icon('arrow-right', 'contact-fab-arrow') ?>
            </a>
<?php endforeach; ?>
        </div>
    </div>
    <button class="contact-fab-button" id="contactFabButton" type="button" aria-expanded="false" aria-controls="contactPanel" aria-label="Kontakt aufnehmen">
        <span class="contact-fab-ring" aria-hidden="true"></span>
        <?= icon('chat', 'fab-icon-open') ?>
        <?= icon('x', 'fab-icon-close') ?>
    </button>
</div>
