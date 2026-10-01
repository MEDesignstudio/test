<?php
/** @var array $page */
$org = config('organization', []);
$socialIcons = ['github' => 'github', 'linkedin' => 'linkedin', 'mastodon' => 'mastodon', 'instagram' => 'instagram', 'youtube' => 'youtube'];
$words = ['Anschließen', 'Freigeben', 'Mitnutzen', 'Gemeinsam'];
?>
<footer class="site-footer">
    <div class="footer-marquee" aria-hidden="true">
        <div class="marquee-track">
<?php for ($i = 0; $i < 2; $i++): ?>
            <div class="marquee-group">
<?php foreach ($words as $word): ?>
                <span class="marquee-word"><?= e($word) ?></span><span class="marquee-star">✦</span>
<?php endforeach; ?>
            </div>
<?php endfor; ?>
        </div>
    </div>

    <div class="container footer-main">
        <div class="footer-brand">
            <a href="<?= e(url()) ?>" class="footer-logo" aria-label="<?= e(config('site.name')) ?> – zur Startseite">
                <?= img('brand.logoLight', ['class' => 'footer-logo-img']) ?>
            </a>
            <p class="footer-claim"><?= e(config('site.claim')) ?>. Gemeinsam betrieben, in Deutschland zuhause.</p>
            <p class="footer-domains"><?= icon('globe') ?> gemeinsamki.de <span>·</span> gemeinsamki.org</p>
<?php $social = array_filter((array) config('social', [])); if ($social): ?>
            <ul class="footer-social">
<?php foreach ($social as $network => $link): ?>
                <li><a href="<?= e($link) ?>" target="_blank" rel="noopener" aria-label="<?= e(ucfirst($network)) ?>"><?= icon($socialIcons[$network] ?? 'link') ?></a></li>
<?php endforeach; ?>
            </ul>
<?php endif; ?>
        </div>

        <nav class="footer-nav" aria-label="Fußzeilennavigation">
<?php foreach (MENU as $group): ?>
            <div class="footer-col">
                <p class="footer-heading"><?= e($group['label']) ?></p>
                <ul>
<?php foreach ($group['items'] as $item): ?>
                    <li><a class="link-pulse" href="<?= e(url($item['route'])) ?>"><?= e($item['label']) ?></a></li>
<?php endforeach; ?>
                </ul>
            </div>
<?php endforeach; ?>
        </nav>

        <div class="footer-contact">
            <p class="footer-heading">Kontakt</p>
            <address>
                <strong><?= e(isPlaceholder($org['legalName'] ?? '') ? $org['name'] : ($org['legalName'] ?? $org['name'])) ?></strong><br>
<?php foreach (array_filter(orgAddressLines(), fn ($l) => !isPlaceholder($l)) as $line): ?>
                <?= e($line) ?><br>
<?php endforeach; ?>
            </address>
            <ul class="footer-channels">
<?php foreach (array_slice(contactChannels(), 0, 3) as $channel): ?>
                <li><a class="link-pulse" href="<?= e($channel['href']) ?>"<?= $channel['external'] ? ' target="_blank" rel="noopener"' : '' ?>><?= icon($channel['icon']) ?><?= e($channel['key'] === 'whatsapp' ? 'WhatsApp schreiben' : $channel['detail']) ?></a></li>
<?php endforeach; ?>
            </ul>
        </div>
    </div>

    <div class="container footer-bottom">
        <p>© <?= date('Y') ?> <?= e($org['name'] ?? 'GemeinsamKI') ?> · <?= e(config('site.tagline')) ?></p>
        <ul>
            <li><a class="link-pulse" href="<?= e(url('impressum')) ?>">Impressum</a></li>
            <li><a class="link-pulse" href="<?= e(url('datenschutz')) ?>">Datenschutz</a></li>
            <li><a class="link-pulse" href="<?= e(url('kontakt')) ?>">Kontakt</a></li>
        </ul>
        <p class="footer-love">Mit <?= icon('heart', 'heart-beat') ?> in Deutschland gebaut</p>
    </div>
</footer>
