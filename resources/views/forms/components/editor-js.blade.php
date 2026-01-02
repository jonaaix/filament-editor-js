@php
    $statePath = $field->getStatePath();
    $tools = json_encode($field->getTools());
    $i18n = json_encode($field->getTranslations());
    $uploadConfig = json_encode($field->getUploadConfig());
@endphp

<x-dynamic-component
    :component="$field->getFieldWrapperView()"
    :field="$field"
>
    <div
        wire:ignore
        x-data="filamentEditorJs({
            state: $wire.entangle('{{ $statePath }}'),
            statePath: '{{ $statePath }}',
            readOnly: {{ $field->isDisabled() ? 'true' : 'false' }},
            placeholder: '{{ $field->getPlaceholder() }}',
            i18n: {{ $i18n }},
            tools: {{ $tools }},
            uploadConfig: {{ $uploadConfig }}
        })"
        x-on:keydown.cmd.s.prevent
        x-on:keydown.ctrl.s.prevent
        class="filament-editorjs-wrapper prose max-w-none dark:prose-invert"
    >
        <div
            id="{{ $field->getId() }}-editorjs"
            class="filament-editorjs-box"
        ></div>
    </div>
</x-dynamic-component>
