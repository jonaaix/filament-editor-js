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
            %scope% h1 { 
                font-size: 2.5em; 
                font-weight: 800; 
                line-height: 1.2; 
                margin: 2em 0 0.67em; /* Vorher 0.67em oben */
            }
            
            %scope% h2 { 
                font-size: 2em; 
                font-weight: 700; 
                line-height: 1.25; 
                margin: 1.75em 0 0.75em; /* Vorher 0.75em oben */
            }
            
            %scope% h3 { 
                font-size: 1.5em; 
                font-weight: 700; 
                line-height: 1.3; 
                margin: 1.5em 0 0.83em; /* Vorher 0.83em oben */
            }
            
            %scope% h4 { 
                font-size: 1.25em; 
                font-weight: 600; 
                line-height: 1.4; 
                margin: 1.5em 0 1em; /* Vorher 1em oben */
            }
            
            %scope% h5,
            %scope% h6 { 
                font-size: 1em; 
                font-weight: 600; 
                margin: 1.5em 0 1.2em; /* Vorher 1.2em oben */
            }
        CSS;
    }

    public static function getJs(): string
    {
        return '';
    }
}
