<?php /** @var array $page */ ?>
<div class="mega" id="megaMenu" role="dialog" aria-modal="true" aria-label="Hauptmenü" aria-hidden="true" inert>
    <div class="mega-backdrop" aria-hidden="true">
        <span class="mega-orb mega-orb-a"></span>
        <span class="mega-orb mega-orb-b"></span>
        <span class="mega-grid-lines"></span>
    </div>

    <div class="mega-inner container">
        <div class="mega-columns">
<?php foreach (MENU as $index => $group): ?>
            <section class="mega-group">
                <p class="mega-label"><span class="mega-num"><?= sprintf('%02d', $index + 1) ?></span><?= e($group['label']) ?></p>
                <p class="mega-intro"><?= e($group['intro']) ?></p>
                <ul class="mega-list">
<?php foreach ($group['items'] as $item): $active = $page['route'] === $item['route']; ?>
                    <li>
                        <a class="mega-link<?= $active ? ' is-active' : '' ?>" href="<?= e(url($item['route'])) ?>"<?= $active ? ' aria-current="page"' : '' ?>>
                            <span class="mega-link-icon"><?= icon($item['icon']) ?></span>
                            <span class="mega-link-text">
                                <strong><?= e($item['label']) ?></strong>
                                <small><?= e($item['desc']) ?></small>
                            </span>
                            <?= icon('arrow-right', 'mega-link-arrow') ?>
                        </a>
                    </li>
<?php endforeach; ?>
                </ul>
            </section>
<?php endforeach; ?>
        </div>

        <aside class="mega-aside">
            <a class="mega-feature" href="<?= e(url('mitmachen')) ?>">
                <span class="mega-feature-badge"><span class="pulse-dot" aria-hidden="true"></span>Beta</span>
                <strong>Dein Mac.<br>Unser KI-Netz.</strong>
                <span class="mega-feature-text">Sichere dir jetzt einen Platz in der ersten Mesh-Generation.</span>
                <span class="mega-feature-cta">Platz sichern <?= icon('arrow-right') ?></span>
                <span class="mega-feature-mesh" aria-hidden="true"><i></i><i></i><i></i><i></i><i></i></span>
            </a>

            <div class="mega-contact">
                <p class="mega-label"><span class="mega-num">✦</span>Direkt erreichen</p>
                <ul>
<?php foreach (array_slice(contactChannels(), 0, 3) as $channel): ?>
                    <li><a href="<?= e($channel['href']) ?>"<?= $channel['external'] ? ' target="_blank" rel="noopener"' : '' ?>><?= icon($channel['icon']) ?><span><?= e($channel['detail']) ?></span></a></li>
<?php endforeach; ?>
                </ul>
            </div>

            <p class="mega-legal">
                <a href="<?= e(url('impressum')) ?>">Impressum</a>
                <a href="<?= e(url('datenschutz')) ?>">Datenschutz</a>
                <span><?= e(config('site.tagline')) ?></span>
            </p>
        </aside>
    </div>
</div>
