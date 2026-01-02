<?php

declare(strict_types=1);

namespace Aaix\FilamentEditorJs\Support\HtmlRenderer\Renderers;

use Aaix\FilamentEditorJs\Support\HtmlRenderer\Contracts\BlockRenderer;

class DelimiterRenderer implements BlockRenderer
{
    public function render(array $data): string
    {
        return '<div class="delimiter"></div>';
    }

    public static function getCss(): string
    {
        return <<<CSS
            %scope% .delimiter {
                line-height: 1.6em;
                width: 100%;
                text-align: center;
                margin: 2em 0;
                border: none;
                padding: 1.5em 0 0.5em 0;
            }

            %scope% .delimiter::before {
                content: "***";
                font-size: 1.75em;
                letter-spacing: 0.5em;
                color: currentColor;
                opacity: 0.6;
                font-weight: bold;
                display: inline-block;
            }
        CSS;
    }

    public static function getJs(): string
    {
        return '';
    }
}
