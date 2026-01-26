<?php

declare(strict_types=1);

namespace Aaix\FilamentEditorJs\Forms\Components;

use Closure;
use Filament\Forms\Components\Field;
use Illuminate\Support\Facades\URL;

class EditorJs extends Field
{
    protected string $view = 'aaix-editorjs::forms.components.editor-js';

    protected array $tools = [
        'header',
        'list',
        'checklist',
        'quote',
        'table',
        'delimiter',
        'code',
        'collapsible',
        'gallery',
        'alert',
    ];

    protected string|Closure|null $placeholder = null;
    protected string|Closure|null $imageDisk = 'public';
    protected string|Closure|null $imageDirectory = null;

    public function tools(array $tools): static
    {
        $this->tools = $tools;

        return $this;
    }

    public function getTools(): array
    {
        return $this->tools;
    }

    public function placeholder(string|Closure|null $placeholder): static
    {
        $this->placeholder = $placeholder;

        return $this;
    }

    public function getPlaceholder(): string
    {
        return $this->evaluate($this->placeholder) ?? __('filament-editor-js::editorjs.placeholder');
    }

    public function imageDisk(string|Closure|null $disk): static
    {
        $this->imageDisk = $disk;

        return $this;
    }

    public function imageDirectory(string|Closure|null $directory): static
    {
        $this->imageDirectory = $directory;

        return $this;
    }

    public function getUploadConfig(): array
    {
        // Generiert signed URL, die sicherstellt, dass disk/directory nicht manipuliert wurden
        $url = URL::signedRoute('filament-editor-js.upload', [
            'disk' => $this->evaluate($this->imageDisk),
            'directory' => $this->evaluate($this->imageDirectory),
        ]);

        return [
            'url' => $url,
        ];
    }

    public function getTranslations(): array
    {
        return [
            'ui' => [
                'blockTunes' => [
                    'toggler' => [
                        'Click to tune' => __('filament-editor-js::editorjs.tunes.toggler'),
                        'Drag to move' => __('filament-editor-js::editorjs.tunes.drag'),
                    ],
                ],
                'inlineToolbar' => [
                    'converter' => [
                        'Convert to' => __('filament-editor-js::editorjs.inline.convert_to'),
                    ],
                ],
                'toolbar' => [
                    'toolbox' => [
                        'Add' => __('filament-editor-js::editorjs.toolbox.add'),
                    ],
                ],
            ],
            'toolNames' => [
                'text' => __('filament-editor-js::editorjs.tool_names.text'),
                'header' => __('filament-editor-js::editorjs.tool_names.heading'),
                'list' => __('filament-editor-js::editorjs.tool_names.list'),
                'checklist' => __('filament-editor-js::editorjs.tool_names.checklist'),
                'quote' => __('filament-editor-js::editorjs.tool_names.quote'),
                'code' => __('filament-editor-js::editorjs.tool_names.code'),
                'delimiter' => __('filament-editor-js::editorjs.tool_names.delimiter'),
                'collapsible' => __('filament-editor-js::editorjs.tool_names.collapsible'),
                'table' => __('filament-editor-js::editorjs.tool_names.table'),
                'alert' => __('filament-editor-js::editorjs.tool_names.alert') ?? 'Alert',
                'gallery' => __('filament-editor-js::editorjs.tool_names.gallery') ?? 'Gallery',
            ],
            'tools' => [
                'delete' => __('filament-editor-js::editorjs.tools.delete'),
                'moveUp' => __('filament-editor-js::editorjs.tools.move_up'),
                'moveDown' => __('filament-editor-js::editorjs.tools.move_down'),
            ],
        ];
    }
}
