<?php

declare(strict_types=1);

namespace Aaix\FilamentEditorJs\Support\HtmlRenderer\Renderers;

use Aaix\FilamentEditorJs\Support\HtmlRenderer\Contracts\BlockRenderer;
use Illuminate\Support\Str;

class GalleryRenderer implements BlockRenderer
{
    /**
     * Centralized Path Definition
     * Returns the absolute path to the renderer resources directory.
     */
    private static function getResourcesDirectory(): string
    {
        return __DIR__ . '/../../../../resources/renderer/gallery/';
    }

    /**
     * Helper to safely load asset content
     */
    private static function loadAssetContent(string $filename): string
    {
        $path = self::getResourcesDirectory() . $filename;

        if (! file_exists($path)) {
            // Development fallback hint (visible in HTML source)
            return "";
        }

        return file_get_contents($path);
    }

    public function render(array $data): string
    {
        $images = $data['images'] ?? $data['files'] ?? [];
        if (empty($images)) {
            return '';
        }

        $style = $data['style'] ?? 'fit';
        $caption = $data['caption'] ?? null;
        $count = count($images);

        // Data Preparation
        $jsImages = array_map(function($img) {
            $variants = $img['variants'] ?? [];
            $downloadUrl = $variants['_original']
                ?? $variants['_4k']
                ?? $variants['_3k']
                ?? $variants['_2k']
                ?? $variants['_1k']
                ?? $variants['_500']
                ?? '';

            return [
                '_500' => $variants['_500'] ?? '',
                '_1k'  => $variants['_1k'] ?? '',
                '_2k'  => $variants['_2k'] ?? '',
                '_3k'  => $variants['_3k'] ?? '',
                '_4k'  => $variants['_4k'] ?? '',
                '_original' => $downloadUrl,
                'caption' => $img['caption'] ?? '',
                'width'   => $img['width'] ?? 0,
                'height'  => $img['height'] ?? 0,
            ];
        }, $images);

        foreach ($jsImages as &$image) {
            foreach ($image as &$value) {
                if (is_string($value) && Str::startsWith($value, '/') && !Str::startsWith($value, '//')) {
                    $value = rtrim(config('app.url'), '/') . $value;
                }
            }
        }
        unset($image, $value);

        $jsonImages = htmlspecialchars(json_encode($jsImages), ENT_QUOTES, 'UTF-8');

        // 1. Load Main Layout
        if ($style === 'slider') {
            $layoutTemplate = self::loadAssetContent('gallery-slider.html');
        } else {
            $layoutTemplate = self::loadAssetContent('gallery-masonry.html');
            if ($count < 10) {
                $layoutTemplate = str_replace(
                    'class="gallery-masonry"',
                    'class="gallery-grid"',
                    $layoutTemplate
                );
            }
        }

        // 2. Load Lightbox Template (Only loaded once per page ideally, but safe to include here as JS will deduplicate)
        $lightboxTemplate = self::loadAssetContent('gallery-lightbox.html');

        // 3. Caption
        $captionHtml = '';
        if (!empty($caption)) {
            $captionRaw = self::loadAssetContent('gallery-caption.html');
            $captionHtml = str_replace('{{caption}}', htmlspecialchars($caption), $captionRaw);
        }

        // 4. Final Assembly
        // We use data-aaix-gallery to hold the config, and a class hook .aaix-gallery-block
        return <<<HTML
            <div class="gallery-wrapper aaix-gallery-block" data-aaix-gallery="{$jsonImages}" data-style="{$style}">
                {$captionHtml}
                {$layoutTemplate}
                <template class="js-lightbox-template">
                    {$lightboxTemplate}
                </template>
            </div>
        HTML;
    }

    public static function getCss(): string
    {
        return self::loadAssetContent('gallery.css');
    }

    public static function getJs(): string
    {
        return self::loadAssetContent('gallery.js');
    }
}
