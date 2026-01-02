<?php

declare(strict_types=1);

namespace Aaix\FilamentEditorJs\Support\HtmlRenderer\Renderers;

use Aaix\FilamentEditorJs\Support\HtmlRenderer\Contracts\BlockRenderer;

class TableRenderer implements BlockRenderer
{
    public function render(array $data): string
    {
        $content = $data['content'] ?? [];

        if (empty($content)) {
            return '';
        }

        $withHeadings = $data['withHeadings'] ?? false;
        $html = '<div class="table-wrapper"><table>';

        if ($withHeadings) {
            $headerRow = array_shift($content);
            $html .= '<thead><tr>';
            foreach ($headerRow as $cell) {
                $html .= "<th>{$cell}</th>";
            }
            $html .= '</tr></thead>';
        }

        $html .= '<tbody>';
        foreach ($content as $row) {
            $html .= '<tr>';
            foreach ($row as $cell) {
                $html .= "<td>{$cell}</td>";
            }
            $html .= '</tr>';
        }

        return $html . '</tbody></table></div>';
    }

    public static function getCss(): string
    {
        return <<<CSS
            %scope% .table-wrapper { overflow-x: auto; margin: 1em 0; }
            %scope% table { width: 100%; border-collapse: collapse; border-spacing: 0; border: 1px solid #e5e7eb; }
            %scope% th, %scope% td { padding: 0.5em 1em; border: 1px solid #e5e7eb; text-align: left; }
            %scope% th { background-color: rgba(0,0,0,0.05); font-weight: 600; }
            %scope% tr:nth-child(even) { background-color: rgba(0,0,0,0.02); }
        CSS;
    }

    public static function getJs(): string
    {
        return '';
    }
}
