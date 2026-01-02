<?php

declare(strict_types=1);

namespace Aaix\FilamentEditorJs\Support\HtmlRenderer\Renderers;

use Aaix\FilamentEditorJs\Support\HtmlRenderer\Contracts\BlockRenderer;
use Aaix\FilamentEditorJs\Support\HtmlRenderer\HtmlRenderer;

class CollapsibleRenderer implements BlockRenderer
{
    public function render(array $data): string
    {
        $title = $data['title'] ?? '';
        $innerBlocks = $data['_inner_blocks'] ?? [];
        $content = HtmlRenderer::renderBlocks($innerBlocks);

        if (empty($title) && empty($content)) {
            return '';
        }

        $isOpen = ! empty($data['settings']['defaultExpanded']) ? 'open' : '';

        return <<<HTML
            <details class="collapsible" {$isOpen}>
                <summary class="collapsible-summary">
                    <span class="collapsible-title">{$title}</span>
                    <span class="collapsible-icon"></span>
                </summary>
                <div class="collapsible-wrapper">
                    <div class="collapsible-content">
                        {$content}
                    </div>
                </div>
            </details>
            HTML;
    }

    public static function getCss(): string
    {
        return <<<CSS
            /* Container */
            %scope% .collapsible {
                border: 1px solid #e5e7eb;
                margin: 1em 0;
                background-color: #fff;
                overflow: hidden;
            }

            /* Header / Summary */
            %scope% .collapsible-summary {
                display: flex;
                align-items: center;
                justify-content: space-between;
                padding: 0.75em 1em;
                cursor: pointer;
                font-weight: 600;
                background-color: #f9fafb;
                list-style: none;
                user-select: none;
                transition: background-color 0.2s;
            }
            %scope% .collapsible-summary::-webkit-details-marker { display: none; }
            %scope% .collapsible-summary:hover { background-color: #f3f4f6; }

            /* Icon Rotation */
            %scope% .collapsible-icon {
                width: 1.25em;
                height: 1.25em;
                background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%236b7280' stroke-width='2'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M19 9l-7 7-7-7'%3F%3E%3C/svg%3E");
                background-repeat: no-repeat;
                background-position: center;
                transition: transform 0.3s ease;
            }

            /* FIX: Icon nur drehen, wenn NICHT closing */
            %scope% .collapsible[open]:not(.is-closing) .collapsible-icon {
                transform: rotate(180deg);
            }

            /* --- Animation Magic (Grid Trick) --- */
            %scope% .collapsible-wrapper {
                display: grid;
                grid-template-rows: 0fr;
                transition: grid-template-rows 0.3s ease-out;
            }

            /* FIX: Wrapper öffnen (1fr) nur wenn: Open gesetzt, NICHT closing UND NICHT opening */
            /* Dies zwingt den Browser beim Start der Animation kurzzeitig auf 0fr zu bleiben */
            %scope% .collapsible[open]:not(.is-closing):not(.is-opening) .collapsible-wrapper {
                grid-template-rows: 1fr;
            }

            /* Content Container (Inner) */
            %scope% .collapsible-content {
                min-height: 0;
                overflow: hidden;
                padding: 0 1em;
                border-top: 0 solid #e5e7eb;
                transition: padding 0.3s ease, border-color 0.3s ease, opacity 0.3s ease;
                opacity: 0;
            }

            %scope% .collapsible[open]:not(.is-closing):not(.is-opening) .collapsible-content {
                padding: 1em;
                border-top: 1px solid #e5e7eb;
                opacity: 1;
                transition-delay: 0.1s;
            }

            /* Dark Mode (Nested) */
            .dark {
                %scope% .collapsible { background-color: #1f2937; border-color: #374151; }
                %scope% .collapsible-summary { background-color: #374151; color: #e5e7eb; }
                %scope% .collapsible-summary:hover { background-color: #4b5563; }

                %scope% .collapsible[open]:not(.is-closing):not(.is-opening) .collapsible-content {
                    border-top-color: #4b5563;
                    color: #d1d5db;
                }
            }
        CSS;
    }

    public static function getJs(): string
    {
        return <<<JS
            if (!window.fiEditorJsCollapsibleInit) {
                window.fiEditorJsCollapsibleInit = true;
                document.addEventListener('click', (e) => {
                    const summary = e.target.closest('.collapsible-summary');
                    if (!summary) return;

                    const details = summary.parentElement;
                    if (!details || !details.classList.contains('collapsible')) return;

                    e.preventDefault();

                    if (details.classList.contains('is-closing') || details.classList.contains('is-opening')) {
                        return;
                    }

                    if (details.hasAttribute('open')) {
                        // CLOSE
                        details.classList.add('is-closing');
                        const wrapper = details.querySelector('.collapsible-wrapper');

                        const onTransitionEnd = () => {
                            details.removeAttribute('open');
                            details.classList.remove('is-closing');
                            wrapper.removeEventListener('transitionend', onTransitionEnd);
                        };

                        wrapper.addEventListener('transitionend', onTransitionEnd, { once: true });

                        // Safety timeout
                        setTimeout(() => {
                            if (details.hasAttribute('open') && details.classList.contains('is-closing')) {
                                onTransitionEnd();
                            }
                        }, 400);
                    } else {
                        // OPEN
                        details.setAttribute('open', '');
                        details.classList.add('is-opening');

                        // Double RAF trick: Zwingt den Browser, den Startzustand (0fr) zu rendern,
                        // bevor wir die Klasse entfernen und die Transition auf 1fr beginnt.
                        requestAnimationFrame(() => {
                            requestAnimationFrame(() => {
                                details.classList.remove('is-opening');
                            });
                        });
                    }
                });
            }
        JS;
    }
}
