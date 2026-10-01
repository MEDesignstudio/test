<?php
/** FAQ mit Suche und Kategorien. @var array $page */
$items = faqItems();
$categories = array_values(array_unique(array_column($items, 'category')));
?>
<?= partial('pageHero', [
    'page' => $page,
    'eyebrow' => 'FAQ',
    'title' => 'Fragen? <em>Antworten.</em>',
    'lead' => 'Alles Wichtige rund um GemeinsamKI – kurz und klar.',
]) ?>

<section class="section-light">
    <div class="container faq-page">
        <div class="faq-tools reveal" data-faq-tools>
            <label class="faq-search">
                <?= icon('search') ?>
                <span class="sr-only">Fragen durchsuchen</span>
                <input type="search" placeholder="Frage suchen, z. B. „Akku“ oder „sicher“" data-faq-search autocomplete="off">
            </label>
            <div class="chip-group chip-group-light" role="group" aria-label="Kategorie filtern">
                <button type="button" class="chip is-active" data-faq-filter="">Alle</button>
<?php foreach ($categories as $category): ?>
                <button type="button" class="chip" data-faq-filter="<?= e($category) ?>"><?= e($category) ?></button>
<?php endforeach; ?>
            </div>
        </div>
        <?= partial('faqList', ['items' => $items]) ?>
        <p class="faq-empty" data-faq-empty hidden><?= icon('search') ?>Dazu haben wir noch keine Antwort. <a class="link-arrow link-arrow-dark" href="<?= e(url('kontakt')) ?>">Frag uns direkt <?= icon('arrow-right') ?></a></p>

        <aside class="ask-card reveal">
            <span class="ask-card-icon"><?= icon('chat') ?></span>
            <div>
                <h2>Deine Frage war nicht dabei?</h2>
                <p>Schreib uns – wir antworten persönlich.</p>
            </div>
            <div class="btn-row">
                <a class="btn btn-primary btn-heart magnetic" href="<?= e(url('kontakt', [], 'formular')) ?>">Frage stellen <?= icon('arrow-right') ?></a>
                <a class="btn btn-ghost btn-heart" href="<?= e(url('chat')) ?>">Chat-Demo testen</a>
            </div>
        </aside>
    </div>
</section>
