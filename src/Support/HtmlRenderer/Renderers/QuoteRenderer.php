<?php

declare(strict_types=1);

namespace Aaix\FilamentEditorJs\Support\HtmlRenderer\Renderers;

use Aaix\FilamentEditorJs\Support\HtmlRenderer\Contracts\BlockRenderer;

class QuoteRenderer implements BlockRenderer
{
    public function render(array $data): string
    {
        $text = $data['text'] ?? '';
        $caption = $data['caption'] ?? '';
        $alignment = $data['alignment'] ?? 'left';

        // CSS-Klasse für Ausrichtung hinzufügen
        $alignmentClass = match ($alignment) {
            'center' => 'quote--center',
            default => '',
        };

        $html = "<figure class=\"quote {$alignmentClass}\">";
        $html .= "<blockquote>\"{$text}\"</blockquote>";

        if (! empty($caption)) {
            $html .= "<figcaption>— {$caption}</figcaption>";
        }

        $html .= '</figure>';

        return $html;
    }

    public static function getCss(): string
    {
        return <<<CSS
            %scope% .quote {
                margin: 2em 0;
                padding: 1em 1.5em;
                background-color: rgba(0,0,0, 0.03); /* Sehr leichtes Grau für Kontrast */
                border-left: 4px solid #e5e7eb; /* Standard Border (Gray-200) */
                border-radius: 0 0.5em 0.5em 0;
            }

            %scope% .quote blockquote {
                margin: 0;
                font-size: 1.1em;
                font-style: italic;
                line-height: 1.6;
                color: inherit;
            }

            %scope% .quote figcaption {
                margin-top: 0.75em;
                font-size: 0.85em;
                color: #6b7280; /* Gray-500 für subtile Caption */
                font-weight: 500;
            }

            /* Zentrierte Variante (z.B. für Testimonials) */
            %scope% .quote--center {
                text-align: center;
                border-left: none;
                border-top: 4px solid #e5e7eb;
                border-radius: 0.5em;
            }
        CSS;
    }

    public static function getJs(): string
    {
        return '';
    }
}
