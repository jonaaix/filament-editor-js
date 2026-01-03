<h1 align="center">Filament Editor.js</h1>

<p align="center">
  Filament integration for <a href="https://editorjs.io">Editor.js</a> with a standalone HTML renderer.
</p>

<p align="center">
  <a href="https://packagist.org/packages/aaix/filament-editor-js">
    <img src="https://img.shields.io/packagist/v/aaix/filament-editor-js.svg?style=flat-square" alt="Latest Version on Packagist">
  </a>
  <a href="https://packagist.org/packages/aaix/filament-editor-js">
    <img src="https://img.shields.io/packagist/dt/aaix/filament-editor-js.svg?style=flat-square" alt="Total Downloads">
  </a>
  <a href="https://github.com/aaix/filament-editor-js/blob/main/LICENSE">
    <img src="https://img.shields.io/packagist/l/aaix/filament-editor-js.svg?style=flat-square" alt="License">
  </a>
</p>

---

<p align="center">
  <a href="https://github.com/jonaaix/filament-editor-js">
    <img src="https://raw.githubusercontent.com/jonaaix/filament-editor-js/main/demo.webp" alt="Filament Editor.js Logo">
  </a>
</p>

## Installation

```bash
composer require aaix/filament-editor-js

php artisan filament:assets
```

## Usage

Use the `EditorJs` component in your Filament Resource form.
You can use the included `HtmlRenderer` to render the content (including Minified CSS/JS) without Alpine dependencies in any location.

```php
use Aaix\FilamentEditorJs\Forms\Components\EditorJs;
use Aaix\FilamentEditorJs\Support\HtmlRenderer\HtmlRenderer;
use Filament\Forms\Components\Placeholder;
use Filament\Schemas\Schema;
use Illuminate\Support\HtmlString;

public static function configure(Schema $schema): Schema
{
    return $schema
        ->schema([
            EditorJs::make('content')
                ->columnSpanFull(),

            // Preview the Server-Side Rendered HTML
            Placeholder::make('html_preview')
                ->label('Rendered Preview')
                ->content(function ($get) {
                    $jsonState = $get('content');

                    // Renders structured HTML from Editor.js JSON
                    $html = HtmlRenderer::render($jsonState);

                    return new HtmlString(
                        '<div style="max-width:50rem;border:1px dashed #d1d5db;padding:2rem;border-radius:0.5rem;">' .
                        $html .
                        '</div>'
                    );
                })
                ->columnSpanFull(),
        ]);
}
```

## Frontend Rendering

To render the content in your Blade views (e.g., Blog Post):

```blade
{!! \Aaix\FilamentEditorJs\Support\HtmlRenderer\HtmlRenderer::render($post->content) !!}
```

*The renderer automatically injects required CSS and vanilla JS for galleries/accordions.*

## API / Headless Usage

Since the renderer produces self-contained HTML (including styles and scripts), you can easily serve it via an API:

```php
use Aaix\FilamentEditorJs\Support\HtmlRenderer\HtmlRenderer;
use App\Models\Post;
use Illuminate\Support\Facades\Route;

Route::get('/api/posts/{post}', function (Post $post) {
    return response()->json([
        'id' => $post->id,
        'title' => $post->title,
        'content_html' => HtmlRenderer::render($post->content), // Returns fully rendered HTML string
    ]);
});
```

## Configuration
### Image Upload location
The plugin will automatically create a scalable directory structure for all uploaded images and also creates different
image sizes for the gallery block, used in srcset attributes.
```php
use Aaix\FilamentEditorJs\Forms\Components\EditorJs;
EditorJs::make('content')
        ->imageDisk('s3') // Defaults to 'public'
        ->imageDirectory('my_photos') // Defaults to 'editorjs-images'
```

The directory structure would look like this:
```
editorjs-images
└── 0d
    └── e3
        └── 1a
            ├── f2-880d-4e7b-b402-b1b2cc685e6f_1k.webp
            ├── f2-880d-4e7b-b402-b1b2cc685e6f_2k.webp
            ├── f2-880d-4e7b-b402-b1b2cc685e6f_3k.webp
            ├── f2-880d-4e7b-b402-b1b2cc685e6f_4k.webp
            ├── f2-880d-4e7b-b402-b1b2cc685e6f_500.webp
            └── f2-880d-4e7b-b402-b1b2cc685e6f_original.jpg
```

## Included Tools & Plugins

The editor comes pre-configured with the following block tools:

* **Typography:** Header, Paragraph, Quote, Code, Delimiter.
* **Lists:** Unordered, Ordered, Checklist.
* **Structure:** Table, Collapsible (Accordion).
* **Media:** Gallery (Masonry & Slider layouts with Lightbox).

```
@editorjs/checklist
@editorjs/code
@editorjs/delimiter
@editorjs/editorjs
@editorjs/header
@editorjs/list
@editorjs/quote
@editorjs/table
@kiberpro/editorjs-gallery
editorjs-collapsible-block
```

## Contributing
Need another editor.js plugin? Contributions are welcome!
Please submit a Pull Request or open an issue to discuss your ideas.

## License
The MIT License (MIT). Please see [License File](https://www.google.com/search?q=LICENSE) for more information.
