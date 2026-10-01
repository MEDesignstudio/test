<?php
/**
 * GemeinsamKI – controller.php
 * Verarbeitet alle AJAX-Aufrufe (Formulare, Tracking-Beacon, Chat).
 * Ohne JavaScript abgeschickte Formulare werden mit ?danke= bzw. ?fehler= zurückgeleitet.
 */
declare(strict_types=1);

require __DIR__ . '/app.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['ok' => false, 'message' => 'Nur POST erlaubt.'], 405);
}

$action = (string) ($_POST['action'] ?? '');
$isAjax = ($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') !== '' || str_contains((string) ($_SERVER['HTTP_ACCEPT'] ?? ''), 'application/json');

/** Antwort für Formulare: JSON (AJAX) oder Redirect (ohne JS). */
function respondForm(string $action, bool $ok, array $data, bool $isAjax): void
{
    if ($isAjax) {
        jsonResponse(array_merge(['ok' => $ok], $data), $ok ? 200 : 422);
    }
    $route = (string) ($_POST['page'] ?? '');
    $route = routeExists($route) ? $route : '';
    header('Location: ' . url($route, [$ok ? 'danke' : 'fehler' => $action]), true, 303);
    exit;
}

/** Gemeinsame Schutzprüfungen für alle Formulare. */
function guardForm(string $action, bool $isAjax): void
{
    if (trim((string) ($_POST['website'] ?? '')) !== '') {
        // Honeypot ausgefüllt: so tun, als wäre alles gut.
        respondForm($action, true, ['title' => 'Danke!', 'message' => 'Deine Nachricht ist angekommen.'], $isAjax);
    }
    $tokenError = verifyFormToken((string) ($_POST['token'] ?? ''));
    if ($tokenError !== null) {
        respondForm($action, false, ['message' => $tokenError], $isAjax);
    }
    $limit = (int) config('security.formRateLimit', 5);
    $window = (int) config('security.formRateWindowMinutes', 10);
    if (!rateLimit('form:' . visitorId(), $limit, $window)) {
        respondForm($action, false, ['message' => 'Zu viele Anfragen. Bitte versuche es in ein paar Minuten erneut.'], $isAjax);
    }
}

function requireEmail(array &$errors): string
{
    $email = cleanText($_POST['email'] ?? '', 120);
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Bitte gib eine gültige E-Mail-Adresse an.';
    }
    return $email;
}

function requirePrivacy(array &$errors): void
{
    if (empty($_POST['privacy'])) {
        $errors['privacy'] = 'Bitte stimme der Datenschutzerklärung zu.';
    }
}

function pick(string $value, array $allowed, string $default): string
{
    return array_key_exists($value, $allowed) ? $value : $default;
}

/** Benachrichtigung an das Team + Bestätigung an die anfragende Person. */
function notify(string $subject, string $intro, array $rows, string $email, string $confirmTitle, string $confirmText): void
{
    $admin = (string) config('notifications.adminEmail', '');
    if ($admin !== '') {
        sendMail($admin, '[' . config('site.name') . '] ' . $subject, mailTemplate($subject, e($intro), $rows), $email);
    }
    if (config('notifications.sendConfirmation', true) && $email !== '') {
        sendMail($email, $confirmTitle . ' – ' . config('site.name'), mailTemplate($confirmTitle, e($confirmText), []));
    }
}

switch ($action) {
    /* ---- Beta-Anmeldung (ausführlich) -------------------------------- */
    case 'register':
    case 'quickRegister':
        guardForm($action, $isAjax);
        $errors = [];
        $email = requireEmail($errors);
        requirePrivacy($errors);
        if ($errors) {
            respondForm($action, false, ['message' => 'Bitte prüfe die markierten Angaben.', 'errors' => $errors], $isAjax);
        }
        $chip = (string) ($_POST['chip'] ?? '');
        $usage = array_values(array_intersect((array) ($_POST['usage'] ?? []), ['chat', 'vscode', 'api']));
        $fields = [
            'form' => $action === 'quickRegister' ? 'Schnellanmeldung' : 'Beta-Anmeldung',
            'role' => pick((string) ($_POST['role'] ?? ''), JOIN_ROLES, 'beides'),
            'name' => cleanText($_POST['name'] ?? '', 80),
            'email' => $email,
            'chip' => (array_key_exists($chip, MAC_CHIPS) || in_array($chip, ['intel', 'keiner'], true)) ? $chip : '',
            'ram' => in_array((int) ($_POST['ram'] ?? 0), MAC_RAM, true) ? (int) $_POST['ram'] : null,
            'schedule' => pick((string) ($_POST['schedule'] ?? ''), JOIN_SCHEDULES, ''),
            'usage' => $usage,
            'city' => cleanText($_POST['city'] ?? '', 60),
            'message' => cleanText($_POST['message'] ?? '', 1500),
            'newsletter' => !empty($_POST['newsletter']),
        ];
        $duplicate = false;
        foreach (readJson(dataFile('registrations'), []) as $existing) {
            if (strcasecmp($existing['fields']['email'] ?? '', $email) === 0) {
                $duplicate = true;
                break;
            }
        }
        if (!$duplicate) {
            $record = storeRecord('registrations', $fields);
            notify(
                'Neue Beta-Anmeldung',
                'Eine neue Anmeldung ist eingegangen (' . $record['id'] . ').',
                [
                    'Rolle' => JOIN_ROLES[$fields['role']]['label'],
                    'Name' => $fields['name'],
                    'E-Mail' => $email,
                    'Mac' => trim($fields['chip'] . ' ' . ($fields['ram'] ? $fields['ram'] . ' GB' : '')),
                    'Zeitplan' => JOIN_SCHEDULES[$fields['schedule']] ?? '',
                    'Nutzung' => implode(', ', $usage),
                    'Ort' => $fields['city'],
                    'Nachricht' => $fields['message'],
                ],
                $email,
                'Willkommen bei GemeinsamKI',
                'Danke für deine Anmeldung! Du stehst auf der Liste für die Beta. Wir melden uns, sobald es losgeht.'
            );
        }
        respondForm($action, true, [
            'title' => $duplicate ? 'Du bist schon dabei!' : 'Willkommen im Mesh!',
            'message' => $duplicate ? 'Diese E-Mail-Adresse steht bereits auf unserer Liste. Wir melden uns, sobald es losgeht.' : 'Dein Beta-Platz ist gesichert. Wir melden uns, sobald es losgeht.',
        ], $isAjax);
        break;

    /* ---- Kontaktformular ------------------------------------------------ */
    case 'contact':
        guardForm($action, $isAjax);
        $errors = [];
        $email = requireEmail($errors);
        requirePrivacy($errors);
        $name = cleanText($_POST['name'] ?? '', 80);
        $message = cleanText($_POST['message'] ?? '', 3000);
        if ($name === '') {
            $errors['name'] = 'Bitte gib deinen Namen an.';
        }
        if (mb_strlen($message) < 5) {
            $errors['message'] = 'Bitte schreib uns ein paar Worte.';
        }
        if ($errors) {
            respondForm($action, false, ['message' => 'Bitte prüfe die markierten Angaben.', 'errors' => $errors], $isAjax);
        }
        $fields = [
            'topic' => pick((string) ($_POST['topic'] ?? ''), CONTACT_TOPICS, 'sonstiges'),
            'name' => $name,
            'email' => $email,
            'phone' => cleanText($_POST['phone'] ?? '', 40),
            'organization' => cleanText($_POST['organization'] ?? '', 100),
            'message' => $message,
        ];
        $record = storeRecord('contacts', $fields);
        notify(
            'Neue Kontaktanfrage: ' . CONTACT_TOPICS[$fields['topic']],
            'Eine neue Kontaktanfrage ist eingegangen (' . $record['id'] . ').',
            ['Thema' => CONTACT_TOPICS[$fields['topic']], 'Name' => $name, 'E-Mail' => $email, 'Telefon' => $fields['phone'], 'Organisation' => $fields['organization'], 'Nachricht' => $message],
            $email,
            'Danke für deine Nachricht',
            'Wir haben deine Nachricht erhalten und melden uns so schnell wie möglich bei dir.'
        );
        respondForm($action, true, ['title' => 'Nachricht gesendet!', 'message' => 'Danke, ' . $name . '! Wir melden uns so schnell wie möglich.'], $isAjax);
        break;

    /* ---- Spenden-/Förderanfrage --------------------------------------- */
    case 'donate':
        guardForm($action, $isAjax);
        $errors = [];
        $email = requireEmail($errors);
        requirePrivacy($errors);
        $name = cleanText($_POST['name'] ?? '', 80);
        if ($name === '') {
            $errors['name'] = 'Bitte gib deinen Namen an.';
        }
        $preset = (string) ($_POST['amountPreset'] ?? '');
        $amount = $preset === 'frei' ? (int) ($_POST['amount'] ?? 0) : (int) $preset;
        $type = pick((string) ($_POST['type'] ?? ''), DONATION_TYPES, 'spende');
        if ($type !== 'hardware' && ($amount < 1 || $amount > 1000000)) {
            $errors['amount'] = 'Bitte gib einen Betrag an.';
        }
        if ($errors) {
            respondForm($action, false, ['message' => 'Bitte prüfe die markierten Angaben.', 'errors' => $errors], $isAjax);
        }
        $fields = [
            'type' => $type,
            'amount' => $type === 'hardware' ? null : $amount,
            'interval' => in_array($_POST['interval'] ?? '', ['einmalig', 'monatlich', 'jaehrlich'], true) ? $_POST['interval'] : 'einmalig',
            'name' => $name,
            'email' => $email,
            'organization' => cleanText($_POST['organization'] ?? '', 100),
            'message' => cleanText($_POST['message'] ?? '', 2000),
            'receipt' => !empty($_POST['receipt']),
        ];
        $record = storeRecord('donations', $fields);
        notify(
            'Neue Unterstützungsanfrage: ' . DONATION_TYPES[$type]['label'],
            'Eine neue Anfrage ist eingegangen (' . $record['id'] . ').',
            ['Art' => DONATION_TYPES[$type]['label'], 'Betrag' => $fields['amount'] ? $fields['amount'] . ' € (' . $fields['interval'] . ')' : '–', 'Name' => $name, 'E-Mail' => $email, 'Organisation' => $fields['organization'], 'Zuwendungsbestätigung' => $fields['receipt'] ? 'ja' : 'nein', 'Nachricht' => $fields['message']],
            $email,
            'Danke für deine Unterstützung',
            'Wir haben deine Anfrage erhalten und melden uns persönlich mit allen Details.'
        );
        respondForm($action, true, ['title' => 'Von Herzen danke!', 'message' => 'Wir melden uns persönlich bei dir – mit allen Details.'], $isAjax);
        break;

    /* ---- Verweildauer & Scrolltiefe ------------------------------------ */
    case 'track':
        trackPageLeave((string) ($_POST['pv'] ?? ''), (int) ($_POST['t'] ?? 0), (int) ($_POST['s'] ?? 0));
        http_response_code(204);
        exit;

    /* ---- Chat ------------------------------------------------------------- */
    case 'chat':
        $tokenError = verifyFormToken((string) ($_POST['token'] ?? ''));
        if ($tokenError !== null && !str_contains($tokenError, 'schnell')) {
            jsonResponse(['ok' => false, 'message' => $tokenError], 400);
        }
        $chat = config('mesh.chat', []);
        if (!rateLimit('chat:' . visitorId(), (int) ($chat['rateLimit'] ?? 20), (int) ($chat['rateWindowMinutes'] ?? 10))) {
            jsonResponse(['ok' => false, 'message' => 'Du schreibst gerade sehr viel – gönn dem Mesh eine kurze Pause.'], 429);
        }
        $messages = json_decode((string) ($_POST['messages'] ?? '[]'), true);
        $clean = [];
        foreach (array_slice(is_array($messages) ? $messages : [], -12) as $message) {
            $role = ($message['role'] ?? '') === 'assistant' ? 'assistant' : 'user';
            $content = cleanText($message['content'] ?? '', 2000);
            if ($content !== '') {
                $clean[] = ['role' => $role, 'content' => $content];
            }
        }
        if (!$clean) {
            jsonResponse(['ok' => false, 'message' => 'Bitte gib eine Nachricht ein.'], 422);
        }

        // Demo-Modus: Antworten aus data/chatDemo.json
        if (empty($chat['enabled']) || isPlaceholder($chat['apiUrl'] ?? '') || empty($chat['apiUrl'])) {
            $demo = readJson(dataFile('chatDemo'), []);
            $question = mb_strtolower(end($clean)['content']);
            $reply = $demo['default'] ?? 'Demo-Modus.';
            $best = 0;
            foreach ($demo['answers'] ?? [] as $answer) {
                $score = 0;
                foreach ($answer['keywords'] as $keyword) {
                    if (str_contains($question, mb_strtolower($keyword))) {
                        $score += mb_strlen($keyword);
                    }
                }
                if ($score > $best) {
                    $best = $score;
                    $reply = $answer['answer'];
                }
            }
            jsonResponse(['ok' => true, 'reply' => $reply, 'demo' => true, 'model' => 'demo']);
        }

        // Live: OpenAI-kompatibler Endpunkt des Mesh-Hubs (z. B. /v1/chat/completions)
        $allowedModels = array_column((array) config('mesh.demoModels', []), 'id');
        $model = (string) ($_POST['model'] ?? '');
        $model = in_array($model, $allowedModels, true) ? $model : (string) ($chat['defaultModel'] ?? '');
        $payload = [
            'model' => $model,
            'messages' => array_merge([['role' => 'system', 'content' => (string) ($chat['systemPrompt'] ?? '')]], $clean),
            'max_tokens' => (int) ($chat['maxTokens'] ?? 800),
            'temperature' => (float) ($chat['temperature'] ?? 0.7),
            'stream' => false,
        ];
        $curl = curl_init(rtrim((string) $chat['apiUrl'], '/') . '/v1/chat/completions');
        $headers = ['Content-Type: application/json'];
        if (!empty($chat['apiKey'])) {
            $headers[] = 'Authorization: Bearer ' . $chat['apiKey'];
        }
        curl_setopt_array($curl, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($payload, JSON_UNESCAPED_UNICODE),
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => (int) ($chat['timeoutSeconds'] ?? 60),
            CURLOPT_CONNECTTIMEOUT => 8,
        ]);
        $raw = curl_exec($curl);
        $status = (int) curl_getinfo($curl, CURLINFO_RESPONSE_CODE);
        curl_close($curl);
        $result = is_string($raw) ? json_decode($raw, true) : null;
        $reply = $result['choices'][0]['message']['content'] ?? null;
        if ($status !== 200 || !is_string($reply)) {
            jsonResponse(['ok' => false, 'message' => 'Das Mesh ist gerade nicht erreichbar. Bitte versuche es gleich noch einmal.'], 502);
        }
        // Denk-Abschnitte mancher Modelle ausblenden
        $reply = trim((string) preg_replace('#<think>.*?</think>#s', '', $reply));
        jsonResponse(['ok' => true, 'reply' => $reply, 'demo' => false, 'model' => $result['model'] ?? $model]);
        break;

    default:
        jsonResponse(['ok' => false, 'message' => 'Unbekannte Aktion.'], 400);
}
