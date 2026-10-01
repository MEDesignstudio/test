<?php
/**
 * GemeinsamKI – index.php
 * Haupteinstieg: Routing, Logging jedes Seitenaufrufs und Ausgabe der Unterseite im Layout.
 */
declare(strict_types=1);

require __DIR__ . '/app.php';

handleCanonicalHost();

$path = requestPath();

// Einheitliche URLs: abschließenden Schrägstrich und index.php entfernen
if ($path !== '/' && (str_ends_with($path, '/') || $path === '/index.php')) {
    $target = url(trim(str_replace('/index.php', '', $path), '/'));
    $query = $_SERVER['QUERY_STRING'] ?? '';
    header('Location: ' . $target . ($query !== '' ? '?' . $query : ''), true, 301);
    exit;
}

$route = trim($path, '/');
$status = routeExists($route) ? 200 : 404;
$page = page($route);

http_response_code($status);
header('Content-Type: text/html; charset=utf-8');
sendSecurityHeaders();

$pageViewId = trackPageView($page, $status);
$content = renderView($page['view'], ['page' => $page]);

include VIEW_PATH . '/partials/layout.php';
