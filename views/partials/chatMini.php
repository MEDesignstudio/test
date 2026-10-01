<?php /** Animierte Mini-Chat-Vorschau (Startseite). */ ?>
<div class="chat-mini" data-chat-mini aria-hidden="true">
    <div class="chat-mini-head">
        <span class="chat-mini-title"><?= icon('chat') ?>chat.gemeinsamki.de</span>
        <span class="model-chip"><span class="status-dot"></span>gpt-oss-20b</span>
    </div>
    <div class="chat-mini-body">
        <p class="bubble bubble-user" data-chat-step="1">Erklär mir das Mesh in einem Satz.</p>
        <p class="bubble bubble-ai" data-chat-step="2"><span class="typing"><i></i><i></i><i></i></span><span class="bubble-text">Viele Macs, ein Netz: Jeder teilt freie Rechenleistung – und alle nutzen gemeinsam die KI.</span></p>
        <p class="bubble-meta" data-chat-step="3"><?= icon('laptop') ?>beantwortet von MacBook Air M4 · 0,8 s</p>
    </div>
    <div class="chat-mini-input"><span>Frag das Mesh …</span><span class="chat-mini-send"><?= icon('send') ?></span></div>
</div>
