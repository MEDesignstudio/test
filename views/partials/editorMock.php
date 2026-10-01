<?php /** VS-Code-Mockup mit getippter Eingabe und KI-Vorschlag (Animation in main.js). */ ?>
<div class="editor" data-editor aria-hidden="true">
    <div class="editor-bar">
        <span class="traffic"><i></i><i></i><i></i></span>
        <span class="editor-tab is-active"><?= icon('code') ?>mesh.ts</span>
        <span class="editor-tab">hub.ts</span>
    </div>
    <div class="editor-body">
        <div class="editor-side">
            <span class="is-active"><?= icon('layers') ?></span>
            <span><?= icon('search') ?></span>
            <span class="editor-side-brand"><?= img('brand.mark', ['alt' => '']) ?></span>
        </div>
        <pre class="editor-code"><code><span class="code-line" data-line="typed"><span class="tk-c">// Finde einen freien Mac mit passendem Modell</span></span>
<span class="code-line" data-line="typed"><span class="tk-k">export function</span> <span class="tk-f">pickNode</span>(nodes: <span class="tk-t">MeshNode</span>[], model: <span class="tk-t">string</span>) {</span>
<span class="code-line" data-line="ghost">  <span class="tk-k">return</span> nodes</span>
<span class="code-line" data-line="ghost">    .<span class="tk-f">filter</span>(n =&gt; n.online &amp;&amp; n.models.<span class="tk-f">includes</span>(model))</span>
<span class="code-line" data-line="ghost">    .<span class="tk-f">sort</span>((a, b) =&gt; a.load - b.load)[<span class="tk-n">0</span>];</span>
<span class="code-line" data-line="ghost">}</span></code></pre>
        <span class="editor-hint"><kbd>Tab</kbd> Vorschlag übernehmen</span>
    </div>
    <div class="editor-status">
        <span><?= icon('mesh') ?>GemeinsamKI</span>
        <span>qwen3.6-35b · MacBook Pro M5 Max</span>
        <span class="editor-status-ok"><span class="status-dot"></span>verbunden</span>
    </div>
</div>
