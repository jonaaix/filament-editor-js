<?php

declare(strict_types=1);

namespace Aaix\FilamentEditorJs\Support\HtmlRenderer\Contracts;

interface BlockRenderer
{
    public function render(array $data): string;

    /**
     * Returns the CSS required for this block.
     * Use the placeholder selector %scope% to reference the wrapper class.
     * Example: "%scope% h2 { font-size: 2rem; }"
     */
    public static function getCss(): string;

    /**
     * Returns the JS required for this block (e.g. event listeners).
     * Should include <script> tags and logic to prevent duplicate execution.
     */
    public static function getJs(): string;
}
