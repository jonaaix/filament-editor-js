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
            %scope% h1, 
            %scope% h2, 
            %scope% h3, 
            %scope% h4, 
            %scope% h5, 
            %scope% h6 {
                font-weight: 700;
                line-height: 1.25;
                margin-top: 2rem;
                margin-bottom: 1rem;
            }

            %scope% h1 { font-size: 2.25rem; }
            %scope% h2 { font-size: 1.875rem; }
            %scope% h3 { font-size: 1.5rem; }
            %scope% h4 { font-size: 1.25rem; }
            %scope% h5 { font-size: 1.125rem; }
            %scope% h6 { font-size: 1rem; }
        CSS;
    }

    public static function getJs(): string
    {
        return '';
    }
}
