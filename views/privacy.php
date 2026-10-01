<?php
/** Datenschutzerklärung – Angaben aus data/config.json. @var array $page */
$org = config('organization', []);
$address = $org['address'] ?? [];
$retention = (int) config('analytics.retentionMonths', 14);
$chatLive = (bool) config('mesh.chat.enabled', false);
$sections = [
    'verantwortlich' => 'Verantwortlicher',
    'grundsaetze' => 'Das Wichtigste in Kürze',
    'hosting' => 'Hosting & Server-Logs',
    'statistik' => 'Cookielose Statistik',
    'formulare' => 'Formulare & Anfragen',
    'chat' => 'Chat',
    'email' => 'E-Mail-Versand',
    'cookies' => 'Cookies & lokale Ressourcen',
    'rechte' => 'Deine Rechte',
    'aenderungen' => 'Änderungen',
];
?>
<?= partial('pageHero', [
    'page' => $page,
    'eyebrow' => 'Rechtliches',
    'title' => 'Datenschutz',
    'lead' => 'Transparenz ist uns wichtig. Hier erfährst du, welche Daten wir verarbeiten – und welche nicht.',
]) ?>

<section class="section-light">
    <div class="container legal legal-with-toc">
        <nav class="legal-toc reveal" aria-label="Inhalt">
            <p class="field-label">Inhalt</p>
            <ol>
<?php foreach ($sections as $id => $label): ?>
                <li><a href="#<?= e($id) ?>"><?= e($label) ?></a></li>
<?php endforeach; ?>
            </ol>
        </nav>

        <div class="legal-text">
            <h2 id="verantwortlich">1. Verantwortlicher</h2>
            <p>
                <strong><?= e($org['legalName'] ?? $org['name']) ?></strong><br>
                <?= e($address['street'] ?? '') ?>, <?= e(($address['zip'] ?? '') . ' ' . ($address['city'] ?? '')) ?><br>
                E-Mail: <a href="mailto:<?= e($org['email']) ?>"><?= e($org['email']) ?></a>
<?php if (!empty($org['phone'])): ?>
                <br>Telefon: <?= e($org['phoneDisplay'] ?? $org['phone']) ?>
<?php endif; ?>
            </p>

            <h2 id="grundsaetze">2. Das Wichtigste in Kürze</h2>
            <ul class="check-list">
                <li><?= icon('check') ?>Keine Cookies für Statistik oder Werbung</li>
                <li><?= icon('check') ?>Keine Dienste von Dritten (Schriften, Skripte und Bilder liegen auf unserem Server)</li>
                <li><?= icon('check') ?>Keine Speicherung von IP-Adressen in unserer Statistik</li>
                <li><?= icon('check') ?>Formulardaten nutzen wir nur, um dein Anliegen zu bearbeiten</li>
            </ul>

            <h2 id="hosting">3. Hosting &amp; Server-Logs</h2>
            <p>Diese Website wird bei einem Hosting-Anbieter in Deutschland betrieben. Beim Aufruf verarbeitet der Webserver technisch notwendige Daten (z. B. IP-Adresse, Zeitpunkt, aufgerufene Datei, Browser). Das ist erforderlich, um die Website sicher auszuliefern (Art. 6 Abs. 1 lit. f DSGVO). Die Server-Logs des Hosters werden nach kurzer Zeit automatisch gelöscht.</p>

            <h2 id="statistik">4. Cookielose Statistik</h2>
            <p>Um unser Angebot zu verbessern, protokollieren wir jeden Seitenaufruf in einer eigenen Logdatei auf unserem Server. Gespeichert werden: aufgerufene Seite, Zeitpunkt, Herkunftsseite (Referrer), Browser-, Betriebssystem- und Gerätetyp, Sprache sowie Verweildauer und Scrolltiefe.</p>
            <p>Zur Unterscheidung von Besuchen bilden wir aus IP-Adresse und Browserkennung einen Einweg-Hashwert, der jeden Tag mit einem neuen Schlüssel erzeugt wird. Die IP-Adresse selbst wird nicht gespeichert; ein Wiedererkennen über mehrere Tage ist nicht möglich. Es werden keine Cookies gesetzt und keine Daten an Dritte übermittelt. Rechtsgrundlage ist unser berechtigtes Interesse an einer datensparsamen Reichweitenmessung (Art. 6 Abs. 1 lit. f DSGVO). Die Daten werden nach <?= $retention ?> Monaten gelöscht.</p>

            <h2 id="formulare">5. Formulare &amp; Anfragen</h2>
            <p>Wenn du dich für die Beta anmeldest, uns kontaktierst oder eine Spendenanfrage sendest, speichern wir deine Angaben (z. B. Name, E-Mail-Adresse, Nachricht und freiwillige Angaben zu deinem Mac) auf unserem Server, um dein Anliegen zu bearbeiten (Art. 6 Abs. 1 lit. b DSGVO) bzw. auf Grundlage deiner Einwilligung (Art. 6 Abs. 1 lit. a DSGVO). Zum Schutz vor Spam nutzen wir ein unsichtbares Prüffeld und eine Zeitprüfung – ohne externe Dienste.</p>
            <p>Wir löschen deine Daten, sobald sie für den Zweck nicht mehr erforderlich sind und keine gesetzlichen Aufbewahrungspflichten bestehen. Eine erteilte Einwilligung kannst du jederzeit per E-Mail widerrufen.</p>

            <h2 id="chat">6. Chat</h2>
<?php if ($chatLive): ?>
            <p>Nachrichten im Chat werden verschlüsselt an das GemeinsamKI-Mesh übertragen und dort von einem verbundenen Mac beantwortet. Inhalte werden nicht dauerhaft gespeichert. Bitte gib keine sensiblen personenbezogenen Daten ein.</p>
<?php else: ?>
            <p>Der Chat läuft derzeit im Demo-Modus: Deine Nachricht wird an unseren Server gesendet, mit vorbereiteten Antworten abgeglichen und nicht gespeichert. Bitte gib keine sensiblen personenbezogenen Daten ein.</p>
<?php endif; ?>

            <h2 id="email">7. E-Mail-Versand</h2>
            <p>Benachrichtigungen und Bestätigungen versenden wir über den E-Mail-Server unseres Anbieters. Dabei werden die für den Versand nötigen Daten (E-Mail-Adresse, Inhalt) verarbeitet.</p>

            <h2 id="cookies">8. Cookies &amp; lokale Ressourcen</h2>
            <p>Diese Website setzt für Besucher:innen keine Cookies. Lediglich der passwortgeschützte Verwaltungsbereich verwendet ein technisch notwendiges Sitzungs-Cookie. Schriftarten (Inter, JetBrains Mono) werden lokal von unserem Server geladen – es findet keine Verbindung zu Google oder anderen Drittanbietern statt.</p>

            <h2 id="rechte">9. Deine Rechte</h2>
            <p>Du hast das Recht auf Auskunft (Art. 15 DSGVO), Berichtigung (Art. 16), Löschung (Art. 17), Einschränkung der Verarbeitung (Art. 18), Datenübertragbarkeit (Art. 20) und Widerspruch (Art. 21). Außerdem kannst du dich bei einer Datenschutz-Aufsichtsbehörde beschweren. Schreib uns einfach an <a href="mailto:<?= e($org['email']) ?>"><?= e($org['email']) ?></a>.</p>

            <h2 id="aenderungen">10. Änderungen</h2>
            <p>Wir passen diese Datenschutzerklärung an, sobald sich unser Angebot ändert. Stand: <?= e(date('m/Y', (int) filemtime(__FILE__))) ?>.</p>
        </div>
    </div>
</section>
