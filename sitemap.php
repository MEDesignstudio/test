<?php
/**
 * GemeinsamKI – sitemap.php
 * Erstellt sitemap.xml im Root (aus den Routen in app.php) und liefert sie direkt aus.
 * Aufruf im Browser (/sitemap.php bzw. /sitemap.xml) oder per CLI: php sitemap.php
 */
declare(strict_types=1);

require_once __DIR__ . '/app.php';

if (!function_exists('buildSitemap')) {
function buildSitemap(): string
{
    $xml = new XMLWriter();
    $xml->openMemory();
    $xml->setIndent(true);
    $xml->startDocument('1.0', 'UTF-8');
    $xml->startElement('urlset');
    $xml->writeAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');
    $xml->writeAttribute('xmlns:image', 'http://www.google.com/schemas/sitemap-image/1.1');
    foreach (ROUTES as $route => $page) {
        $file = VIEW_PATH . '/' . $page['view'] . '.php';
        $xml->startElement('url');
        $xml->writeElement('loc', absUrl($route));
        $xml->writeElement('lastmod', date('Y-m-d', is_file($file) ? (int) filemtime($file) : time()));
        $xml->writeElement('changefreq', $page['changefreq'] ?? 'monthly');
        $xml->writeElement('priority', $page['priority'] ?? '0.5');
        if ($route === '') {
            $xml->startElement('image:image');
            $xml->writeElement('image:loc', imgSrc('og.default', true));
            $xml->endElement();
        }
        $xml->endElement();
    }
    $xml->endElement();
    $xml->endDocument();
    return $xml->outputMemory();
}
}

$sitemap = buildSitemap();
@file_put_contents(ROOT_PATH . '/sitemap.xml', $sitemap, LOCK_EX);

if (PHP_SAPI !== 'cli') {
    header('Content-Type: application/xml; charset=utf-8');
    header('X-Robots-Tag: noindex');
}
echo $sitemap;
