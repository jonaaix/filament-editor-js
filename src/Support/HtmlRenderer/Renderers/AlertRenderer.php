<?php

declare(strict_types=1);

namespace Aaix\FilamentEditorJs\Support\HtmlRenderer\Renderers;

use Aaix\FilamentEditorJs\Support\HtmlRenderer\Contracts\BlockRenderer;

class AlertRenderer implements BlockRenderer
{
    public function render(array $data): string
    {
        $text = $data['message'] ?? $data['text'] ?? '';
        $type = $data['type'] ?? 'info';
        $align = $data['align'] ?? 'left';

        $alignClass = match ($align) {
            'center' => 'alert--center',
            'right' => 'alert--right',
            default => 'alert--left',
        };

        return <<<HTML
            <div class="alert alert--{$type} {$alignClass}">
                <div class="alert-content">{$text}</div>
            </div>
        HTML;
    }

    public static function getCss(): string
    {
        return <<<CSS
            %scope% .alert {
                padding: 1rem 1.25rem;
                margin: 1.5rem 0;
                border-radius: 0.5rem;
                border-width: 1px;
                border-style: solid;
                font-size: 1rem;
                line-height: 1.5;
                position: relative;
            }

            /* Alignment Modifiers */
            %scope% .alert--left { text-align: left; }
            %scope% .alert--center { text-align: center; }
            %scope% .alert--right { text-align: right; }

            /* Color Variants - Borders darkened to 300/400 scale */
            %scope% .alert--primary {
                background-color: #eff6ff; /* blue-50 */
                border-color: #93c5fd;     /* blue-300 */
                color: #1e40af;            /* blue-800 */
            }

            %scope% .alert--secondary {
                background-color: #f3f4f6; /* gray-100 */
                border-color: #d1d5db;     /* gray-300 */
                color: #374151;            /* gray-700 */
            }

            %scope% .alert--info {
                background-color: #ecfeff; /* cyan-50 */
                border-color: #67e8f9;     /* cyan-300 */
                color: #155e75;            /* cyan-800 */
            }

            %scope% .alert--success {
                background-color: #f0fdf4; /* green-50 */
                border-color: #86efac;     /* green-300 */
                color: #166534;            /* green-800 */
            }

            %scope% .alert--warning {
                background-color: #fefce8; /* yellow-50 */
                border-color: #facc15;     /* yellow-400 */
                color: #854d0e;            /* yellow-800 */
            }

            %scope% .alert--danger {
                background-color: #fef2f2; /* red-50 */
                border-color: #fca5a5;     /* red-300 */
                color: #991b1b;            /* red-800 */
            }

            %scope% .alert--light {
                background-color: #ffffff;
                border-color: #d1d5db;
                color: #374151;
            }

            %scope% .alert--dark {
                background-color: #1f2937; /* gray-800 */
                border-color: #000000;     /* black */
                color: #f3f4f6;            /* gray-100 */
            }
            
            /* Dark Mode Adaptations */
            .dark %scope% .alert--primary { background: rgba(30, 64, 175, 0.2); border-color: #3b82f6; color: #bfdbfe; }
            .dark %scope% .alert--secondary { background: rgba(55, 65, 81, 0.4); border-color: #6b7280; color: #e5e7eb; }
            .dark %scope% .alert--info { background: rgba(21, 94, 117, 0.2); border-color: #06b6d4; color: #a5f3fc; }
            .dark %scope% .alert--success { background: rgba(22, 101, 52, 0.2); border-color: #22c55e; color: #bbf7d0; }
            .dark %scope% .alert--warning { background: rgba(133, 77, 14, 0.2); border-color: #eab308; color: #fde047; }
            .dark %scope% .alert--danger { background: rgba(153, 27, 27, 0.2); border-color: #ef4444; color: #fecaca; }
            .dark %scope% .alert--light { background: #1f2937; border-color: #4b5563; color: #e5e7eb; }
            .dark %scope% .alert--dark { background: #111827; border-color: #000; color: #e5e7eb; }
        CSS;
    }

    public static function getJs(): string
    {
        return '';
    }
}
