<p align="center">
  <a href="https://github.com/aaix/filament-editor-js">
    <img src="https://raw.githubusercontent.com/aaix/filament-editor-js/main/demo.webp" alt="Filament Editor.js Logo" width="170">
  </a>
</p>

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
                ->label('Rendered HTML Output (Server-Side)')
                ->content(function ($get) {
                    $jsonState = $get('content');

                    // Renders structured HTML from Editor.js JSON
                    $html = HtmlRenderer::render($jsonState);

                    return new HtmlString(
                        '<div class="prose max-w-none dark:prose-invert border border-dashed border-gray-300 p-8 rounded-lg">' .
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

## Included Tools & Plugins

The editor comes pre-configured with the following block tools:

* **Typography:** Header, Paragraph, Quote, Code, Delimiter.
* **Lists:** Unordered, Ordered, Checklist.
* **Structure:** Table, Collapsible (Accordion).
* **Media:** Gallery (Masonry & Slider layouts with Lightbox).

## Contributing
Need another editor.js plugin? Contributions are welcome!
Please submit a Pull Request or open an issue to discuss your ideas.

## License
The MIT License (MIT). Please see [License File](https://www.google.com/search?q=LICENSE) for more information.
