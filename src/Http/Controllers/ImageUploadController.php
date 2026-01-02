<?php

declare(strict_types=1);

namespace Aaix\FilamentEditorJs\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class ImageUploadController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        if ($request->hasFile('image') && ! $request->file('image')->isValid()) {
            return response()->json(['success' => 0, 'error' => 'Upload Error'], 422);
        }

        $request->validate([
            'image' => ['required', 'image', 'max:51200'], // 50MB Max
        ]);

        $disk = $request->query('disk', 'public');
        $directory = $request->query('directory', 'editorjs-images');

        /** @var \Illuminate\Http\UploadedFile $file */
        $file = $request->file('image');
        $uuid = Str::uuid()->toString();

        // ARCHITECTURE V3: Flat Hash Structure + Consistent Naming
        // UUID: 9b3998e4-5809-4b87-9444-90871fe9b01e
        // Path: 9b/39/98/ (3 levels for distribution)
        // File: e4-5809-4b87-9444-90871fe9b01e_{size}.webp

        $hashPath = substr($uuid, 0, 2) . '/' . substr($uuid, 2, 2) . '/' . substr($uuid, 4, 2);
        $filePrefix = substr($uuid, 6); // The unique rest of the UUID

        $storagePath = "{$directory}/{$hashPath}";

        $storage = Storage::disk($disk);
        $urls = [];

        try {
            // 1. Save ORIGINAL (Raw Upload)
            // Name: {uuid_rest}_original.{ext}
            $originalExt = $file->getClientOriginalExtension();
            $originalName = "{$filePrefix}_original.{$originalExt}";

            $storage->putFileAs($storagePath, $file, $originalName, 'public');
            // Store using the consistent key '_original'
            $urls['_original'] = parse_url($storage->url("{$storagePath}/{$originalName}"), PHP_URL_PATH);

            // 2. Image Processing
            $manager = new ImageManager(new Driver());
            $image = $manager->read($file);

            if ($image->width() > 4000 || $image->height() > 4000) {
                $image->scaleDown(width: 4000, height: 4000);
            }

            // CONSISTENT NAMING SCHEME: _4k, _3k, _2k, _1k, _500
            $names = [
                '_4k'  => "{$filePrefix}_4k.webp",
                '_3k'  => "{$filePrefix}_3k.webp",
                '_2k'  => "{$filePrefix}_2k.webp",
                '_1k'  => "{$filePrefix}_1k.webp",
                '_500' => "{$filePrefix}_500.webp", // New Thumbsize 500px
            ];

            // Helper for Saving Variants
            $saveVariant = function($key, $width) use ($image, $names, $storagePath, $storage, &$urls) {
                $variant = clone $image;

                if ($image->width() > $width) {
                    $variant->scaleDown(width: $width);
                }

                $encoded = $variant->toWebp(quality: 75);

                $path = "{$storagePath}/" . $names[$key];
                $storage->put($path, (string) $encoded, 'public');

                $urls[$key] = parse_url($storage->url($path), PHP_URL_PATH);
            };

            // 3. Generate Variants
            $saveVariant('_4k', 4000);
            $saveVariant('_3k', 3000);
            $saveVariant('_2k', 2000);
            $saveVariant('_1k', 1000);
            $saveVariant('_500', 500);

            return response()->json([
                'success' => 1,
                'file' => [
                    'url' => $urls['_500'], // Editor.js Preview (Thumb)
                    'variants' => $urls,    // Full Data
                    'width' => $image->width(),
                    'height' => $image->height(),
                    'name' => pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME) . '.webp',
                ],
            ]);

        } catch (\Throwable $e) {
            report($e);
            return response()->json(['success' => 0, 'error' => $e->getMessage()], 500);
        }
    }
}
