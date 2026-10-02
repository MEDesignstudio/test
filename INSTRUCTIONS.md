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
1. Per Docker starten (siehe unten) oder klassisch hochladen (Apache mit `mod_rewrite`, `data/` beschreibbar).
2. `data/config.json` ausfüllen: alle Werte in `[eckigen Klammern]` ersetzen (Adresse, Vorstand, Register, IBAN, SMTP …). Platzhalter werden auf der Website/im Schema automatisch ausgeblendet, im Impressum aber angezeigt.
3. SMTP aktivieren: `smtp.enabled: true` + Zugangsdaten. Ohne SMTP wird `mail()` genutzt.
4. Admin: `/admin.php`, Benutzer `admin`. Passwort nach dem ersten Login unter *Einstellungen* ändern.
5. Live-Chat: `mesh.chat.enabled: true` und `mesh.chat.apiUrl` auf den OpenAI-kompatiblen Hub setzen (`/v1/chat/completions`, z. B. LM Studio). Bis dahin läuft der Chat im Demo-Modus (`data/chatDemo.json`).
6. `gemeinsamki.org` und `www.` leiten per 301 auf `gemeinsamki.de` um (`site.redirectAliases`). Andere Hosts (z. B. Dev-Server) bekommen automatisch `noindex`.
7. Optional HTTPS erzwingen: `site.forceHttps: true`.

## Docker (empfohlen)
Auf dem Docker-Host:
```bash
git clone <repo> gemeinsamki && cd gemeinsamki
cp .env.example .env        # Port, UID/GID und optionale Secrets anpassen
docker compose up -d --build
```
Die Seite läuft dann auf `http://<host>:8080` (Port über `HTTP_PORT` in `.env`).

- **Image:** `php:8.3-apache` mit `mod_rewrite`, `headers`, `expires`, `deflate`, `remoteip` und OPcache. Die `.htaccess` gilt unverändert.
- **Daten:** `./data` wird in den Container gemountet. `config.json`, Logs und eingegangene Anfragen liegen also auf dem Host und überstehen Updates. Ist `data/` leer, legt der Container die Standarddateien automatisch an. Vorhandene Dateien werden nie überschrieben.
- **Dateirechte:** `APP_UID`/`APP_GID` in `.env` auf `id -u` / `id -g` setzen, dann gehören alle Dateien in `./data` deinem Benutzer.
- **Secrets per Umgebungsvariable:** `GKI_SMTP_PASSWORD`, `GKI_CHAT_API_KEY` usw. (Liste in `.env.example`) überschreiben die Werte aus `config.json`. Passwörter müssen so nicht in der JSON-Datei stehen.
- **HTTPS:** Den Container hinter einen Reverse-Proxy hängen (Traefik, Caddy, Nginx Proxy Manager). `X-Forwarded-For` und `X-Forwarded-Proto` werden ausgewertet: Die Statistik sieht so die echte Besucher-IP (nur als Hash) und die Seite erkennt HTTPS.
- **Domains:** Beide Domains (`gemeinsamki.de`, `gemeinsamki.org`) auf den Proxy zeigen lassen. `.org` und `www.` leitet die App per 301 auf `.de` um.
- **Update:** `git pull && docker compose up -d --build`
- **Logs:** `docker compose logs -f` (Apache- und PHP-Fehler gehen nach stdout/stderr)
- **Healthcheck:** eingebaut, Status über `docker compose ps`.
- **E-Mail:** Im Container gibt es kein `sendmail`. Für Mails bitte SMTP aktivieren (`smtp.enabled` bzw. `GKI_SMTP_*`). Anfragen werden immer gespeichert, auch wenn der Versand fehlschlägt.
- **vendor/ (PHPMailer)** liegt im Repository. Beim Build wird nichts per Composer geladen.

## Analytics
Jeder Seitenaufruf wird in `data/logs/visits-JJJJ-MM.jsonl` geloggt – cookielos, ohne IP-Speicherung (täglich wechselnder Besucher-Hash). Verweildauer und Scrolltiefe kommen per Beacon. `admin.php` zeigt Besucher/Tag, Seiten, Referrer, Ausstiegs- und Einstiegsseiten, Besucherverläufe, Technik, Heatmap und Live-Besucher. Aufbewahrung: `analytics.retentionMonths`.
