<?php
/**
 * GemeinsamKI – app.php
 * Zentrale Funktionen, Konstanten und Zuordnungen (Routen, Menü, SEO, Schema.org).
 * Wird von index.php, controller.php, admin.php und sitemap.php eingebunden.
 */
declare(strict_types=1);

if (basename($_SERVER['SCRIPT_FILENAME'] ?? '') === 'app.php') {
    http_response_code(403);
    exit;
}

mb_internal_encoding('UTF-8');
date_default_timezone_set('Europe/Berlin');

/* ==========================================================================
   Konstanten & Pfade
   ========================================================================== */

define('APP_VERSION', '1.0.0');
define('ROOT_PATH', __DIR__);
define('DATA_PATH', ROOT_PATH . '/data');
define('LOG_PATH', DATA_PATH . '/logs');
define('VIEW_PATH', ROOT_PATH . '/views');
define('ASSET_PATH', ROOT_PATH . '/assets');

// BASE_URL: Pfad, unter dem die Seite liegt (z. B. "/" oder "/rc1/"). Ermöglicht Hosting im Unterordner.
$scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/'));
$scriptDir = ($scriptDir === '/' || $scriptDir === '.' || $scriptDir === '') ? '' : rtrim($scriptDir, '/');
define('BASE_URL', $scriptDir . '/');
unset($scriptDir);

if (is_file(ROOT_PATH . '/vendor/autoload.php')) {
    require_once ROOT_PATH . '/vendor/autoload.php';
}

/* ==========================================================================
   Zuordnungen: Routen (URL-Slug => View + SEO + Schema)
   ========================================================================== */

const ROUTES = [
    '' => [
        'view' => 'home',
        'nav' => 'Start',
        'title' => 'GemeinsamKI – Das deutsche KI-Mesh aus Macs',
        'description' => 'Schließ deinen Mac an das GemeinsamKI-Mesh an, gib freie Rechenleistung frei und nutze starke KI-Modelle – per Chat oder in VS Code. KI aus Deutschland, gemeinsam betrieben.',
        'keywords' => 'KI Mesh, Mac KI, dezentrale KI, KI Deutschland, LLM lokal, Apple Silicon KI, gemeinsame Rechenleistung',
        'schema' => 'home',
        'priority' => '1.0',
        'changefreq' => 'weekly',
        'theme' => 'dark',
    ],
    'so-funktionierts' => [
        'view' => 'how',
        'nav' => "So funktioniert's",
        'title' => "So funktioniert's – In drei Schritten ins KI-Mesh",
        'description' => 'Anschließen, freigeben, mitnutzen: So wird dein Mac Teil des GemeinsamKI-Mesh. Vom Download bis zur ersten KI-Antwort in wenigen Minuten.',
        'keywords' => 'KI Mesh Anleitung, Mac Rechenleistung teilen, verteilte KI, Hub Knoten',
        'schema' => 'howTo',
        'priority' => '0.9',
        'changefreq' => 'monthly',
    ],
    'mac-app' => [
        'view' => 'macApp',
        'nav' => 'Mac-App',
        'title' => 'Die Mac-App – Play, Stop, Zeitplan',
        'description' => 'Die GemeinsamKI-App für macOS: Mit einem Klick Rechenleistung freigeben, jederzeit stoppen oder per Zeitplan nur nachts teilen. Für Apple-Silicon-Macs.',
        'keywords' => 'GemeinsamKI App, macOS KI App, Apple Silicon, LM Studio, Rechenleistung freigeben',
        'schema' => 'macApp',
        'priority' => '0.9',
        'changefreq' => 'monthly',
    ],
    'vscode-plugin' => [
        'view' => 'vscode',
        'nav' => 'VS-Code-Plugin',
        'title' => 'VS-Code-Plugin – Das ganze Mesh im Editor',
        'description' => 'Mit dem GemeinsamKI-Plugin für Visual Studio Code nutzt du alle Modelle im Mesh direkt beim Programmieren: Vervollständigung, Chat und Refactoring.',
        'keywords' => 'VS Code KI Plugin, Code Assistent Deutschland, KI Autovervollständigung, LLM VS Code',
        'schema' => 'vscode',
        'priority' => '0.8',
        'changefreq' => 'monthly',
    ],
    'chat' => [
        'view' => 'chat',
        'nav' => 'Chat',
        'title' => 'Chat – Mit dem KI-Mesh sprechen',
        'description' => 'Stell deine Fragen direkt an das GemeinsamKI-Mesh. Bald auch unter chat.gemeinsamki.de – betrieben von Macs aus der Community.',
        'keywords' => 'KI Chat Deutschland, Chat GemeinsamKI, LLM Chat, deutsche KI',
        'schema' => 'chat',
        'priority' => '0.8',
        'changefreq' => 'weekly',
    ],
    'mitmachen' => [
        'view' => 'join',
        'nav' => 'Mitmachen',
        'title' => 'Mitmachen – Sichere dir deinen Beta-Platz',
        'description' => 'Melde dich für die GemeinsamKI-Beta an: Stell deinen Mac bereit, nutze das Mesh oder beides. In zwei Minuten dabei.',
        'keywords' => 'GemeinsamKI Beta, Warteliste, Mac anmelden, KI Community',
        'schema' => 'join',
        'priority' => '0.9',
        'changefreq' => 'monthly',
    ],
    'mission' => [
        'view' => 'mission',
        'nav' => 'Mission',
        'title' => 'Mission – KI in Deutschland bündeln',
        'description' => 'Warum wir GemeinsamKI bauen: Wir bündeln ungenutzte Rechenleistung zu einer KI, die in Deutschland läuft und uns allen gehört.',
        'keywords' => 'KI Souveränität, KI Deutschland, Gemeinschaft, Mission GemeinsamKI',
        'schema' => 'about',
        'priority' => '0.7',
        'changefreq' => 'monthly',
    ],
    'sicherheit' => [
        'view' => 'security',
        'nav' => 'Sicherheit',
        'title' => 'Sicherheit & Datenschutz im Mesh',
        'description' => 'Wie GemeinsamKI deinen Mac und deine Daten schützt: verschlüsselte Verbindungen, keine Speicherung von Inhalten und volle Kontrolle für dich.',
        'keywords' => 'KI Datenschutz, sichere KI, DSGVO KI, Mesh Sicherheit',
        'schema' => 'security',
        'priority' => '0.7',
        'changefreq' => 'monthly',
    ],
    'unterstuetzen' => [
        'view' => 'support',
        'nav' => 'Unterstützen',
        'title' => 'Unterstützen – Spenden, fördern, mitbauen',
        'description' => 'Hilf mit, KI in Deutschland zu bündeln: mit einer Spende, als Fördermitglied, Sponsor oder mit deinem Know-how.',
        'keywords' => 'GemeinsamKI spenden, KI Projekt fördern, Sponsoring, Open Source KI',
        'schema' => 'support',
        'priority' => '0.7',
        'changefreq' => 'monthly',
    ],
    'faq' => [
        'view' => 'faq',
        'nav' => 'FAQ',
        'title' => 'FAQ – Häufige Fragen zu GemeinsamKI',
        'description' => 'Antworten auf die häufigsten Fragen: Welche Macs passen? Was kostet es? Wie sicher ist das Mesh? Wann darf mein Mac rechnen?',
        'keywords' => 'GemeinsamKI FAQ, Fragen KI Mesh, Mac Voraussetzungen',
        'schema' => 'faq',
        'priority' => '0.7',
        'changefreq' => 'monthly',
    ],
    'kontakt' => [
        'view' => 'contact',
        'nav' => 'Kontakt',
        'title' => 'Kontakt – Wir freuen uns auf dich',
        'description' => 'Fragen, Ideen oder Partnerschaften? Schreib uns, ruf an oder nutze WhatsApp – das GemeinsamKI-Team meldet sich schnell.',
        'keywords' => 'GemeinsamKI Kontakt, Ansprechpartner, Partnerschaft KI',
        'schema' => 'contact',
        'priority' => '0.6',
        'changefreq' => 'yearly',
    ],
    'impressum' => [
        'view' => 'imprint',
        'nav' => 'Impressum',
        'title' => 'Impressum',
        'description' => 'Impressum und Anbieterkennzeichnung von GemeinsamKI (gemeinsamki.de und gemeinsamki.org).',
        'keywords' => 'Impressum GemeinsamKI',
        'schema' => 'legal',
        'priority' => '0.2',
        'changefreq' => 'yearly',
    ],
    'datenschutz' => [
        'view' => 'privacy',
        'nav' => 'Datenschutz',
        'title' => 'Datenschutzerklärung',
        'description' => 'So geht GemeinsamKI mit deinen Daten um: cookielose Statistik, sichere Formulare und volle Transparenz nach DSGVO.',
        'keywords' => 'Datenschutz GemeinsamKI, DSGVO',
        'schema' => 'legal',
        'priority' => '0.2',
        'changefreq' => 'yearly',
    ],
];

const NOT_FOUND_ROUTE = [
    'view' => 'notFound',
    'nav' => 'Seite nicht gefunden',
    'title' => 'Dieser Knoten ist offline (404)',
    'description' => 'Die gesuchte Seite existiert nicht (mehr). Zurück ins Mesh!',
    'keywords' => '',
    'schema' => 'legal',
    'robots' => 'noindex, follow',
    'theme' => 'dark',
];

/* Megamenü-Struktur (Gruppen => Routen) */
const MENU = [
    [
        'label' => 'Mitmachen',
        'intro' => 'Dein Mac wird Teil des Mesh.',
        'items' => [
            ['route' => 'mitmachen', 'label' => 'Beta-Platz sichern', 'desc' => 'In zwei Minuten dabei', 'icon' => 'rocket'],
            ['route' => 'mac-app', 'label' => 'Die Mac-App', 'desc' => 'Play, Stop & Zeitplan', 'icon' => 'laptop'],
            ['route' => 'so-funktionierts', 'label' => "So funktioniert's", 'desc' => 'Anschließen. Freigeben. Mitnutzen.', 'icon' => 'mesh'],
        ],
    ],
    [
        'label' => 'Nutzen',
        'intro' => 'Das ganze Mesh, da wo du arbeitest.',
        'items' => [
            ['route' => 'chat', 'label' => 'Chat', 'desc' => 'Frag das Mesh', 'icon' => 'chat'],
            ['route' => 'vscode-plugin', 'label' => 'VS-Code-Plugin', 'desc' => 'KI direkt im Editor', 'icon' => 'code'],
        ],
    ],
    [
        'label' => 'Projekt',
        'intro' => 'KI in Deutschland bündeln.',
        'items' => [
            ['route' => 'mission', 'label' => 'Mission', 'desc' => 'Warum wir das machen', 'icon' => 'heart'],
            ['route' => 'sicherheit', 'label' => 'Sicherheit', 'desc' => 'So schützen wir dich', 'icon' => 'shield'],
            ['route' => 'unterstuetzen', 'label' => 'Unterstützen', 'desc' => 'Spenden & fördern', 'icon' => 'gift'],
        ],
    ],
    [
        'label' => 'Hilfe',
        'intro' => 'Wir sind für dich da.',
        'items' => [
            ['route' => 'faq', 'label' => 'FAQ', 'desc' => 'Schnelle Antworten', 'icon' => 'question'],
            ['route' => 'kontakt', 'label' => 'Kontakt', 'desc' => 'Schreib uns', 'icon' => 'mail'],
        ],
    ],
];

/* Schnellnavigation im Header (Desktop) */
const QUICK_NAV = ['so-funktionierts', 'mac-app', 'chat', 'mission'];

/* Mac-Daten für Mac-Check und Anmeldung (GPU-Kerne: jeweils maximale Ausbaustufe) */
const MAC_CHIPS = [
    'M1' => 8, 'M1 Pro' => 16, 'M1 Max' => 32, 'M1 Ultra' => 64,
    'M2' => 10, 'M2 Pro' => 19, 'M2 Max' => 38, 'M2 Ultra' => 76,
    'M3' => 10, 'M3 Pro' => 18, 'M3 Max' => 40, 'M3 Ultra' => 80,
    'M4' => 10, 'M4 Pro' => 20, 'M4 Max' => 40,
    'M5' => 10, 'M5 Pro' => 20, 'M5 Max' => 40,
];
const MAC_RAM = [8, 16, 18, 24, 32, 36, 48, 64, 96, 128, 192, 256, 512];

const JOIN_ROLES = [
    'bereitstellen' => ['label' => 'Mac bereitstellen', 'desc' => 'Ich teile Rechenleistung', 'icon' => 'laptop'],
    'nutzen' => ['label' => 'Mesh nutzen', 'desc' => 'Ich will die KI nutzen', 'icon' => 'chat'],
    'beides' => ['label' => 'Beides', 'desc' => 'Geben und nehmen', 'icon' => 'heart'],
];
const JOIN_SCHEDULES = ['immer' => 'Immer', 'nachts' => 'Nur nachts', 'feierabend' => 'Nach Feierabend', 'wochenende' => 'Am Wochenende', 'offen' => 'Weiß ich noch nicht'];
const CONTACT_TOPICS = ['frage' => 'Frage', 'partnerschaft' => 'Partnerschaft', 'presse' => 'Presse', 'technik' => 'Technik', 'sonstiges' => 'Sonstiges'];
const DONATION_TYPES = [
    'spende' => ['label' => 'Spende', 'icon' => 'heart'],
    'foerdermitglied' => ['label' => 'Fördermitglied', 'icon' => 'users'],
    'sponsoring' => ['label' => 'Sponsoring', 'icon' => 'star'],
    'hardware' => ['label' => 'Hardware', 'icon' => 'laptop'],
];
const DONATION_AMOUNTS = [25, 50, 100, 250];

/* Datensätze, die unter data/ gesammelt und in admin.php verwaltet werden */
const RECORD_TYPES = [
    'registrations' => ['label' => 'Anmeldungen', 'icon' => 'rocket'],
    'contacts' => ['label' => 'Kontaktanfragen', 'icon' => 'mail'],
    'donations' => ['label' => 'Spendenanfragen', 'icon' => 'gift'],
];

const RECORD_STATUS = ['neu' => 'Neu', 'inBearbeitung' => 'In Bearbeitung', 'erledigt' => 'Erledigt', 'archiviert' => 'Archiviert'];

/* ==========================================================================
   JSON-Speicher (keine Datenbank – alles liegt als JSON unter data/)
   ========================================================================== */

function dataFile(string $name): string
{
    $name = preg_replace('/[^a-zA-Z0-9_\-\/.]/', '', $name);
    return DATA_PATH . '/' . (str_ends_with($name, '.json') ? $name : $name . '.json');
}

function readJson(string $file, $default = [])
{
    if (!is_file($file)) {
        return $default;
    }
    $raw = file_get_contents($file);
    if ($raw === false || $raw === '') {
        return $default;
    }
    $data = json_decode($raw, true);
    return $data ?? $default;
}

function writeJson(string $file, $data): bool
{
    $dir = dirname($file);
    if (!is_dir($dir)) {
        mkdir($dir, 0775, true);
    }
    $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    $tmp = $file . '.' . bin2hex(random_bytes(4)) . '.tmp';
    if (file_put_contents($tmp, $json, LOCK_EX) === false) {
        return false;
    }
    return rename($tmp, $file);
}

/** Liest, verändert und schreibt eine JSON-Datei unter exklusiver Sperre. */
function updateJson(string $file, callable $mutator, $default = [])
{
    $dir = dirname($file);
    if (!is_dir($dir)) {
        mkdir($dir, 0775, true);
    }
    $fp = fopen($file, 'c+');
    if (!$fp) {
        return null;
    }
    flock($fp, LOCK_EX);
    $raw = stream_get_contents($fp);
    $data = ($raw !== '' && $raw !== false) ? (json_decode($raw, true) ?? $default) : $default;
    $result = $mutator($data);
    ftruncate($fp, 0);
    rewind($fp);
    fwrite($fp, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    fflush($fp);
    flock($fp, LOCK_UN);
    fclose($fp);
    return $result;
}

/** Hängt einen Datensatz als JSON-Zeile an (für Logdateien). */
function appendJsonLine(string $file, array $data): bool
{
    $dir = dirname($file);
    if (!is_dir($dir)) {
        mkdir($dir, 0775, true);
    }
    $line = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . "\n";
    return file_put_contents($file, $line, FILE_APPEND | LOCK_EX) !== false;
}

function readJsonLines(string $file): array
{
    if (!is_file($file)) {
        return [];
    }
    $rows = [];
    $fp = fopen($file, 'r');
    if (!$fp) {
        return [];
    }
    while (($line = fgets($fp)) !== false) {
        $row = json_decode($line, true);
        if (is_array($row)) {
            $rows[] = $row;
        }
    }
    fclose($fp);
    return $rows;
}

/* ==========================================================================
   Konfiguration
   ========================================================================== */

function config(?string $path = null, $default = null)
{
    static $config = null;
    if ($config === null) {
        $config = readJson(dataFile('config'), []);
    }
    if ($path === null) {
        return $config;
    }
    $value = $config;
    foreach (explode('.', $path) as $key) {
        if (!is_array($value) || !array_key_exists($key, $value)) {
            return $default;
        }
        $value = $value[$key];
    }
    return $value;
}

function updateConfig(string $path, $newValue): bool
{
    $file = dataFile('config');
    $config = readJson($file, []);
    $ref = &$config;
    foreach (explode('.', $path) as $key) {
        if (!isset($ref[$key]) || !is_array($ref[$key])) {
            $ref[$key] = $ref[$key] ?? [];
        }
        $ref = &$ref[$key];
    }
    $ref = $newValue;
    unset($ref);
    return writeJson($file, $config);
}

/** Geheimschlüssel für Signaturen. Wird bei leerer Konfiguration automatisch in data/.secret erzeugt. */
function appSecret(): string
{
    static $secret = null;
    if ($secret !== null) {
        return $secret;
    }
    $secret = (string) config('security.appSecret', '');
    if ($secret === '') {
        $file = DATA_PATH . '/.secret';
        if (!is_file($file)) {
            file_put_contents($file, bin2hex(random_bytes(32)), LOCK_EX);
            @chmod($file, 0600);
        }
        $secret = trim((string) file_get_contents($file));
    }
    return $secret;
}

/* ==========================================================================
   URLs, Assets & Ausgabe
   ========================================================================== */

function e($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function isHttps(): bool
{
    if (!empty($_SERVER['HTTPS']) && strtolower((string) $_SERVER['HTTPS']) !== 'off') {
        return true;
    }
    if (strtolower($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https') {
        return true;
    }
    return (int) ($_SERVER['SERVER_PORT'] ?? 0) === 443;
}

function currentHost(): string
{
    $host = strtolower($_SERVER['HTTP_HOST'] ?? '');
    if ($host === '' || !preg_match('/^[a-z0-9.\-]+(:\d+)?$/', $host)) {
        $host = (string) config('site.primaryDomain', 'localhost');
    }
    return $host;
}

function siteOrigin(): string
{
    if (PHP_SAPI === 'cli') {
        return 'https://' . config('site.primaryDomain', 'localhost');
    }
    return (isHttps() ? 'https' : 'http') . '://' . currentHost();
}

function isProductionHost(): bool
{
    $host = preg_replace('/:\d+$/', '', currentHost());
    $primary = (string) config('site.primaryDomain', '');
    return PHP_SAPI === 'cli' || $host === $primary || $host === 'www.' . $primary;
}

/** Relativ zur BASE_URL: url('kontakt') => /unterordner/kontakt */
function url(string $route = '', array $query = [], string $hash = ''): string
{
    $route = trim($route, '/');
    $out = BASE_URL . $route;
    if ($query) {
        $out .= '?' . http_build_query($query);
    }
    if ($hash !== '') {
        $out .= '#' . $hash;
    }
    return $out;
}

function absUrl(string $route = '', array $query = []): string
{
    return siteOrigin() . url($route, $query);
}

/** Asset-URL mit Cache-Busting über das Änderungsdatum. */
function asset(string $path): string
{
    $path = ltrim($path, '/');
    $file = ASSET_PATH . '/' . $path;
    $version = is_file($file) ? (string) filemtime($file) : APP_VERSION;
    return BASE_URL . 'assets/' . $path . '?v=' . $version;
}

function attrs(array $attributes): string
{
    $html = '';
    foreach ($attributes as $key => $value) {
        if ($value === null || $value === false) {
            continue;
        }
        $html .= $value === true ? ' ' . e($key) : ' ' . e($key) . '="' . e($value) . '"';
    }
    return $html;
}

/* ==========================================================================
   Bilder – ausschließlich über matching.json zugeordnet
   ========================================================================== */

function imageMap(): array
{
    static $map = null;
    if ($map === null) {
        $map = readJson(dataFile('matching'), []);
    }
    return $map;
}

function imgData(string $key): array
{
    $map = imageMap();
    if (!isset($map[$key])) {
        trigger_error('Bildschlüssel nicht gefunden: ' . $key, E_USER_NOTICE);
        return ['src' => '', 'alt' => ''];
    }
    return $map[$key];
}

function imgSrc(string $key, bool $absolute = false): string
{
    $data = imgData($key);
    if (($data['src'] ?? '') === '') {
        return '';
    }
    $src = asset($data['src']);
    return $absolute ? siteOrigin() . $src : $src;
}

/** Gibt ein <img>-Tag für einen Schlüssel aus matching.json zurück. */
function img(string $key, array $attributes = []): string
{
    $data = imgData($key);
    if (($data['src'] ?? '') === '') {
        return '';
    }
    $base = [
        'src' => asset($data['src']),
        'alt' => $data['alt'] ?? '',
        'width' => $data['width'] ?? null,
        'height' => $data['height'] ?? null,
        'loading' => $data['loading'] ?? 'lazy',
        'decoding' => 'async',
    ];
    if (!empty($data['srcset']) && is_array($data['srcset'])) {
        $set = [];
        foreach ($data['srcset'] as $descriptor => $path) {
            $set[] = asset($path) . ' ' . $descriptor;
        }
        $base['srcset'] = implode(', ', $set);
    }
    return '<img' . attrs(array_merge($base, $attributes)) . '>';
}

/** Inline-Icon aus dem SVG-Sprite (assets/img/icons.svg, Schlüssel sprite.icons). */
function icon(string $name, string $class = ''): string
{
    static $sprite = null;
    if ($sprite === null) {
        $sprite = imgSrc('sprite.icons');
    }
    $cls = trim('icon icon-' . $name . ' ' . $class);
    return '<svg class="' . e($cls) . '" aria-hidden="true" focusable="false"><use href="' . e($sprite . '#i-' . $name) . '"></use></svg>';
}

/* ==========================================================================
   Routing & Seitendaten
   ========================================================================== */

function requestPath(): string
{
    $uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
    $uri = rawurldecode($uri);
    $base = rtrim(BASE_URL, '/');
    if ($base !== '' && str_starts_with($uri, $base)) {
        $uri = substr($uri, strlen($base));
    }
    return $uri === '' ? '/' : $uri;
}

function routeExists(string $route): bool
{
    return array_key_exists($route, ROUTES);
}

function page(string $route): array
{
    $page = ROUTES[$route] ?? NOT_FOUND_ROUTE;
    $page['route'] = routeExists($route) ? $route : '404';
    $page['robots'] = $page['robots'] ?? 'index, follow, max-image-preview:large';
    $page['theme'] = $page['theme'] ?? 'dark';
    $page['og'] = $page['og'] ?? 'og.default';
    return $page;
}

/** Leitet Alias-Domains (gemeinsamki.org, www.) auf die Hauptdomain um und erzwingt optional HTTPS. */
function handleCanonicalHost(): void
{
    $host = preg_replace('/:\d+$/', '', currentHost());
    $primary = (string) config('site.primaryDomain', '');
    $aliases = (array) config('site.aliasDomains', []);
    $toPrimary = config('site.redirectAliases', false) && $primary !== '' && in_array($host, $aliases, true);
    $toHttps = config('site.forceHttps', false) && !isHttps() && !in_array($host, ['localhost', '127.0.0.1'], true);
    if ($toPrimary || $toHttps) {
        $scheme = (config('site.forceHttps', false) || isHttps()) ? 'https' : 'http';
        $target = $scheme . '://' . ($toPrimary ? $primary : currentHost()) . ($_SERVER['REQUEST_URI'] ?? '/');
        header('Location: ' . $target, true, 301);
        exit;
    }
}

function sendSecurityHeaders(): void
{
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: SAMEORIGIN');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header('Permissions-Policy: camera=(), microphone=(), geolocation=(), interest-cohort=()');
    header("Content-Security-Policy: default-src 'self'; script-src 'self'; style-src 'self'; img-src 'self' data:; font-src 'self'; connect-src 'self'; frame-ancestors 'self'; base-uri 'self'; form-action 'self'; object-src 'none'");
    if (isHttps()) {
        header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
    }
    if (!isProductionHost()) {
        header('X-Robots-Tag: noindex, nofollow');
    }
}

function renderView(string $view, array $vars = []): string
{
    $view = preg_replace('#[^a-zA-Z0-9/]#', '', $view);
    $file = VIEW_PATH . '/' . str_replace('..', '', $view) . '.php';
    if (!is_file($file)) {
        return '';
    }
    extract($vars, EXTR_SKIP);
    ob_start();
    include $file;
    return (string) ob_get_clean();
}

function partial(string $name, array $vars = []): string
{
    return renderView('partials/' . $name, $vars);
}

/* ==========================================================================
   SEO & Schema.org
   ========================================================================== */

function pageTitle(array $page): string
{
    $site = (string) config('site.name', 'GemeinsamKI');
    return $page['route'] === '' ? $page['title'] : $page['title'] . ' | ' . $site;
}

function pageRobots(array $page): string
{
    return isProductionHost() ? $page['robots'] : 'noindex, nofollow';
}

function orgId(): string
{
    return absUrl() . '#organization';
}

function schemaOrganization(): array
{
    $org = config('organization', []);
    $address = $org['address'] ?? [];
    $sameAs = array_values(array_filter((array) config('social', [])));
    $schema = [
        '@type' => $org['schemaType'] ?? 'Organization',
        '@id' => orgId(),
        'name' => $org['name'] ?? 'GemeinsamKI',
        'legalName' => $org['legalName'] ?? null,
        'url' => absUrl(),
        'logo' => ['@type' => 'ImageObject', 'url' => imgSrc('brand.logoPng', true), 'width' => 1292, 'height' => 440],
        'slogan' => config('site.tagline'),
        'description' => config('site.claim'),
        'foundingDate' => $org['foundingDate'] ?? null,
        'email' => $org['email'] ?? null,
        'telephone' => $org['phone'] ?? null,
        'address' => [
            '@type' => 'PostalAddress',
            'streetAddress' => $address['street'] ?? null,
            'postalCode' => $address['zip'] ?? null,
            'addressLocality' => $address['city'] ?? null,
            'addressRegion' => $address['region'] ?? null,
            'addressCountry' => $address['countryCode'] ?? 'DE',
        ],
        'areaServed' => ['@type' => 'Country', 'name' => 'Deutschland'],
        'contactPoint' => [
            '@type' => 'ContactPoint',
            'contactType' => 'customer support',
            'email' => $org['email'] ?? null,
            'telephone' => $org['phone'] ?? null,
            'availableLanguage' => ['de', 'en'],
        ],
        'sameAs' => $sameAs ?: null,
    ];
    return schemaClean($schema);
}

function schemaWebsite(): array
{
    return [
        '@type' => 'WebSite',
        '@id' => absUrl() . '#website',
        'url' => absUrl(),
        'name' => config('site.name'),
        'alternateName' => ['gemeinsamki.de', 'gemeinsamki.org', 'Gemeinsam KI'],
        'description' => ROUTES['']['description'],
        'inLanguage' => config('site.language', 'de-DE'),
        'publisher' => ['@id' => orgId()],
    ];
}

function schemaWebPage(array $page, string $type = 'WebPage'): array
{
    return [
        '@type' => $type,
        '@id' => absUrl($page['route']) . '#webpage',
        'url' => absUrl($page['route']),
        'name' => pageTitle($page),
        'description' => $page['description'],
        'inLanguage' => config('site.language', 'de-DE'),
        'isPartOf' => ['@id' => absUrl() . '#website'],
        'about' => ['@id' => orgId()],
        'primaryImageOfPage' => ['@type' => 'ImageObject', 'url' => imgSrc($page['og'], true)],
        'breadcrumb' => $page['route'] !== '' ? ['@id' => absUrl($page['route']) . '#breadcrumb'] : null,
    ];
}

function schemaBreadcrumb(array $page): ?array
{
    if ($page['route'] === '' || $page['route'] === '404') {
        return null;
    }
    return [
        '@type' => 'BreadcrumbList',
        '@id' => absUrl($page['route']) . '#breadcrumb',
        'itemListElement' => [
            ['@type' => 'ListItem', 'position' => 1, 'name' => 'Start', 'item' => absUrl()],
            ['@type' => 'ListItem', 'position' => 2, 'name' => $page['nav'], 'item' => absUrl($page['route'])],
        ],
    ];
}

function schemaSoftware(string $name, string $category, string $os, string $description, string $route, array $features = []): array
{
    return [
        '@type' => 'SoftwareApplication',
        '@id' => absUrl($route) . '#software',
        'name' => $name,
        'applicationCategory' => $category,
        'operatingSystem' => $os,
        'description' => $description,
        'url' => absUrl($route),
        'image' => imgSrc('og.default', true),
        'inLanguage' => 'de',
        'featureList' => $features ?: null,
        'offers' => ['@type' => 'Offer', 'price' => '0', 'priceCurrency' => 'EUR', 'availability' => 'https://schema.org/PreOrder'],
        'publisher' => ['@id' => orgId()],
    ];
}

function schemaFaq(array $items): array
{
    return [
        '@type' => 'FAQPage',
        '@id' => absUrl('faq') . '#faq',
        'mainEntity' => array_map(fn ($item) => [
            '@type' => 'Question',
            'name' => $item['q'],
            'acceptedAnswer' => ['@type' => 'Answer', 'text' => $item['a']],
        ], $items),
    ];
}

/** Baut den kompletten, auf die Unterseite zugeschnittenen Schema.org-Graphen. */
function schemaGraph(array $page): string
{
    $graph = [schemaOrganization(), schemaWebsite()];
    $type = $page['schema'] ?? 'legal';
    $appFeatures = ['Freigabe per Play/Stop', 'Zeitsteuerung (z. B. nur nachts)', 'Ressourcen-Limits für RAM und GPU', 'Statusanzeige in der Menüleiste'];

    switch ($type) {
        case 'home':
            $graph[] = schemaWebPage($page);
            $graph[] = schemaSoftware('GemeinsamKI für macOS', 'UtilitiesApplication', 'macOS (Apple Silicon)', 'Verbindet deinen Mac mit dem GemeinsamKI-Mesh und teilt freie Rechenleistung für KI-Modelle.', 'mac-app', $appFeatures);
            break;
        case 'howTo':
            $graph[] = schemaWebPage($page);
            $graph[] = [
                '@type' => 'HowTo',
                '@id' => absUrl($page['route']) . '#howto',
                'name' => 'So wird dein Mac Teil des GemeinsamKI-Mesh',
                'description' => $page['description'],
                'totalTime' => 'PT5M',
                'tool' => [['@type' => 'HowToTool', 'name' => 'Mac mit Apple Silicon']],
                'step' => [
                    ['@type' => 'HowToStep', 'position' => 1, 'name' => 'Anschließen', 'text' => 'Lade die GemeinsamKI-App herunter und verbinde deinen Mac mit dem Mesh.', 'url' => absUrl($page['route']) . '#schritt-1'],
                    ['@type' => 'HowToStep', 'position' => 2, 'name' => 'Freigeben', 'text' => 'Drücke Play oder lege einen Zeitplan fest, wann dein Mac Rechenleistung teilt.', 'url' => absUrl($page['route']) . '#schritt-2'],
                    ['@type' => 'HowToStep', 'position' => 3, 'name' => 'Mitnutzen', 'text' => 'Nutze alle Modelle im Mesh über den Chat oder das VS-Code-Plugin.', 'url' => absUrl($page['route']) . '#schritt-3'],
                ],
            ];
            break;
        case 'macApp':
            $graph[] = schemaWebPage($page, 'ItemPage');
            $graph[] = schemaSoftware('GemeinsamKI für macOS', 'UtilitiesApplication', 'macOS 14 oder neuer (Apple Silicon)', $page['description'], $page['route'], $appFeatures);
            break;
        case 'vscode':
            $graph[] = schemaWebPage($page, 'ItemPage');
            $graph[] = schemaSoftware('GemeinsamKI für Visual Studio Code', 'DeveloperApplication', 'Windows, macOS, Linux (Visual Studio Code)', $page['description'], $page['route'], ['Code-Vervollständigung', 'Chat in der Seitenleiste', 'Modellauswahl aus dem Mesh', 'Erklären und Refactoring']);
            break;
        case 'chat':
            $graph[] = schemaWebPage($page);
            $graph[] = [
                '@type' => 'WebApplication',
                '@id' => absUrl($page['route']) . '#webapp',
                'name' => 'GemeinsamKI Chat',
                'url' => config('site.chatUrl') ?: absUrl($page['route']),
                'applicationCategory' => 'CommunicationApplication',
                'operatingSystem' => 'Browser',
                'browserRequirements' => 'Aktueller Webbrowser mit JavaScript',
                'offers' => ['@type' => 'Offer', 'price' => '0', 'priceCurrency' => 'EUR'],
                'publisher' => ['@id' => orgId()],
            ];
            break;
        case 'join':
            $webPage = schemaWebPage($page);
            $webPage['potentialAction'] = [
                '@type' => 'RegisterAction',
                'name' => 'Für die GemeinsamKI-Beta anmelden',
                'target' => absUrl($page['route']) . '#anmelden',
                'object' => ['@type' => 'Service', 'name' => 'GemeinsamKI-Mesh (Beta)', 'provider' => ['@id' => orgId()]],
            ];
            $graph[] = $webPage;
            break;
        case 'about':
            $graph[] = schemaWebPage($page, 'AboutPage');
            break;
        case 'security':
            $graph[] = schemaWebPage($page);
            $graph[] = [
                '@type' => 'TechArticle',
                '@id' => absUrl($page['route']) . '#article',
                'headline' => 'Sicherheit & Datenschutz im GemeinsamKI-Mesh',
                'description' => $page['description'],
                'inLanguage' => 'de',
                'author' => ['@id' => orgId()],
                'publisher' => ['@id' => orgId()],
                'mainEntityOfPage' => ['@id' => absUrl($page['route']) . '#webpage'],
                'about' => ['Datenschutz', 'IT-Sicherheit', 'Verteilte KI'],
            ];
            break;
        case 'support':
            $webPage = schemaWebPage($page);
            $webPage['potentialAction'] = [
                '@type' => 'DonateAction',
                'name' => 'GemeinsamKI unterstützen',
                'recipient' => ['@id' => orgId()],
                'target' => absUrl($page['route']) . '#spenden',
            ];
            $graph[] = $webPage;
            break;
        case 'faq':
            $graph[] = schemaWebPage($page);
            $graph[] = schemaFaq(faqItems());
            break;
        case 'contact':
            $graph[] = schemaWebPage($page, 'ContactPage');
            break;
        default:
            $graph[] = schemaWebPage($page);
    }

    $breadcrumb = schemaBreadcrumb($page);
    if ($breadcrumb) {
        $graph[] = $breadcrumb;
    }
    $json = ['@context' => 'https://schema.org', '@graph' => array_map('schemaClean', $graph)];
    return json_encode($json, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP);
}

/** Entfernt leere Werte und Platzhalter in [eckigen Klammern] aus Schema-Daten. */
function schemaClean($value)
{
    if (!is_array($value)) {
        return $value;
    }
    $out = [];
    foreach ($value as $key => $item) {
        $item = schemaClean($item);
        if ($item === null || $item === '' || $item === []) {
            continue;
        }
        if (is_string($item) && preg_match('/^\[.*\]$/', $item)) {
            continue;
        }
        $out[$key] = $item;
    }
    $isList = $value === [] || array_keys($value) === range(0, count($value) - 1);
    return $isList ? array_values($out) : $out;
}

/* ==========================================================================
   Inhalte aus data/
   ========================================================================== */

function faqItems(?string $category = null, int $limit = 0): array
{
    $groups = readJson(dataFile('faq'), []);
    $items = [];
    foreach ($groups as $group) {
        if ($category !== null && ($group['category'] ?? '') !== $category) {
            continue;
        }
        foreach ($group['items'] ?? [] as $item) {
            $item['category'] = $group['category'] ?? '';
            $items[] = $item;
        }
    }
    return $limit > 0 ? array_slice($items, 0, $limit) : $items;
}

function faqFeatured(int $limit = 5): array
{
    return array_slice(array_values(array_filter(faqItems(), fn ($i) => !empty($i['featured']))), 0, $limit);
}

function recordCount(string $type): int
{
    return count(readJson(dataFile($type), []));
}

function waitlistCount(): ?int
{
    $count = recordCount('registrations');
    return $count >= (int) config('site.showWaitlistCountFrom', 25) ? $count : null;
}

/** Kontaktwege für Kontakt-Button, Footer und Kontaktseite – vollständig aus config.json. */
function contactChannels(): array
{
    $org = config('organization', []);
    $channels = [];
    if (!empty($org['phone'])) {
        $channels[] = ['key' => 'phone', 'label' => 'Anrufen', 'detail' => $org['phoneDisplay'] ?? $org['phone'], 'href' => 'tel:' . preg_replace('/[^0-9+]/', '', $org['phone']), 'icon' => 'phone', 'external' => false];
    }
    if (!empty($org['whatsapp'])) {
        $channels[] = ['key' => 'whatsapp', 'label' => 'WhatsApp', 'detail' => 'Nachricht senden', 'href' => 'https://wa.me/' . preg_replace('/\D/', '', $org['whatsapp']), 'icon' => 'whatsapp', 'external' => true];
    }
    if (!empty($org['email'])) {
        $channels[] = ['key' => 'email', 'label' => 'E-Mail', 'detail' => $org['email'], 'href' => 'mailto:' . $org['email'], 'icon' => 'mail', 'external' => false];
    }
    $channels[] = ['key' => 'form', 'label' => 'Kontaktformular', 'detail' => 'Frage oder Idee senden', 'href' => url('kontakt', [], 'formular'), 'icon' => 'form', 'external' => false];
    $channels[] = ['key' => 'join', 'label' => 'Beta-Anmeldung', 'detail' => 'Deinen Mac anmelden', 'href' => url('mitmachen', [], 'anmelden'), 'icon' => 'rocket', 'external' => false];
    $channels[] = ['key' => 'support', 'label' => 'Unterstützen', 'detail' => 'Spende oder Sponsoring', 'href' => url('unterstuetzen', [], 'spenden'), 'icon' => 'gift', 'external' => false];
    return $channels;
}

function orgAddressLines(): array
{
    $a = config('organization.address', []);
    return array_values(array_filter([
        $a['street'] ?? '',
        trim(($a['zip'] ?? '') . ' ' . ($a['city'] ?? '')),
        $a['country'] ?? '',
    ]));
}

/* ==========================================================================
   Formularschutz: signierte Tokens (ohne Cookies), Honeypot, Rate-Limit
   ========================================================================== */

function formToken(): string
{
    $time = (string) time();
    $nonce = bin2hex(random_bytes(6));
    $sig = hash_hmac('sha256', $time . '|' . $nonce, appSecret());
    return $time . '.' . $nonce . '.' . substr($sig, 0, 32);
}

/** Prüft Signatur und Ausfüllzeit. Gibt null bei Erfolg, sonst eine Fehlermeldung zurück. */
function verifyFormToken(string $token): ?string
{
    $parts = explode('.', $token);
    if (count($parts) !== 3) {
        return 'Ungültiges Formular. Bitte lade die Seite neu.';
    }
    [$time, $nonce, $sig] = $parts;
    $expected = substr(hash_hmac('sha256', $time . '|' . $nonce, appSecret()), 0, 32);
    if (!hash_equals($expected, $sig)) {
        return 'Ungültiges Formular. Bitte lade die Seite neu.';
    }
    $age = time() - (int) $time;
    if ($age < (int) config('security.formMinSeconds', 3)) {
        return 'Das ging aber schnell! Bitte versuche es in ein paar Sekunden noch einmal.';
    }
    if ($age > 6 * 3600) {
        return 'Das Formular ist abgelaufen. Bitte lade die Seite neu.';
    }
    return null;
}

/** Einfaches Rate-Limit pro Bucket (z. B. Formular + Besucher-Hash). */
function rateLimit(string $bucket, int $limit, int $windowMinutes): bool
{
    $now = time();
    $window = $windowMinutes * 60;
    return false !== updateJson(dataFile('ratelimit'), function (array &$data) use ($bucket, $limit, $window, $now) {
        foreach ($data as $key => $hits) {
            $data[$key] = array_values(array_filter((array) $hits, fn ($t) => $t > $now - 86400));
            if (!$data[$key]) {
                unset($data[$key]);
            }
        }
        $hits = array_filter($data[$bucket] ?? [], fn ($t) => $t > $now - $window);
        if (count($hits) >= $limit) {
            return false;
        }
        $hits[] = $now;
        $data[$bucket] = array_values($hits);
        return true;
    });
}

/** Versteckte Pflichtfelder jedes Formulars: Aktion, signiertes Token, Herkunftsseite, Honeypot. */
function formHiddenFields(string $action, string $route): string
{
    return '<input type="hidden" name="action" value="' . e($action) . '">'
        . '<input type="hidden" name="token" value="' . e(formToken()) . '">'
        . '<input type="hidden" name="page" value="' . e($route) . '">'
        . '<div class="hp-field" aria-hidden="true"><label>Website <input type="text" name="website" value="" tabindex="-1" autocomplete="off"></label></div>';
}

/** Rückmeldung für Formulare ohne JavaScript (controller.php leitet mit ?danke= bzw. ?fehler= zurück). */
function formNotice(string $form): string
{
    if (($_GET['danke'] ?? '') === $form) {
        return '<p class="form-notice is-success" role="status">' . icon('check') . 'Danke! Deine Nachricht ist bei uns angekommen.</p>';
    }
    if (($_GET['fehler'] ?? '') === $form) {
        return '<p class="form-notice is-error" role="alert">' . icon('x') . 'Das hat leider nicht geklappt. Bitte prüfe deine Angaben.</p>';
    }
    return '';
}

/* ==========================================================================
   Besucher-Erkennung & Logfile-Tracking (cookielos, IP wird nicht gespeichert)
   ========================================================================== */

function clientIp(): string
{
    return (string) ($_SERVER['REMOTE_ADDR'] ?? '0.0.0.0');
}

/** Täglich wechselnder, anonymer Besucher-Hash aus IP + Browser (keine Cookies, keine IP-Speicherung). */
function visitorId(): string
{
    $daySalt = hash_hmac('sha256', date('Y-m-d'), appSecret());
    return substr(hash('sha256', $daySalt . '|' . clientIp() . '|' . ($_SERVER['HTTP_USER_AGENT'] ?? '')), 0, 16);
}

function isBot(string $ua): bool
{
    if ($ua === '') {
        return true;
    }
    return (bool) preg_match('/bot|crawl|spider|slurp|bingpreview|facebookexternalhit|embedly|quora|pinterest|whatsapp|telegram|discord|skype|curl|wget|python|httpclient|java\/|go-http|headless|lighthouse|pagespeed|gtmetrix|monitor|uptime|preview|scan|feed|okhttp|axios|node-fetch|ahrefs|semrush|mj12|dotbot|petal|yandex|baidu|duckduck|applebot|gptbot|claude|ccbot|perplexity|bytespider/i', $ua);
}

function parseUserAgent(string $ua): array
{
    $browser = 'Andere';
    $map = ['Edg/' => 'Edge', 'OPR/' => 'Opera', 'SamsungBrowser' => 'Samsung Internet', 'Firefox/' => 'Firefox', 'FxiOS' => 'Firefox', 'CriOS' => 'Chrome', 'Chrome/' => 'Chrome', 'Safari/' => 'Safari'];
    foreach ($map as $needle => $name) {
        if (str_contains($ua, $needle)) {
            $browser = $name;
            break;
        }
    }
    $os = 'Andere';
    if (preg_match('/iPhone|iPad|iPod/', $ua)) {
        $os = 'iOS';
    } elseif (str_contains($ua, 'Android')) {
        $os = 'Android';
    } elseif (str_contains($ua, 'Mac OS X') || str_contains($ua, 'Macintosh')) {
        $os = 'macOS';
    } elseif (str_contains($ua, 'Windows')) {
        $os = 'Windows';
    } elseif (str_contains($ua, 'CrOS')) {
        $os = 'ChromeOS';
    } elseif (str_contains($ua, 'Linux')) {
        $os = 'Linux';
    }
    $device = 'Desktop';
    if (preg_match('/iPad|Tablet|PlayBook|Silk|(Android(?!.*Mobile))/i', $ua)) {
        $device = 'Tablet';
    } elseif (preg_match('/Mobile|iPhone|iPod|Android.*Mobile|Windows Phone/i', $ua)) {
        $device = 'Smartphone';
    }
    return ['browser' => $browser, 'os' => $os, 'device' => $device];
}

function logFile(?int $timestamp = null): string
{
    return LOG_PATH . '/visits-' . date('Y-m', $timestamp ?? time()) . '.jsonl';
}

/** Loggt jeden Seitenaufruf explizit in data/logs/visits-JJJJ-MM.jsonl und liefert die Pageview-ID. */
function trackPageView(array $page, int $status = 200): string
{
    $id = 'pv' . bin2hex(random_bytes(6));
    if (!config('analytics.enabled', true) || PHP_SAPI === 'cli') {
        return $id;
    }
    if (in_array(clientIp(), (array) config('analytics.ignoreIps', []), true)) {
        return $id;
    }
    $ua = (string) ($_SERVER['HTTP_USER_AGENT'] ?? '');
    $referrer = (string) ($_SERVER['HTTP_REFERER'] ?? '');
    $refHost = $referrer !== '' ? strtolower((string) parse_url($referrer, PHP_URL_HOST)) : '';
    $ownHost = preg_replace('/:\d+$/', '', currentHost());
    $internal = $refHost !== '' && ($refHost === $ownHost || in_array($refHost, (array) config('site.aliasDomains', []), true) || $refHost === config('site.primaryDomain'));
    $utm = [];
    foreach (['utm_source', 'utm_medium', 'utm_campaign'] as $key) {
        if (!empty($_GET[$key])) {
            $utm[substr($key, 4)] = mb_substr((string) $_GET[$key], 0, 80);
        }
    }
    $client = parseUserAgent($ua);
    $entry = [
        't' => 'pv',
        'id' => $id,
        'ts' => time(),
        'vid' => visitorId(),
        'path' => mb_substr(requestPath(), 0, 200),
        'route' => $page['route'],
        'title' => $page['nav'] ?? '',
        'status' => $status,
        'ref' => $internal ? '' : mb_substr(preg_replace('/[?#].*$/', '', $referrer), 0, 200),
        'refHost' => $internal ? '' : preg_replace('/^www\./', '', $refHost),
        'internal' => $internal,
        'utm' => $utm ?: null,
        'browser' => $client['browser'],
        'os' => $client['os'],
        'device' => $client['device'],
        'lang' => strtolower(substr((string) ($_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? ''), 0, 2)),
        'bot' => isBot($ua),
    ];
    appendJsonLine(logFile(), $entry);
    return $id;
}

/** Ergänzt einen Seitenaufruf um Verweildauer und Scrolltiefe (per Beacon aus main.js). */
function trackPageLeave(string $pageViewId, int $seconds, int $scroll): void
{
    if (!config('analytics.enabled', true) || !preg_match('/^pv[a-f0-9]{12}$/', $pageViewId)) {
        return;
    }
    appendJsonLine(logFile(), [
        't' => 'leave',
        'id' => $pageViewId,
        'ts' => time(),
        'vid' => visitorId(),
        'dur' => max(0, min($seconds, 3 * 3600)),
        'scroll' => max(0, min($scroll, 100)),
    ]);
}

/* ==========================================================================
   Datensätze (Anmeldungen, Kontakt, Spenden)
   ========================================================================== */

function storeRecord(string $type, array $fields): array
{
    $record = [
        'id' => date('ymd') . '-' . bin2hex(random_bytes(3)),
        'createdAt' => date('c'),
        'status' => 'neu',
        'fields' => $fields,
        'meta' => [
            'vid' => visitorId(),
            'page' => mb_substr((string) ($_POST['page'] ?? ''), 0, 120),
            'client' => parseUserAgent((string) ($_SERVER['HTTP_USER_AGENT'] ?? '')),
        ],
        'notes' => '',
    ];
    updateJson(dataFile($type), function (array &$data) use ($record) {
        $data[] = $record;
    });
    return $record;
}

/* ==========================================================================
   E-Mail-Versand (PHPMailer via Composer, Fallback: mail())
   ========================================================================== */

function sendMail(string $to, string $subject, string $html, ?string $replyTo = null): bool
{
    $smtp = config('smtp', []);
    $fromEmail = $smtp['fromEmail'] ?? config('organization.email', 'info@localhost');
    $fromName = $smtp['fromName'] ?? config('site.name', 'GemeinsamKI');
    $text = trim(html_entity_decode(strip_tags(str_replace(['<br>', '</p>', '</tr>'], "\n", $html)), ENT_QUOTES, 'UTF-8'));

    if (!empty($smtp['enabled']) && class_exists('PHPMailer\\PHPMailer\\PHPMailer')) {
        try {
            $mail = new PHPMailer\PHPMailer\PHPMailer(true);
            $mail->isSMTP();
            $mail->Host = $smtp['host'];
            $mail->Port = (int) $smtp['port'];
            $mail->SMTPAuth = true;
            $mail->Username = $smtp['username'];
            $mail->Password = $smtp['password'];
            if (($smtp['secure'] ?? '') === 'ssl') {
                $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS;
            } elseif (($smtp['secure'] ?? '') === 'tls') {
                $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
            }
            $mail->CharSet = 'UTF-8';
            $mail->setFrom($fromEmail, $fromName);
            $mail->addAddress($to);
            if ($replyTo && filter_var($replyTo, FILTER_VALIDATE_EMAIL)) {
                $mail->addReplyTo($replyTo);
            }
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $html;
            $mail->AltBody = $text;
            return $mail->send();
        } catch (Throwable $exception) {
            error_log('GemeinsamKI Mailfehler: ' . $exception->getMessage());
            return false;
        }
    }

    $headers = [
        'MIME-Version: 1.0',
        'Content-Type: text/html; charset=UTF-8',
        'From: ' . mb_encode_mimeheader($fromName) . ' <' . $fromEmail . '>',
    ];
    if ($replyTo && filter_var($replyTo, FILTER_VALIDATE_EMAIL)) {
        $headers[] = 'Reply-To: ' . $replyTo;
    }
    return @mail($to, mb_encode_mimeheader($subject), $html, implode("\r\n", $headers));
}

/** Schlichtes HTML-Mail-Layout im GemeinsamKI-Stil. */
function mailTemplate(string $headline, string $intro, array $rows = []): string
{
    $table = '';
    foreach ($rows as $label => $value) {
        $table .= '<tr><td style="padding:6px 12px 6px 0;color:#6b6b80;vertical-align:top">' . e($label) . '</td><td style="padding:6px 0;color:#111111">' . nl2br(e($value)) . '</td></tr>';
    }
    return '<!doctype html><html lang="de"><body style="margin:0;background:#f4f3ff;font-family:Inter,Arial,sans-serif">'
        . '<div style="max-width:560px;margin:0 auto;padding:32px 20px"><div style="background:#ffffff;border-radius:20px;padding:32px">'
        . '<p style="margin:0 0 4px;color:#685EFF;font-weight:700;letter-spacing:.08em;font-size:12px;text-transform:uppercase">' . e(config('site.name')) . '</p>'
        . '<h1 style="margin:0 0 12px;font-size:22px;color:#111111">' . e($headline) . '</h1>'
        . '<p style="margin:0 0 20px;color:#3c3c4a;line-height:1.6">' . $intro . '</p>'
        . ($table ? '<table style="width:100%;border-collapse:collapse;font-size:14px">' . $table . '</table>' : '')
        . '</div><p style="text-align:center;color:#8a8aa0;font-size:12px;margin-top:16px">' . e(config('site.tagline')) . '</p></div></body></html>';
}

/* ==========================================================================
   Hilfsfunktionen
   ========================================================================== */

function jsonResponse(array $data, int $status = 200): void
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-store');
    header('X-Content-Type-Options: nosniff');
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

function cleanText($value, int $max = 500): string
{
    $value = trim(strip_tags((string) $value));
    $value = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $value) ?? '';
    return mb_substr($value, 0, $max);
}

function isPlaceholder($value): bool
{
    return is_string($value) && preg_match('/^\[.*\]$/', $value) === 1;
}

function formatNumber($number, int $decimals = 0): string
{
    return number_format((float) $number, $decimals, ',', '.');
}

function bodyData(array $page, string $pageViewId): string
{
    return attrs([
        'data-base' => BASE_URL,
        'data-route' => $page['route'],
        'data-pv' => $pageViewId,
        'data-endpoint' => url('controller.php'),
    ]);
}
