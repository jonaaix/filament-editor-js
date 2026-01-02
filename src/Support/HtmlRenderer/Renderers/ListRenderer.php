<?php

declare(strict_types=1);

namespace Aaix\FilamentEditorJs\Support\HtmlRenderer\Renderers;

use Aaix\FilamentEditorJs\Support\HtmlRenderer\Contracts\BlockRenderer;

class ListRenderer implements BlockRenderer
{
    public function render(array $data): string
    {
        $style = ($data['style'] ?? 'unordered');
        $tag = $style === 'ordered' ? 'ol' : 'ul';
        $items = $data['items'] ?? [];

        if (empty($items)) {
            return '';
        }

        $html = "<{$tag}>";

        foreach ($items as $item) {
            $html .= "<li>{$item}</li>";
        }

        $html .= "</{$tag}>";

        return $html;
    }

    public static function getCss(): string
    {
        return <<<CSS
            %scope% ul,
            %scope% ol {
                margin: 1em 0;
                padding-left: 2.5em;
                line-height: 1.6;
            }

            %scope% ul {
                list-style-type: disc;
            }

            %scope% ol {
                list-style-type: decimal;
            }

            %scope% li {
                margin-bottom: 0.25em;
                padding-left: 0.25em;
            }

            /* Nested Lists Support */
            %scope% ul ul,
            %scope% ol ul {
                list-style-type: circle;
                margin: 0.5em 0;
            }

            %scope% ol ol,
            %scope% ul ol {
                list-style-type: lower-alpha;
                margin: 0.5em 0;
            }
        CSS;
    }

    public static function getJs(): string
    {
        return '';
    }
}
