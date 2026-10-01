<?php
/** Impressum – alle Angaben aus data/config.json. @var array $page */
$org = config('organization', []);
$address = $org['address'] ?? [];
?>
<?= partial('pageHero', [
    'page' => $page,
    'eyebrow' => 'Rechtliches',
    'title' => 'Impressum',
    'lead' => 'Angaben gemäß § 5 DDG für gemeinsamki.de und gemeinsamki.org.',
]) ?>

<section class="section-light">
    <div class="container legal">
        <div class="legal-grid">
            <div class="legal-block reveal">
                <h2>Anbieter</h2>
                <p>
                    <strong><?= e($org['legalName'] ?? $org['name']) ?></strong><br>
                    <?= e($address['street'] ?? '') ?><br>
                    <?= e(($address['zip'] ?? '') . ' ' . ($address['city'] ?? '')) ?><br>
                    <?= e($address['country'] ?? '') ?>
                </p>
            </div>
            <div class="legal-block reveal">
                <h2>Kontakt</h2>
                <p>
<?php if (!empty($org['phone'])): ?>
                    Telefon: <a href="tel:<?= e(preg_replace('/[^0-9+]/', '', $org['phone'])) ?>"><?= e($org['phoneDisplay'] ?? $org['phone']) ?></a><br>
<?php endif; ?>
                    E-Mail: <a href="mailto:<?= e($org['email']) ?>"><?= e($org['email']) ?></a>
                </p>
            </div>
            <div class="legal-block reveal">
                <h2>Vertreten durch</h2>
                <p><?= implode('<br>', array_map('e', (array) ($org['representatives'] ?? []))) ?></p>
            </div>
<?php if (!empty($org['register']['number'])): ?>
            <div class="legal-block reveal">
                <h2>Registereintrag</h2>
                <p><?= e($org['legalForm'] ?? '') ?><br>Registergericht: <?= e($org['register']['court'] ?? '') ?><br>Registernummer: <?= e($org['register']['number']) ?></p>
            </div>
<?php endif; ?>
<?php if (!empty($org['vatId'])): ?>
            <div class="legal-block reveal">
                <h2>Umsatzsteuer-ID</h2>
                <p>Umsatzsteuer-Identifikationsnummer gemäß § 27 a UStG: <?= e($org['vatId']) ?></p>
            </div>
<?php endif; ?>
            <div class="legal-block reveal">
                <h2>Verantwortlich für den Inhalt</h2>
                <p>nach § 18 Abs. 2 MStV:<br><?= e($org['contentResponsible'] ?? '') ?><br><?= e(trim(($address['street'] ?? '') . ', ' . ($address['zip'] ?? '') . ' ' . ($address['city'] ?? ''), ', ')) ?></p>
            </div>
        </div>

        <div class="legal-text reveal">
            <h2>Verbraucherstreitbeilegung</h2>
            <p>Wir sind nicht bereit oder verpflichtet, an Streitbeilegungsverfahren vor einer Verbraucherschlichtungsstelle teilzunehmen.</p>

            <h2>Haftung für Inhalte</h2>
            <p>Als Diensteanbieter sind wir für eigene Inhalte auf diesen Seiten nach den allgemeinen Gesetzen verantwortlich. Wir sind jedoch nicht verpflichtet, übermittelte oder gespeicherte fremde Informationen zu überwachen oder nach Umständen zu forschen, die auf eine rechtswidrige Tätigkeit hinweisen. Bei Bekanntwerden von Rechtsverletzungen entfernen wir derartige Inhalte umgehend.</p>

            <h2>Haftung für Links</h2>
            <p>Unser Angebot enthält Links zu externen Websites Dritter, auf deren Inhalte wir keinen Einfluss haben. Für die Inhalte der verlinkten Seiten ist stets der jeweilige Anbieter oder Betreiber verantwortlich. Bei Bekanntwerden von Rechtsverletzungen entfernen wir derartige Links umgehend.</p>

            <h2>Urheberrecht</h2>
            <p>Die durch die Seitenbetreiber erstellten Inhalte und Werke auf diesen Seiten unterliegen dem deutschen Urheberrecht. Beiträge Dritter sind als solche gekennzeichnet. Die Schriftarten Inter und JetBrains Mono stehen unter der SIL Open Font License.</p>
        </div>
    </div>
</section>
