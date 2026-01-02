<?php

declare(strict_types=1);

namespace Aaix\FilamentEditorJs\Support\HtmlRenderer\Renderers;

use Aaix\FilamentEditorJs\Support\HtmlRenderer\Contracts\BlockRenderer;

class HeaderRenderer implements BlockRenderer
{
    public function render(array $data): string
    {
        $level = $data['level'] ?? 2;
        $text = $data['text'] ?? '';

        return "<h{$level}>{$text}</h{$level}>";
    }

    public static function getCss(): string
    {
        return <<<CSS
            %scope% h1 { font-size: 2.5em; font-weight: 800; margin: 0.67em 0; line-height: 1.2; }
            %scope% h2 { font-size: 2em; font-weight: 700; margin: 0.75em 0; line-height: 1.25; }
            %scope% h3 { font-size: 1.5em; font-weight: 700; margin: 0.83em 0; line-height: 1.3; }
            %scope% h4 { font-size: 1.25em; font-weight: 600; margin: 1em 0; line-height: 1.4; }
            %scope% h5, %scope% h6 { font-size: 1em; font-weight: 600; margin: 1.2em 0; }
        CSS;
    }

    public static function getJs(): string
    {
        return '';
    }
}
