<?php /** @var array $page */ ?>
<header class="site-header" id="siteHeader">
    <div class="header-inner container">
        <a class="brand" href="<?= e(url()) ?>" aria-label="<?= e(config('site.name')) ?> – zur Startseite">
            <?= img('brand.wordmarkLight', ['class' => 'brand-logo', 'alt' => config('site.name'), 'loading' => 'eager']) ?>
        </a>

        <nav class="quick-nav" aria-label="Schnellnavigation">
            <ul>
<?php foreach (QUICK_NAV as $route): ?>
                <li><a class="quick-link<?= $page['route'] === $route ? ' is-active' : '' ?>" href="<?= e(url($route)) ?>"<?= $page['route'] === $route ? ' aria-current="page"' : '' ?>><?= e(ROUTES[$route]['nav']) ?></a></li>
<?php endforeach; ?>
            </ul>
        </nav>

        <div class="header-actions">
            <a class="btn btn-primary btn-sm btn-heart header-cta" href="<?= e(url('mitmachen')) ?>">
                <span class="btn-dot" aria-hidden="true"></span>Mac anschließen
            </a>
            <button class="menu-toggle" id="menuToggle" type="button" aria-expanded="false" aria-controls="megaMenu">
                <span class="menu-toggle-label" data-open="Menü" data-close="Schließen">Menü</span>
                <span class="burger" aria-hidden="true"><i></i><i></i><i></i></span>
            </button>
        </div>
    </div>
</header>
