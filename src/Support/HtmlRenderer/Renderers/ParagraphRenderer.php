<?php

declare(strict_types=1);

namespace Aaix\FilamentEditorJs\Support\HtmlRenderer\Renderers;

use Aaix\FilamentEditorJs\Support\HtmlRenderer\Contracts\BlockRenderer;

class ParagraphRenderer implements BlockRenderer
{
    public function render(array $data): string
    {
        // Standard HTML Paragraph
        return '<p>' . ($data['text'] ?? '') . '</p>';
    }

    public static function getCss(): string
    {
        return <<<CSS
            %scope% p {
                margin-top: 0;
                margin-bottom: 1.25em;
                line-height: 1.625;
                font-size: 1em;
            }

            /* Optional: Links im Text hervorheben */
            %scope% p a {
                color: inherit;
                text-decoration: underline;
                text-decoration-thickness: 1px;
                text-underline-offset: 2px;
            }
        CSS;
    }

    public static function getJs(): string
    {
        return '';
    }
}
