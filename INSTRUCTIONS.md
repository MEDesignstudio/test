# GemeinsamKI – Projekt-Instructions

Website für **gemeinsamki.de** / **gemeinsamki.org** – das deutsche KI-Mesh aus Macs.
Schrift: **Inter** (lokal), Farben: **#685EFF** (Marke) und **#111111** (Dunkel).

## Ordnerstruktur
| Pfad | Inhalt |
|---|---|
| `assets/` | `css/`, `js/`, `img/`, `mov/`, `fonts/` (Inter, JetBrains Mono – lokal, OFL) |
| `data/` | Alle Daten als JSON, per `.htaccess` gesperrt: `config.json` (Unternehmens-/Vereinsdaten, SMTP, Admin, Mesh-API), `matching.json` (Bildzuordnung), `faq.json`, `chatDemo.json`, `logs/` (Besucher-Logs), Laufzeitdaten `registrations.json`, `contacts.json`, `donations.json` |
| `views/` | Unterseiten (`home.php`, `how.php`, `macApp.php`, …) und `partials/` |
| `vendor/` | Composer (PHPMailer), Einbindung per Autoload in `app.php` |
| `app.php` | Funktionen, Konstanten, Routen + SEO-Meta + Schema.org-Zuordnung, Menü |
| `controller.php` | Alle AJAX-Aufrufe (Formulare, Tracking-Beacon, Chat) |
| `admin.php` | Analytics-Dashboard + Verwaltung der Datensätze |
| `index.php` | Haupteinstieg (Routing, Logging, Layout) |
| `sitemap.php` | Erzeugt `sitemap.xml` im Root und liefert sie aus |

## Regeln
- Jede Unterseite: eigenes SEO-Meta (in `ROUTES`, `app.php`) und eigenes Schema.org (`schemaGraph()`).
- Immer `url()`, `asset()`, `BASE_URL` nutzen – die Seite läuft auch im Unterordner.
- Bilder ausschließlich über `img('schluessel')` / `imgSrc()` – Zuordnung in `data/matching.json`. Icons über `icon('name')` (Sprite `assets/img/icons.svg`).
- camelCase, keine Datenbank – alles JSON unter `data/`.
- Keine Inline-Styles, keine Inline-Scripts (CSP aktiv), keine CDNs, keine Google Fonts.
- Keine hardkodierten Firmendaten – alles aus `data/config.json`.
- Wenig Text pro Screen, kurze Aussagen, klarer CTA.

## Inbetriebnahme
1. Hochladen (Apache mit `mod_rewrite`). `data/` muss beschreibbar sein.
2. `data/config.json` ausfüllen: alle Werte in `[eckigen Klammern]` ersetzen (Adresse, Vorstand, Register, IBAN, SMTP …). Platzhalter werden auf der Website/im Schema automatisch ausgeblendet, im Impressum aber angezeigt.
3. SMTP aktivieren: `smtp.enabled: true` + Zugangsdaten. Ohne SMTP wird `mail()` genutzt.
4. Admin: `/admin.php`, Benutzer `admin`. Passwort nach dem ersten Login unter *Einstellungen* ändern.
5. Live-Chat: `mesh.chat.enabled: true` und `mesh.chat.apiUrl` auf den OpenAI-kompatiblen Hub setzen (`/v1/chat/completions`, z. B. LM Studio). Bis dahin läuft der Chat im Demo-Modus (`data/chatDemo.json`).
6. `gemeinsamki.org` und `www.` leiten per 301 auf `gemeinsamki.de` um (`site.redirectAliases`). Andere Hosts (z. B. Dev-Server) bekommen automatisch `noindex`.
7. Optional HTTPS erzwingen: `site.forceHttps: true`.

## Analytics
Jeder Seitenaufruf wird in `data/logs/visits-JJJJ-MM.jsonl` geloggt – cookielos, ohne IP-Speicherung (täglich wechselnder Besucher-Hash). Verweildauer und Scrolltiefe kommen per Beacon. `admin.php` zeigt Besucher/Tag, Seiten, Referrer, Ausstiegs- und Einstiegsseiten, Besucherverläufe, Technik, Heatmap und Live-Besucher. Aufbewahrung: `analytics.retentionMonths`.
