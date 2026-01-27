<?php

declare(strict_types=1);

namespace Aaix\FilamentEditorJs\Support\HtmlRenderer\Renderers;

use Aaix\FilamentEditorJs\Support\HtmlRenderer\Contracts\BlockRenderer;

class ChecklistRenderer implements BlockRenderer
{
    public function render(array $data): string
    {
        $items = $data['items'] ?? [];
        $html = '<ul class="checklist">';

        foreach ($items as $item) {
            $text = $item['text'] ?? '';
            $checked = $item['checked'] ?? false;
            $checkedClass = $checked ? 'checklist-item--checked' : '';

            // Using a span for the custom checkbox to avoid browser-default styling issues
            $checkbox = '<span class="checklist-box"></span>';
            if ($checked) {
                // SVG Checkmark
                $checkbox = '<span class="checklist-box"><svg viewBox="0 0 24 24"><path d="M5 12l5 5L20 7"/></svg></span>';
            }

            $html .= "<li class=\"checklist-item {$checkedClass}\">{$checkbox}<span class=\"checklist-text\">{$text}</span></li>";
        }

        return $html . '</ul>';
    }

    public static function getCss(): string
    {
        return <<<CSS
            %scope% .checklist { list-style: none; padding: 0; margin: 1em 0; }
            %scope% .checklist-item { display: flex; align-items: flex-start; margin-bottom: 0.5em; }
            %scope% .checklist-box {
                flex-shrink: 0;
                width: 1.35em;
                height: 1.35em;
                margin-right: 0.75em;
                margin-left: 0.75em;
                border: 1px solid rgba(145,145,145, 1);
                border-radius: 4px;
                display: flex;
                align-items: center;
                justify-content: center;
                opacity: 0.6;
            }
            %scope% .checklist-item--checked .checklist-box {
                color: white;
                background-color: rgba(145,145,145, 1);
                opacity: 1;
                svg {
                  stroke: currentColor;
                }
            }
            %scope% .checklist-box svg { width: 80%; height: 80%; fill: none; stroke: #fff; stroke-width: 3; stroke-linecap: round; stroke-linejoin: round; }
            %scope% .checklist-item--checked .checklist-text { opacity: 0.7; }
        CSS;
    }

    public static function getJs(): string
    {
        return '';
    }
}
