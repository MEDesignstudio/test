<?php
/** @var array $page @var string $content @var string $pageViewId */
$canonical = absUrl($page['route'] === '404' ? '' : $page['route']);
?><!doctype html>
<html lang="de" class="no-js">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title><?= e(pageTitle($page)) ?></title>
    <meta name="description" content="<?= e($page['description']) ?>">
<?php if (!empty($page['keywords'])): ?>
    <meta name="keywords" content="<?= e($page['keywords']) ?>">
<?php endif; ?>
    <meta name="robots" content="<?= e(pageRobots($page)) ?>">
    <meta name="author" content="<?= e(config('organization.name')) ?>">
    <meta name="theme-color" content="<?= e(config('site.themeColor', '#111111')) ?>">
    <meta name="color-scheme" content="dark light">
<?php if ($page['route'] !== '404'): ?>
    <link rel="canonical" href="<?= e($canonical) ?>">
    <link rel="alternate" hreflang="de" href="<?= e($canonical) ?>">
    <link rel="alternate" hreflang="x-default" href="<?= e($canonical) ?>">
<?php endif; ?>

    <meta property="og:type" content="<?= $page['route'] === '' ? 'website' : 'article' ?>">
    <meta property="og:site_name" content="<?= e(config('site.name')) ?>">
    <meta property="og:locale" content="<?= e(config('site.locale', 'de_DE')) ?>">
    <meta property="og:title" content="<?= e(pageTitle($page)) ?>">
    <meta property="og:description" content="<?= e($page['description']) ?>">
    <meta property="og:url" content="<?= e($canonical) ?>">
    <meta property="og:image" content="<?= e(imgSrc($page['og'], true)) ?>">
    <meta property="og:image:width" content="<?= e(imgData($page['og'])['width'] ?? 1200) ?>">
    <meta property="og:image:height" content="<?= e(imgData($page['og'])['height'] ?? 630) ?>">
    <meta property="og:image:alt" content="<?= e(imgData($page['og'])['alt'] ?? '') ?>">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?= e(pageTitle($page)) ?>">
    <meta name="twitter:description" content="<?= e($page['description']) ?>">
    <meta name="twitter:image" content="<?= e(imgSrc($page['og'], true)) ?>">

    <link rel="icon" href="<?= e(imgSrc('icon.favicon')) ?>" type="image/svg+xml">
    <link rel="icon" href="<?= e(imgSrc('icon.favicon32')) ?>" type="image/png" sizes="32x32">
    <link rel="apple-touch-icon" href="<?= e(imgSrc('icon.apple')) ?>">
    <link rel="manifest" href="<?= e(url('site.webmanifest')) ?>">

    <link rel="preload" href="<?= e(BASE_URL . 'assets/fonts/inter-latin-var.woff2') ?>" as="font" type="font/woff2" crossorigin>
    <link rel="stylesheet" href="<?= e(asset('css/main.css')) ?>">
    <noscript><link rel="stylesheet" href="<?= e(asset('css/noscript.css')) ?>"></noscript>
    <script src="<?= e(asset('js/main.js')) ?>" defer></script>

    <script type="application/ld+json"><?= schemaGraph($page) ?></script>
</head>
<body class="page-<?= e($page['route'] === '' ? 'home' : $page['route']) ?> theme-<?= e($page['theme']) ?>"<?= bodyData($page, $pageViewId) ?>>
    <a class="skip-link" href="#inhalt">Zum Inhalt springen</a>

    <?= partial('header', ['page' => $page]) ?>
    <?= partial('megaMenu', ['page' => $page]) ?>

    <main id="inhalt" tabindex="-1">
        <?= $content ?>
    </main>

    <?= partial('footer', ['page' => $page]) ?>
    <?= partial('floating', ['page' => $page]) ?>
    <div class="toast-stack" id="toastStack" aria-live="polite"></div>
</body>
</html>
