import { defineConfig } from 'vite';

export default defineConfig({
    build: {
        outDir: 'resources/dist',
        emptyOutDir: true,
        lib: {
            entry: 'resources/js/index.js',
            name: 'FilamentEditorJs',
            // Force exact filename to match ServiceProvider
            fileName: () => 'filament-editor-js.js',
            formats: ['iife'],
        },
        rollupOptions: {
            output: {
                assetFileNames: (assetInfo) => {
                    if (assetInfo.name === 'style.css') return 'filament-editor-js.css';
                    return assetInfo.name;
                },
            },
        },
    },
});
