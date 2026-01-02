<?php

declare(strict_types=1);

namespace Aaix\FilamentEditorJs\Support\HtmlRenderer\Renderers;

use Aaix\FilamentEditorJs\Support\HtmlRenderer\Contracts\BlockRenderer;

class CodeRenderer implements BlockRenderer
{
    public function render(array $data): string
    {
        $code = htmlspecialchars($data['code'] ?? '');
        return "<pre><code>{$code}</code></pre>";
    }

    public static function getCss(): string
    {
        return <<<CSS
            %scope% pre { background: #282c34; color: #abb2bf; padding: 1em; border-radius: 4px; overflow-x: auto; margin: 1em 0; }
            %scope% code { font-family: monospace; font-size: 0.9em; }
        CSS;
    }

    public static function getJs(): string
    {
        return '';
    }
}
