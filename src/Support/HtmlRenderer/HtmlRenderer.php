<?php

declare(strict_types=1);

namespace Aaix\FilamentEditorJs\Support\HtmlRenderer;

use Aaix\FilamentEditorJs\Support\HtmlRenderer\Contracts\BlockRenderer;
use Aaix\FilamentEditorJs\Support\HtmlRenderer\Renderers\AlertRenderer;
use Aaix\FilamentEditorJs\Support\HtmlRenderer\Renderers\CollapsibleRenderer;
use Aaix\FilamentEditorJs\Support\HtmlRenderer\Renderers\ChecklistRenderer;
use Aaix\FilamentEditorJs\Support\HtmlRenderer\Renderers\CodeRenderer;
use Aaix\FilamentEditorJs\Support\HtmlRenderer\Renderers\DelimiterRenderer;
use Aaix\FilamentEditorJs\Support\HtmlRenderer\Renderers\GalleryRenderer;
use Aaix\FilamentEditorJs\Support\HtmlRenderer\Renderers\HeaderRenderer;
use Aaix\FilamentEditorJs\Support\HtmlRenderer\Renderers\ListRenderer;
use Aaix\FilamentEditorJs\Support\HtmlRenderer\Renderers\ParagraphRenderer;
use Aaix\FilamentEditorJs\Support\HtmlRenderer\Renderers\QuoteRenderer;
use Aaix\FilamentEditorJs\Support\HtmlRenderer\Renderers\TableRenderer;
use MatthiasMullie\Minify;

class HtmlRenderer
{
    protected const SCOPE_CLASS = 'filament-editorjs-content';

    protected static array $renderers = [
        'header' => HeaderRenderer::class,
        'paragraph' => ParagraphRenderer::class,
        'list' => ListRenderer::class,
        'bulletList' => ListRenderer::class,
        'orderedList' => ListRenderer::class,
        'checklist' => ChecklistRenderer::class,
        'quote' => QuoteRenderer::class,
        'delimiter' => DelimiterRenderer::class,
        'table' => TableRenderer::class,
        'code' => CodeRenderer::class,
        'alert' => AlertRenderer::class,
        'collapsible' => CollapsibleRenderer::class,
        'gallery' => GalleryRenderer::class,
    ];

    /**
     * Main Entry Point: Orchestrates the rendering pipeline.
     */
    public static function render(string|array|null $data): string
    {
        // 1. Input Normalization
        $blocks = self::normalizeInput($data);
        if (empty($blocks)) {
            return '';
        }

        // 2. Tree Hydration (Transform Flat -> Nested)
        $blockTree = self::buildBlockTree($blocks);

        // 3. Dependency Analysis (Collect CSS for ALL nested blocks)
        $requiredRenderers = self::scanForRenderers($blockTree);

        // 4. Output Generation
        $css = self::compileCss($requiredRenderers);
        $js = self::compileJs($requiredRenderers);
        $html = self::renderBlocks($blockTree);

        // 5. Encapsulation
        return <<<HTML
            <style>{$css}</style>
            <div class="fi-editorjs-content">
                {$html}
            </div>
            <script>{$js}</script>
            HTML;
    }

    /**
     * Public Helper for Renderers (e.g. Accordion) to render their children.
     * Does NOT generate CSS or Wrappers, purely HTML fragments.
     */
    public static function renderBlocks(array $blocks): string
    {
        $html = [];
        foreach ($blocks as $block) {
            $type = $block['type'] ?? null;

            if ($type && isset(self::$renderers[$type])) {
                /** @var BlockRenderer $instance */
                $instance = new (self::$renderers[$type])();
                $html[] = $instance->render($block['data']);
            }
        }
        return implode('', $html);
    }

    // --- Pipeline Steps ---

    protected static function normalizeInput(string|array|null $data): array
    {
        if (empty($data)) {
            return [];
        }
        if (is_string($data)) {
            $data = json_decode($data, true);
        }
        return $data['blocks'] ?? [];
    }

    /**
     * PHASE 2: Hydration
     * Recursively transforms a flat list into a tree structure based on block settings.
     */
    protected static function buildBlockTree(array $blocks): array
    {
        $tree = [];
        $count = count($blocks);

        for ($i = 0; $i < $count; $i++) {
            $block = $blocks[$i];
            $type = $block['type'] ?? '';

            // Logic for Container Blocks (Lookahead)
            if (self::isContainerBlock($block)) {
                $consumeCount = (int) ($block['data']['settings']['blockCount'] ?? 0);

                // Extract children from the flat list
                $childrenSlice = [];
                for ($j = 1; $j <= $consumeCount; $j++) {
                    if (isset($blocks[$i + $j])) {
                        $childrenSlice[] = $blocks[$i + $j];
                    }
                }

                // Recursively build tree for children (in case of nested accordions)
                if (! empty($childrenSlice)) {
                    $childrenSlice = self::buildBlockTree($childrenSlice);
                }

                // Inject structural data
                $block['data']['_inner_blocks'] = $childrenSlice;
                $tree[] = $block;

                // Advance main pointer
                $i += $consumeCount;
            } else {
                $tree[] = $block;
            }
        }

        return $tree;
    }

    /**
     * PHASE 3: Analysis
     * Recursively scans the hydrated tree to find every used Renderer class.
     * @return array<string, bool> Map of ClassName => true
     */
    protected static function scanForRenderers(array $blocks): array
    {
        $classes = [];

        foreach ($blocks as $block) {
            $type = $block['type'] ?? null;

            if ($type && isset(self::$renderers[$type])) {
                $classes[self::$renderers[$type]] = true;
            }

            // Recursive Scan for Inner Blocks
            if (! empty($block['data']['_inner_blocks'])) {
                $classes += self::scanForRenderers($block['data']['_inner_blocks']);
            }
        }

        return $classes;
    }

    protected static function compileCss(array $rendererMap): string
    {
        $minifier = new Minify\CSS();

        // Base Scoped Styles
        $minifier->add(".fi-editorjs-content { font-family: inherit; line-height: 1.6; color: inherit; }");
        $minifier->add(".fi-editorjs-content * { box-sizing: border-box; }");

        foreach (array_keys($rendererMap) as $class) {
            /** @var BlockRenderer $class */
            $blockCss = $class::getCss();
            $scopedCss = str_replace('%scope%', '.fi-editorjs-content', $blockCss);
            $minifier->add($scopedCss);
        }

        return $minifier->minify();
    }

    protected static function compileJs(array $rendererMap): string
    {
        $minifier = new Minify\JS();

        foreach (array_keys($rendererMap) as $class) {
            /** @var BlockRenderer $class */
            $jsContent = $class::getJs();

            if (! empty($jsContent)) {
                $minifier->add($jsContent);
            }
        }

        return $minifier->minify();
    }

    protected static function isContainerBlock(array $block): bool
    {
        $type = $block['type'] ?? '';
        return $type === 'collapsible' && isset($block['data']['settings']['blockCount']);
    }

    public static function register(string $type, string $rendererClass): void
    {
        if (! is_subclass_of($rendererClass, BlockRenderer::class)) {
            throw new \InvalidArgumentException("Renderer class must implement BlockRenderer interface.");
        }
        self::$renderers[$type] = $rendererClass;
    }
}
