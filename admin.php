<?php
/**
 * GemeinsamKI – admin.php
 * Geschützter Verwaltungsbereich: Analytics aus den Logdateien und Verwaltung der Datensätze unter data/.
 */
declare(strict_types=1);

require __DIR__ . '/app.php';

session_name('gkiAdmin');
session_set_cookie_params([
    'lifetime' => 0,
    'path' => BASE_URL,
    'secure' => isHttps(),
    'httponly' => true,
    'samesite' => 'Strict',
]);
session_start();

header('Content-Type: text/html; charset=utf-8');
header('X-Robots-Tag: noindex, nofollow');
header('Cache-Control: no-store');
sendSecurityHeaders();

/* ==========================================================================
   Authentifizierung
   ========================================================================== */

function adminCsrf(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(16));
    }
    return $_SESSION['csrf'];
}

function checkCsrf(): void
{
    if (!hash_equals(adminCsrf(), (string) ($_POST['csrf'] ?? $_GET['csrf'] ?? ''))) {
        http_response_code(400);
        exit('Ungültiges Sicherheitstoken. Bitte Seite neu laden.');
    }
}

function isLoggedIn(): bool
{
    $minutes = (int) config('admin.sessionMinutes', 120);
    if (empty($_SESSION['admin']) || (time() - (int) ($_SESSION['seen'] ?? 0)) > $minutes * 60) {
        return false;
    }
    $_SESSION['seen'] = time();
    return true;
}

function loginKey(): string
{
    return hash('sha256', clientIp() . appSecret());
}

function loginLocked(): int
{
    $attempts = readJson(dataFile('loginAttempts'), []);
    $entry = $attempts[loginKey()] ?? null;
    if (!$entry) {
        return 0;
    }
    $lockSeconds = (int) config('admin.lockMinutes', 15) * 60;
    if ($entry['count'] >= (int) config('admin.maxLoginAttempts', 5) && time() - $entry['last'] < $lockSeconds) {
        return (int) ceil(($lockSeconds - (time() - $entry['last'])) / 60);
    }
    return 0;
}

function registerFailedLogin(): void
{
    $key = loginKey();
    $lockSeconds = (int) config('admin.lockMinutes', 15) * 60;
    updateJson(dataFile('loginAttempts'), function (array &$data) use ($key, $lockSeconds) {
        $entry = $data[$key] ?? ['count' => 0, 'last' => 0];
        if (time() - $entry['last'] > $lockSeconds) {
            $entry['count'] = 0;
        }
        $entry['count']++;
        $entry['last'] = time();
        $data[$key] = $entry;
    });
}

function adminUrl(array $params = []): string
{
    return url('admin.php', $params);
}

$loginError = '';
if (($_POST['do'] ?? '') === 'login') {
    $locked = loginLocked();
    if ($locked > 0) {
        $loginError = 'Zu viele Fehlversuche. Bitte warte ' . $locked . ' Minute(n).';
    } elseif (hash_equals((string) config('admin.username', 'admin'), (string) ($_POST['username'] ?? ''))
        && password_verify((string) ($_POST['password'] ?? ''), (string) config('admin.passwordHash', ''))) {
        session_regenerate_id(true);
        $_SESSION['admin'] = true;
        $_SESSION['seen'] = time();
        updateJson(dataFile('loginAttempts'), function (array &$data) {
            unset($data[loginKey()]);
        });
        header('Location: ' . adminUrl());
        exit;
    } else {
        registerFailedLogin();
        usleep(400000);
        $loginError = 'Benutzername oder Passwort falsch.';
    }
}

if (($_GET['do'] ?? '') === 'logout') {
    $_SESSION = [];
    session_destroy();
    header('Location: ' . adminUrl());
    exit;
}

if (!isLoggedIn()) {
    renderAdminLogin($loginError);
    exit;
}

/* ==========================================================================
   Aktionen (POST)
   ========================================================================== */

$flash = $_SESSION['flash'] ?? '';
unset($_SESSION['flash']);

function redirectWithFlash(string $message, array $params): void
{
    $_SESSION['flash'] = $message;
    header('Location: ' . adminUrl($params));
    exit;
}

$do = (string) ($_POST['do'] ?? '');
if ($do !== '') {
    checkCsrf();
    $type = (string) ($_POST['type'] ?? '');
    $id = (string) ($_POST['id'] ?? '');
    switch ($do) {
        case 'recordUpdate':
            if (isset(RECORD_TYPES[$type])) {
                updateJson(dataFile($type), function (array &$data) use ($id) {
                    foreach ($data as &$record) {
                        if (($record['id'] ?? '') === $id) {
                            $status = (string) ($_POST['status'] ?? 'neu');
                            $record['status'] = array_key_exists($status, RECORD_STATUS) ? $status : 'neu';
                            $record['notes'] = cleanText($_POST['notes'] ?? '', 4000);
                            $record['updatedAt'] = date('c');
                        }
                    }
                });
            }
            redirectWithFlash('Datensatz gespeichert.', ['view' => 'records', 'type' => $type]);
            break;
        case 'recordDelete':
            if (isset(RECORD_TYPES[$type])) {
                updateJson(dataFile($type), function (array &$data) use ($id) {
                    $data = array_values(array_filter($data, fn ($r) => ($r['id'] ?? '') !== $id));
                });
            }
            redirectWithFlash('Datensatz gelöscht.', ['view' => 'records', 'type' => $type]);
            break;
        case 'sitemap':
            ob_start();
            include ROOT_PATH . '/sitemap.php';
            ob_end_clean();
            header('Content-Type: text/html; charset=utf-8');
            redirectWithFlash('sitemap.xml wurde neu erstellt.', ['view' => 'settings']);
            break;
        case 'password':
            $new = (string) ($_POST['newPassword'] ?? '');
            if (!password_verify((string) ($_POST['currentPassword'] ?? ''), (string) config('admin.passwordHash', ''))) {
                redirectWithFlash('Aktuelles Passwort ist falsch.', ['view' => 'settings']);
            }
            if (mb_strlen($new) < 12) {
                redirectWithFlash('Das neue Passwort braucht mindestens 12 Zeichen.', ['view' => 'settings']);
            }
            updateConfig('admin.passwordHash', password_hash($new, PASSWORD_DEFAULT));
            redirectWithFlash('Passwort geändert.', ['view' => 'settings']);
            break;
        case 'purgeLogs':
            foreach (glob(LOG_PATH . '/visits-*.jsonl') ?: [] as $file) {
                unlink($file);
            }
            redirectWithFlash('Alle Logdateien wurden gelöscht.', ['view' => 'settings']);
            break;
    }
}

/* CSV-Export */
if (($_GET['export'] ?? '') !== '' && isset(RECORD_TYPES[$_GET['export']])) {
    checkCsrf();
    $type = (string) $_GET['export'];
    $records = readJson(dataFile($type), []);
    $columns = [];
    foreach ($records as $record) {
        $columns = array_unique(array_merge($columns, array_keys($record['fields'] ?? [])));
    }
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $type . '-' . date('Y-m-d') . '.csv"');
    $out = fopen('php://output', 'w');
    fwrite($out, "\xEF\xBB\xBF");
    fputcsv($out, array_merge(['id', 'createdAt', 'status'], $columns, ['notes']), ';');
    foreach ($records as $record) {
        $row = [$record['id'], $record['createdAt'], $record['status']];
        foreach ($columns as $column) {
            $value = $record['fields'][$column] ?? '';
            $value = is_array($value) ? implode(', ', $value) : (is_bool($value) ? ($value ? 'ja' : 'nein') : (string) $value);
            $row[] = preg_match('/^[=+\-@]/', $value) ? "'" . $value : $value;
        }
        $row[] = $record['notes'] ?? '';
        fputcsv($out, $row, ';');
    }
    fclose($out);
    exit;
}

/* ==========================================================================
   Analytics
   ========================================================================== */

/** Löscht Logdateien, die älter als die Aufbewahrungsfrist sind. */
function purgeOldLogs(): void
{
    $limit = date('Y-m', strtotime('-' . (int) config('analytics.retentionMonths', 14) . ' months'));
    foreach (glob(LOG_PATH . '/visits-*.jsonl') ?: [] as $file) {
        if (preg_match('/visits-(\d{4}-\d{2})\.jsonl$/', $file, $m) && $m[1] < $limit) {
            unlink($file);
        }
    }
}

function loadAnalytics(int $days): array
{
    $from = strtotime('today -' . ($days - 1) . ' days');
    $files = [];
    for ($t = strtotime(date('Y-m-01', $from)); $t <= time(); $t = strtotime('+1 month', $t)) {
        $files[] = logFile($t);
    }
    $views = [];
    $leaves = [];
    $bots = 0;
    foreach ($files as $file) {
        foreach (readJsonLines($file) as $row) {
            if (($row['ts'] ?? 0) < $from) {
                continue;
            }
            if (($row['t'] ?? '') === 'leave') {
                $id = $row['id'];
                $leaves[$id] = ['dur' => max($leaves[$id]['dur'] ?? 0, (int) $row['dur']), 'scroll' => max($leaves[$id]['scroll'] ?? 0, (int) $row['scroll'])];
            } elseif (($row['t'] ?? '') === 'pv') {
                if (!empty($row['bot']) && config('analytics.excludeBots', true)) {
                    $bots++;
                    continue;
                }
                $views[] = $row;
            }
        }
    }
    usort($views, fn ($a, $b) => $a['ts'] <=> $b['ts']);
    foreach ($views as &$view) {
        $view['dur'] = $leaves[$view['id']]['dur'] ?? null;
        $view['scroll'] = $leaves[$view['id']]['scroll'] ?? null;
    }
    unset($view);

    // Sitzungen: gleicher Besucher-Hash, max. X Minuten Pause
    $timeout = (int) config('analytics.sessionTimeoutMinutes', 30) * 60;
    $sessions = [];
    $open = [];
    foreach ($views as $view) {
        $vid = $view['vid'];
        if (isset($open[$vid]) && $view['ts'] - $sessions[$open[$vid]]['end'] <= $timeout) {
            $idx = $open[$vid];
        } else {
            $idx = count($sessions);
            $sessions[] = ['vid' => $vid, 'start' => $view['ts'], 'end' => $view['ts'], 'views' => [], 'ref' => $view['refHost'] ?? '', 'utm' => $view['utm'] ?? null, 'device' => $view['device'], 'browser' => $view['browser'], 'os' => $view['os']];
            $open[$vid] = $idx;
        }
        $sessions[$idx]['views'][] = $view;
        $sessions[$idx]['end'] = $view['ts'] + (int) ($view['dur'] ?? 0);
    }

    // Kennzahlen
    $perDay = [];
    for ($i = $days - 1; $i >= 0; $i--) {
        $perDay[date('Y-m-d', strtotime('today -' . $i . ' days'))] = ['views' => 0, 'visitors' => []];
    }
    $pages = [];
    $heat = array_fill(0, 7, array_fill(0, 24, 0));
    $notFound = [];
    foreach ($views as $view) {
        $day = date('Y-m-d', $view['ts']);
        if (isset($perDay[$day])) {
            $perDay[$day]['views']++;
            $perDay[$day]['visitors'][$view['vid']] = true;
        }
        $key = $view['status'] === 404 ? '404' : $view['path'];
        $pages[$key] ??= ['path' => $key, 'title' => $view['title'], 'views' => 0, 'visitors' => [], 'dur' => [], 'scroll' => [], 'entries' => 0, 'exits' => 0];
        $pages[$key]['views']++;
        $pages[$key]['visitors'][$view['vid']] = true;
        if ($view['dur'] !== null) {
            $pages[$key]['dur'][] = $view['dur'];
        }
        if ($view['scroll'] !== null) {
            $pages[$key]['scroll'][] = $view['scroll'];
        }
        $heat[(int) date('N', $view['ts']) - 1][(int) date('G', $view['ts'])]++;
        if ($view['status'] === 404) {
            $notFound[$view['path']] = ($notFound[$view['path']] ?? 0) + 1;
        }
    }
    $referrers = [];
    $campaigns = [];
    $devices = ['device' => [], 'browser' => [], 'os' => []];
    $bounces = 0;
    $durations = [];
    foreach ($sessions as $session) {
        $first = $session['views'][0];
        $last = end($session['views']);
        $pages[$first['status'] === 404 ? '404' : $first['path']]['entries']++;
        $pages[$last['status'] === 404 ? '404' : $last['path']]['exits']++;
        $ref = $session['ref'] !== '' ? $session['ref'] : 'Direkt / unbekannt';
        $referrers[$ref] = ($referrers[$ref] ?? 0) + 1;
        if (!empty($session['utm'])) {
            $label = implode(' · ', array_filter([$session['utm']['source'] ?? '', $session['utm']['medium'] ?? '', $session['utm']['campaign'] ?? '']));
            $campaigns[$label] = ($campaigns[$label] ?? 0) + 1;
        }
        foreach ($devices as $dim => $_) {
            $devices[$dim][$session[$dim]] = ($devices[$dim][$session[$dim]] ?? 0) + 1;
        }
        if (count($session['views']) === 1) {
            $bounces++;
        }
        $durations[] = $session['end'] - $session['start'];
    }
    arsort($referrers);
    arsort($campaigns);
    arsort($notFound);
    foreach ($devices as &$list) {
        arsort($list);
    }
    unset($list);
    foreach ($pages as &$page) {
        $page['visitors'] = count($page['visitors']);
        $page['avgDur'] = $page['dur'] ? array_sum($page['dur']) / count($page['dur']) : null;
        $page['avgScroll'] = $page['scroll'] ? array_sum($page['scroll']) / count($page['scroll']) : null;
        $page['exitRate'] = $page['views'] ? $page['exits'] / $page['views'] * 100 : 0;
    }
    unset($page);
    uasort($pages, fn ($a, $b) => $b['views'] <=> $a['views']);

    $allDur = array_filter(array_column($views, 'dur'), fn ($d) => $d !== null);
    $live = [];
    foreach (array_reverse($views) as $view) {
        if ($view['ts'] < time() - 300) {
            break;
        }
        $live[$view['vid']] ??= $view;
    }

    return [
        'views' => count($views),
        'visitors' => count(array_unique(array_column($views, 'vid'))),
        'sessions' => count($sessions),
        'bounceRate' => $sessions ? $bounces / count($sessions) * 100 : 0,
        'avgSession' => $durations ? array_sum($durations) / count($durations) : 0,
        'avgPageTime' => $allDur ? array_sum($allDur) / count($allDur) : 0,
        'pagesPerSession' => $sessions ? count($views) / count($sessions) : 0,
        'bots' => $bots,
        'perDay' => array_map(fn ($d) => ['views' => $d['views'], 'visitors' => count($d['visitors'])], $perDay),
        'pages' => $pages,
        'referrers' => $referrers,
        'campaigns' => $campaigns,
        'devices' => $devices,
        'heat' => $heat,
        'sessionList' => array_reverse($sessions),
        'live' => array_values($live),
        'notFound' => $notFound,
    ];
}

function formatDuration(?float $seconds): string
{
    if ($seconds === null) {
        return '–';
    }
    $seconds = (int) round($seconds);
    if ($seconds < 60) {
        return $seconds . ' s';
    }
    return floor($seconds / 60) . ' min ' . str_pad((string) ($seconds % 60), 2, '0', STR_PAD_LEFT) . ' s';
}

/** Säulendiagramm (eine Reihe, eine Achse) als SVG mit Hover-Tooltips. */
function barChart(array $perDay): string
{
    $w = 900;
    $h = 240;
    $padL = 36;
    $padB = 26;
    $padT = 12;
    $max = max(1, max(array_column($perDay, 'visitors')));
    $step = (int) ceil($max / 4);
    $top = $step * 4;
    $count = count($perDay);
    $slot = ($w - $padL) / max(1, $count);
    $barW = max(2, min(28, $slot - 2));
    $svg = '<svg class="chart" viewBox="0 0 ' . $w . ' ' . $h . '" role="img" aria-label="Besucher pro Tag">';
    for ($i = 0; $i <= 4; $i++) {
        $y = $padT + ($h - $padT - $padB) * (1 - $i / 4);
        $svg .= '<line class="chart-grid" x1="' . $padL . '" x2="' . $w . '" y1="' . $y . '" y2="' . $y . '"></line>';
        $svg .= '<text class="chart-axis" x="' . ($padL - 8) . '" y="' . ($y + 4) . '" text-anchor="end">' . ($step * $i) . '</text>';
    }
    $i = 0;
    $labelEvery = max(1, (int) ceil($count / 10));
    foreach ($perDay as $day => $values) {
        $x = $padL + $i * $slot + ($slot - $barW) / 2;
        $barH = ($h - $padT - $padB) * ($values['visitors'] / $top);
        $y = $h - $padB - $barH;
        $label = date('d.m.', strtotime($day));
        $tip = date('D d.m.Y', strtotime($day)) . ': ' . $values['visitors'] . ' Besucher, ' . $values['views'] . ' Aufrufe';
        $svg .= '<g class="chart-bar" data-tip="' . e($tip) . '"><rect class="chart-hit" x="' . ($padL + $i * $slot) . '" y="' . $padT . '" width="' . $slot . '" height="' . ($h - $padT - $padB) . '"></rect>';
        if ($values['visitors'] > 0) {
            $r = min(4, $barW / 2, $barH);
            $svg .= '<path class="chart-mark" d="M' . round($x, 1) . ' ' . ($h - $padB) . 'V' . round($y + $r, 1) . 'Q' . round($x, 1) . ' ' . round($y, 1) . ' ' . round($x + $r, 1) . ' ' . round($y, 1) . 'H' . round($x + $barW - $r, 1) . 'Q' . round($x + $barW, 1) . ' ' . round($y, 1) . ' ' . round($x + $barW, 1) . ' ' . round($y + $r, 1) . 'V' . ($h - $padB) . 'Z"></path>';
        }
        $svg .= '<title>' . e($tip) . '</title></g>';
        if ($i % $labelEvery === 0) {
            $svg .= '<text class="chart-axis" x="' . round($x + $barW / 2, 1) . '" y="' . ($h - 6) . '" text-anchor="middle">' . $label . '</text>';
        }
        $i++;
    }
    $svg .= '<line class="chart-base" x1="' . $padL . '" x2="' . $w . '" y1="' . ($h - $padB) . '" y2="' . ($h - $padB) . '"></line>';
    return $svg . '</svg>';
}

/** Heatmap Wochentag × Stunde (sequenziell, eine Farbe). */
function heatmap(array $heat): string
{
    $days = ['Mo', 'Di', 'Mi', 'Do', 'Fr', 'Sa', 'So'];
    $max = 1;
    foreach ($heat as $row) {
        $max = max($max, max($row));
    }
    $html = '<div class="heatmap" role="table" aria-label="Seitenaufrufe nach Wochentag und Stunde"><span></span>';
    for ($h = 0; $h < 24; $h++) {
        $html .= '<span class="heat-hour">' . ($h % 3 === 0 ? $h : '') . '</span>';
    }
    foreach ($heat as $d => $row) {
        $html .= '<span class="heat-day">' . $days[$d] . '</span>';
        foreach ($row as $h => $value) {
            $level = $value === 0 ? 0 : (int) ceil($value / $max * 5);
            $html .= '<span class="heat-cell heat-' . $level . '" data-tip="' . e($days[$d] . ' ' . $h . '–' . ($h + 1) . ' Uhr: ' . $value . ' Aufrufe') . '" title="' . e($days[$d] . ' ' . $h . ' Uhr: ' . $value) . '"></span>';
        }
    }
    return $html . '</div><div class="heat-legend"><span>wenig</span><i class="heat-1"></i><i class="heat-2"></i><i class="heat-3"></i><i class="heat-4"></i><i class="heat-5"></i><span>viel</span></div>';
}

/** Rangliste mit Balken. */
function rankList(array $items, string $empty = 'Noch keine Daten.', int $limit = 10): string
{
    if (!$items) {
        return '<p class="empty">' . e($empty) . '</p>';
    }
    $items = array_slice($items, 0, $limit, true);
    $max = max($items);
    $total = array_sum($items);
    $html = '<ul class="rank">';
    foreach ($items as $label => $value) {
        $html .= '<li><span class="rank-bar"><i data-w="' . round($value / $max * 100) . '"></i></span><span class="rank-label">' . e((string) $label) . '</span><span class="rank-value">' . formatNumber($value) . ' <small>' . round($value / max(1, $total) * 100) . ' %</small></span></li>';
    }
    return $html . '</ul>';
}

/* ==========================================================================
   Ausgabe
   ========================================================================== */

purgeOldLogs();
$view = (string) ($_GET['view'] ?? 'dashboard');
$range = in_array((int) ($_GET['range'] ?? 30), [1, 7, 30, 90, 365], true) ? (int) ($_GET['range'] ?? 30) : 30;
$needsAnalytics = in_array($view, ['dashboard', 'pages', 'sources', 'sessions', 'tech'], true);
$stats = $needsAnalytics ? loadAnalytics($range) : null;
$counts = [];
foreach (RECORD_TYPES as $type => $meta) {
    $records = readJson(dataFile($type), []);
    $counts[$type] = ['total' => count($records), 'new' => count(array_filter($records, fn ($r) => ($r['status'] ?? '') === 'neu'))];
}
$nav = [
    'dashboard' => ['Übersicht', 'chart'],
    'pages' => ['Seiten', 'layers'],
    'sources' => ['Quellen', 'link'],
    'sessions' => ['Besucher', 'users'],
    'tech' => ['Technik', 'laptop'],
    'records' => ['Datensätze', 'form'],
    'settings' => ['Einstellungen', 'sliders'],
];
$csrf = adminCsrf();
$rangeLinks = [1 => 'Heute', 7 => '7 Tage', 30 => '30 Tage', 90 => '90 Tage', 365 => '12 Monate'];

function adminHead(string $title): void
{
    ?><!doctype html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title><?= e($title) ?> · Admin · <?= e(config('site.name')) ?></title>
    <link rel="icon" href="<?= e(imgSrc('icon.favicon')) ?>" type="image/svg+xml">
    <link rel="stylesheet" href="<?= e(asset('css/admin.css')) ?>">
    <script src="<?= e(asset('js/admin.js')) ?>" defer></script>
</head>
<?php
}

function renderAdminLogin(string $error): void
{
    adminHead('Anmelden');
    $locked = loginLocked();
    ?>
<body class="admin-login">
    <main class="login-card">
        <?= img('brand.wordmark', ['class' => 'login-logo']) ?>
        <h1>Verwaltung</h1>
        <p>Bitte melde dich an, um Statistiken und Anfragen zu sehen.</p>
<?php if ($error): ?>
        <p class="alert alert-error"><?= e($error) ?></p>
<?php endif; ?>
        <form method="post" action="<?= e(adminUrl()) ?>">
            <input type="hidden" name="do" value="login">
            <label>Benutzername<input type="text" name="username" autocomplete="username" required autofocus></label>
            <label>Passwort<input type="password" name="password" autocomplete="current-password" required></label>
            <button class="btn" type="submit"<?= $locked ? ' disabled' : '' ?>>Anmelden</button>
        </form>
        <a class="back" href="<?= e(url()) ?>">← Zur Website</a>
    </main>
</body>
</html>
<?php
}

adminHead($nav[$view][0] ?? 'Admin');
?>
<body class="admin">
<aside class="sidebar">
    <a class="sidebar-brand" href="<?= e(adminUrl()) ?>"><?= img('brand.wordmarkLight', ['alt' => config('site.name')]) ?><small>Admin</small></a>
    <nav>
<?php foreach ($nav as $key => [$label, $iconName]): ?>
        <a class="<?= $view === $key ? 'is-active' : '' ?>" href="<?= e(adminUrl(['view' => $key, 'range' => $range])) ?>"><?= icon($iconName) ?><?= e($label) ?>
<?php if ($key === 'records' && array_sum(array_column($counts, 'new'))): ?><span class="badge"><?= array_sum(array_column($counts, 'new')) ?></span><?php endif; ?>
        </a>
<?php endforeach; ?>
    </nav>
    <div class="sidebar-foot">
        <a href="<?= e(url()) ?>" target="_blank" rel="noopener"><?= icon('external') ?>Website öffnen</a>
        <a href="<?= e(adminUrl(['do' => 'logout'])) ?>"><?= icon('logout') ?>Abmelden</a>
    </div>
</aside>

<main class="content">
    <header class="topbar">
        <h1><?= e($nav[$view][0] ?? 'Admin') ?></h1>
<?php if ($needsAnalytics): ?>
        <nav class="range" aria-label="Zeitraum">
<?php foreach ($rangeLinks as $value => $label): ?>
            <a class="<?= $range === $value ? 'is-active' : '' ?>" href="<?= e(adminUrl(['view' => $view, 'range' => $value])) ?>"><?= e($label) ?></a>
<?php endforeach; ?>
        </nav>
<?php endif; ?>
    </header>
<?php if ($flash): ?>
    <p class="alert alert-success"><?= e($flash) ?></p>
<?php endif; ?>

<?php if ($view === 'dashboard'): ?>
    <section class="kpis">
        <div class="kpi"><small>Besucher</small><strong><?= formatNumber($stats['visitors']) ?></strong></div>
        <div class="kpi"><small>Seitenaufrufe</small><strong><?= formatNumber($stats['views']) ?></strong></div>
        <div class="kpi"><small>Sitzungen</small><strong><?= formatNumber($stats['sessions']) ?></strong></div>
        <div class="kpi"><small>Ø Sitzungsdauer</small><strong><?= formatDuration($stats['avgSession']) ?></strong></div>
        <div class="kpi"><small>Seiten / Sitzung</small><strong><?= formatNumber($stats['pagesPerSession'], 1) ?></strong></div>
        <div class="kpi"><small>Absprungrate</small><strong><?= formatNumber($stats['bounceRate'], 0) ?> %</strong></div>
        <div class="kpi kpi-live"><small><span class="live-dot"></span>Jetzt aktiv</small><strong><?= count($stats['live']) ?></strong></div>
    </section>

    <section class="panel">
        <div class="panel-head"><h2>Besucher pro Tag</h2><small>Bots ausgeblendet (<?= formatNumber($stats['bots']) ?>)</small></div>
        <div class="chart-wrap"><?= barChart($stats['perDay']) ?><div class="chart-tip" hidden></div></div>
    </section>

    <div class="grid-2">
        <section class="panel">
            <div class="panel-head"><h2>Meistbesuchte Seiten</h2><a href="<?= e(adminUrl(['view' => 'pages', 'range' => $range])) ?>">Alle</a></div>
            <?= rankList(array_map(fn ($p) => $p['views'], $stats['pages'])) ?>
        </section>
        <section class="panel">
            <div class="panel-head"><h2>Beste Referrer</h2><a href="<?= e(adminUrl(['view' => 'sources', 'range' => $range])) ?>">Alle</a></div>
            <?= rankList($stats['referrers']) ?>
        </section>
        <section class="panel">
            <div class="panel-head"><h2>Ausstiegsseiten</h2></div>
            <?php $exits = array_filter(array_map(fn ($p) => $p['exits'], $stats['pages'])); arsort($exits); ?>
            <?= rankList($exits) ?>
        </section>
        <section class="panel">
            <div class="panel-head"><h2>Gerade auf der Seite</h2><small>letzte 5 Minuten</small></div>
<?php if ($stats['live']): ?>
            <ul class="live-list">
<?php foreach ($stats['live'] as $item): ?>
                <li><span class="live-dot"></span><code><?= e(substr($item['vid'], 0, 6)) ?></code><span><?= e($item['path']) ?></span><small><?= e($item['device']) ?> · <?= date('H:i', $item['ts']) ?></small></li>
<?php endforeach; ?>
            </ul>
<?php else: ?>
            <p class="empty">Gerade niemand online.</p>
<?php endif; ?>
        </section>
    </div>

    <section class="panel">
        <div class="panel-head"><h2>Wann sind Besucher da?</h2><small>Seitenaufrufe nach Wochentag &amp; Uhrzeit</small></div>
        <?= heatmap($stats['heat']) ?>
    </section>

    <section class="panel">
        <div class="panel-head"><h2>Neue Datensätze</h2><a href="<?= e(adminUrl(['view' => 'records'])) ?>">Verwalten</a></div>
        <div class="record-tiles">
<?php foreach (RECORD_TYPES as $type => $meta): ?>
            <a class="record-tile" href="<?= e(adminUrl(['view' => 'records', 'type' => $type])) ?>"><?= icon($meta['icon']) ?><strong><?= $counts[$type]['total'] ?></strong><span><?= e($meta['label']) ?></span><?php if ($counts[$type]['new']): ?><em><?= $counts[$type]['new'] ?> neu</em><?php endif; ?></a>
<?php endforeach; ?>
        </div>
    </section>

<?php elseif ($view === 'pages'): ?>
    <section class="panel">
        <div class="panel-head"><h2>Alle Seiten</h2><input class="table-filter" type="search" placeholder="Filtern …" data-filter="#pagesTable"></div>
        <div class="table-wrap">
            <table id="pagesTable">
                <thead><tr><th>Seite</th><th class="num">Aufrufe</th><th class="num">Besucher</th><th class="num">Ø Verweildauer</th><th class="num">Ø Scrolltiefe</th><th class="num">Einstiege</th><th class="num">Ausstiege</th><th class="num">Ausstiegsrate</th></tr></thead>
                <tbody>
<?php foreach ($stats['pages'] as $p): ?>
                    <tr><td><strong><?= e($p['title']) ?></strong><br><code><?= e($p['path']) ?></code></td><td class="num"><?= formatNumber($p['views']) ?></td><td class="num"><?= formatNumber($p['visitors']) ?></td><td class="num"><?= formatDuration($p['avgDur']) ?></td><td class="num"><?= $p['avgScroll'] === null ? '–' : round($p['avgScroll']) . ' %' ?></td><td class="num"><?= formatNumber($p['entries']) ?></td><td class="num"><?= formatNumber($p['exits']) ?></td><td class="num"><?= formatNumber($p['exitRate'], 0) ?> %</td></tr>
<?php endforeach; ?>
<?php if (!$stats['pages']): ?>
                    <tr><td colspan="8" class="empty">Noch keine Daten im gewählten Zeitraum.</td></tr>
<?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>
<?php if ($stats['notFound']): ?>
    <section class="panel">
        <div class="panel-head"><h2>Nicht gefundene Seiten (404)</h2></div>
        <?= rankList($stats['notFound']) ?>
    </section>
<?php endif; ?>

<?php elseif ($view === 'sources'): ?>
    <div class="grid-2">
        <section class="panel">
            <div class="panel-head"><h2>Referrer</h2><small>Sitzungen nach Herkunft</small></div>
            <?= rankList($stats['referrers'], 'Noch keine Daten.', 50) ?>
        </section>
        <section class="panel">
            <div class="panel-head"><h2>Kampagnen (UTM)</h2><small>source · medium · campaign</small></div>
            <?= rankList($stats['campaigns'], 'Keine Kampagnen-Links genutzt.', 50) ?>
        </section>
    </div>

<?php elseif ($view === 'sessions'): ?>
    <section class="panel">
        <div class="panel-head"><h2>Besucherverläufe</h2><small>Wer war wann auf welcher Seite – anonymisiert, ohne Cookies</small></div>
<?php if (!$stats['sessionList']): ?>
        <p class="empty">Noch keine Daten im gewählten Zeitraum.</p>
<?php endif; ?>
        <ol class="sessions">
<?php foreach (array_slice($stats['sessionList'], 0, 80) as $session): ?>
            <li class="session">
                <div class="session-head">
                    <code title="Anonymer Tages-Hash"><?= e(substr($session['vid'], 0, 6)) ?></code>
                    <strong><?= date('d.m.Y H:i', $session['start']) ?></strong>
                    <span><?= count($session['views']) ?> Seite(n) · <?= formatDuration($session['end'] - $session['start']) ?></span>
                    <span class="muted"><?= e($session['device']) ?> · <?= e($session['browser']) ?> · <?= e($session['os']) ?></span>
                    <span class="muted">via <?= e($session['ref'] ?: 'direkt') ?></span>
                </div>
                <ol class="journey">
<?php foreach ($session['views'] as $v): ?>
                    <li><time><?= date('H:i:s', $v['ts']) ?></time><span class="<?= $v['status'] === 404 ? 'is-404' : '' ?>"><?= e($v['title']) ?></span><small><?= formatDuration($v['dur']) ?><?= $v['scroll'] !== null ? ' · ' . $v['scroll'] . ' %' : '' ?></small></li>
<?php endforeach; ?>
                </ol>
            </li>
<?php endforeach; ?>
        </ol>
    </section>

<?php elseif ($view === 'tech'): ?>
    <div class="grid-3">
        <section class="panel"><div class="panel-head"><h2>Geräte</h2></div><?= rankList($stats['devices']['device']) ?></section>
        <section class="panel"><div class="panel-head"><h2>Browser</h2></div><?= rankList($stats['devices']['browser']) ?></section>
        <section class="panel"><div class="panel-head"><h2>Betriebssysteme</h2></div><?= rankList($stats['devices']['os']) ?></section>
    </div>
    <section class="panel">
        <div class="panel-head"><h2>Hinweis</h2></div>
        <p class="muted">Gezählt werden Sitzungen. Bots und Crawler (<?= formatNumber($stats['bots']) ?> Aufrufe im Zeitraum) sind ausgeblendet. IP-Adressen werden nicht gespeichert; der Besucher-Hash wechselt täglich.</p>
    </section>

<?php elseif ($view === 'records'): ?>
<?php
    $type = isset(RECORD_TYPES[$_GET['type'] ?? '']) ? (string) $_GET['type'] : array_key_first(RECORD_TYPES);
    $status = (string) ($_GET['status'] ?? '');
    $records = array_reverse(readJson(dataFile($type), []));
    if ($status !== '') {
        $records = array_values(array_filter($records, fn ($r) => ($r['status'] ?? '') === $status));
    }
?>
    <nav class="tabs">
<?php foreach (RECORD_TYPES as $key => $meta): ?>
        <a class="<?= $key === $type ? 'is-active' : '' ?>" href="<?= e(adminUrl(['view' => 'records', 'type' => $key])) ?>"><?= icon($meta['icon']) ?><?= e($meta['label']) ?> <span><?= $counts[$key]['total'] ?></span></a>
<?php endforeach; ?>
    </nav>
    <section class="panel">
        <div class="panel-head">
            <div class="filters">
                <a class="<?= $status === '' ? 'is-active' : '' ?>" href="<?= e(adminUrl(['view' => 'records', 'type' => $type])) ?>">Alle</a>
<?php foreach (RECORD_STATUS as $key => $label): ?>
                <a class="<?= $status === $key ? 'is-active' : '' ?>" href="<?= e(adminUrl(['view' => 'records', 'type' => $type, 'status' => $key])) ?>"><?= e($label) ?></a>
<?php endforeach; ?>
            </div>
            <div class="panel-actions">
                <input class="table-filter" type="search" placeholder="Suchen …" data-filter-records>
                <a class="btn btn-sm" href="<?= e(adminUrl(['export' => $type, 'csrf' => $csrf])) ?>"><?= icon('download') ?>CSV</a>
            </div>
        </div>
<?php if (!$records): ?>
        <p class="empty">Keine Datensätze vorhanden.</p>
<?php endif; ?>
        <div class="records">
<?php foreach ($records as $record): $f = $record['fields'] ?? []; ?>
            <details class="record status-<?= e($record['status']) ?>" data-record>
                <summary>
                    <span class="record-status"><?= e(RECORD_STATUS[$record['status']] ?? $record['status']) ?></span>
                    <strong><?= e($f['name'] ?? '' ?: ($f['email'] ?? '–')) ?></strong>
                    <span class="muted"><?= e($f['email'] ?? '') ?></span>
                    <span class="muted"><?= e($f['form'] ?? (CONTACT_TOPICS[$f['topic'] ?? ''] ?? (DONATION_TYPES[$f['type'] ?? '']['label'] ?? ''))) ?></span>
                    <time><?= date('d.m.Y H:i', strtotime($record['createdAt'])) ?></time>
                </summary>
                <div class="record-body">
                    <dl>
<?php foreach ($f as $key => $value): if ($value === '' || $value === null || $value === []) continue; ?>
                        <div><dt><?= e($key) ?></dt><dd><?= nl2br(e(is_array($value) ? implode(', ', $value) : (is_bool($value) ? ($value ? 'ja' : 'nein') : (string) $value))) ?></dd></div>
<?php endforeach; ?>
                        <div><dt>ID</dt><dd><code><?= e($record['id']) ?></code></dd></div>
                        <div><dt>Herkunft</dt><dd><?= e(($record['meta']['page'] ?? '') ?: 'start') ?> · <?= e(implode(' · ', (array) ($record['meta']['client'] ?? []))) ?></dd></div>
                    </dl>
                    <form method="post" action="<?= e(adminUrl()) ?>" class="record-form">
                        <input type="hidden" name="csrf" value="<?= e($csrf) ?>">
                        <input type="hidden" name="type" value="<?= e($type) ?>">
                        <input type="hidden" name="id" value="<?= e($record['id']) ?>">
                        <label>Status
                            <select name="status">
<?php foreach (RECORD_STATUS as $key => $label): ?>
                                <option value="<?= e($key) ?>"<?= $record['status'] === $key ? ' selected' : '' ?>><?= e($label) ?></option>
<?php endforeach; ?>
                            </select>
                        </label>
                        <label>Notiz<textarea name="notes" rows="3"><?= e($record['notes'] ?? '') ?></textarea></label>
                        <div class="record-actions">
<?php if (!empty($f['email'])): ?>
                            <a class="btn btn-ghost btn-sm" href="mailto:<?= e($f['email']) ?>"><?= icon('mail') ?>Antworten</a>
<?php endif; ?>
                            <button class="btn btn-danger btn-sm" type="submit" name="do" value="recordDelete" data-confirm="Datensatz endgültig löschen?"><?= icon('trash') ?>Löschen</button>
                            <button class="btn btn-sm" type="submit" name="do" value="recordUpdate"><?= icon('check') ?>Speichern</button>
                        </div>
                    </form>
                </div>
            </details>
<?php endforeach; ?>
        </div>
    </section>

<?php elseif ($view === 'settings'): ?>
    <div class="grid-2">
        <section class="panel">
            <div class="panel-head"><h2>Sitemap</h2></div>
            <p class="muted">Erstellt <code>sitemap.xml</code> im Hauptverzeichnis neu (alle Routen aus <code>app.php</code>).</p>
            <form method="post" action="<?= e(adminUrl()) ?>">
                <input type="hidden" name="csrf" value="<?= e($csrf) ?>">
                <button class="btn" type="submit" name="do" value="sitemap"><?= icon('refresh') ?>Sitemap erstellen</button>
                <a class="btn btn-ghost" href="<?= e(url('sitemap.xml')) ?>" target="_blank" rel="noopener">Ansehen</a>
            </form>
        </section>
        <section class="panel">
            <div class="panel-head"><h2>Passwort ändern</h2></div>
            <form method="post" action="<?= e(adminUrl()) ?>" class="stack">
                <input type="hidden" name="csrf" value="<?= e($csrf) ?>">
                <label>Aktuelles Passwort<input type="password" name="currentPassword" autocomplete="current-password" required></label>
                <label>Neues Passwort (mind. 12 Zeichen)<input type="password" name="newPassword" autocomplete="new-password" minlength="12" required></label>
                <button class="btn" type="submit" name="do" value="password"><?= icon('lock') ?>Speichern</button>
            </form>
        </section>
        <section class="panel">
            <div class="panel-head"><h2>Systemstatus</h2></div>
            <dl class="status-list">
                <div><dt>PHP</dt><dd><?= e(PHP_VERSION) ?></dd></div>
                <div><dt>BASE_URL</dt><dd><code><?= e(BASE_URL) ?></code></dd></div>
                <div><dt>data/ beschreibbar</dt><dd><?= is_writable(DATA_PATH) ? 'ja' : '<strong class="bad">nein</strong>' ?></dd></div>
                <div><dt>PHPMailer</dt><dd><?= class_exists('PHPMailer\\PHPMailer\\PHPMailer') ? 'installiert' : 'fehlt (composer install)' ?></dd></div>
                <div><dt>SMTP</dt><dd><?= config('smtp.enabled') ? 'aktiv' : 'aus (mail()-Fallback)' ?></dd></div>
                <div><dt>Mesh-Chat</dt><dd><?= config('mesh.chat.enabled') ? 'live' : 'Demo-Modus' ?></dd></div>
                <div><dt>Logdateien</dt><dd><?= count(glob(LOG_PATH . '/visits-*.jsonl') ?: []) ?> Monat(e), Aufbewahrung <?= (int) config('analytics.retentionMonths', 14) ?> Monate</dd></div>
            </dl>
        </section>
        <section class="panel">
            <div class="panel-head"><h2>Logdateien löschen</h2></div>
            <p class="muted">Entfernt alle Besucher-Logs unwiderruflich. Datensätze bleiben erhalten.</p>
            <form method="post" action="<?= e(adminUrl()) ?>">
                <input type="hidden" name="csrf" value="<?= e($csrf) ?>">
                <button class="btn btn-danger" type="submit" name="do" value="purgeLogs" data-confirm="Wirklich alle Logdateien löschen?"><?= icon('trash') ?>Alle Logs löschen</button>
            </form>
        </section>
    </div>
<?php endif; ?>
</main>
</body>
</html>
