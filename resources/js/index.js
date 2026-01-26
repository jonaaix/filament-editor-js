import '../css/editor.css';
import EditorJS from '@editorjs/editorjs';
import Header from '@editorjs/header';
import List from '@editorjs/list';
import Checklist from '@editorjs/checklist';
import Quote from '@editorjs/quote';
import Table from '@editorjs/table';
import Delimiter from '@editorjs/delimiter';
import CodeTool from '@editorjs/code';
import Accordion from 'editorjs-collapsible-block';
import Gallery from '@kiberpro/editorjs-gallery';
import Alert from 'editorjs-alert';
import Sortable from 'sortablejs';

document.addEventListener('alpine:init', () => {
    Alpine.data('filamentEditorJs', ({
                                         state,
                                         statePath,
                                         readOnly,
                                         placeholder,
                                         i18n,
                                         tools,
                                         uploadConfig
                                     }) => ({
        instance: null,
        state: state,
        toolsConfig: tools,
        saveTimeout: null,

        init() {
            this.mountEditor();
            this.$watch('state', (newState) => { });
        },

        mountEditor() {
            if (this.instance) return;

            // CSRF Token for Laravel
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            const library = {
                header: {
                    class: Header,
                    inlineToolbar: true,
                    toolbox: { title: i18n.toolNames?.header || 'Heading' },
                    config: {
                        placeholder: i18n.toolNames?.header || 'Heading',
                        levels: [1, 2, 3, 4, 5],
                        defaultLevel: 2
                    },
                },
                list: {
                    class: List,
                    inlineToolbar: true,
                    config: { defaultStyle: 'unordered' },
                    toolbox: { title: i18n.toolNames?.list || 'List' }
                },
                checklist: {
                    class: Checklist,
                    inlineToolbar: true,
                    toolbox: { title: i18n.toolNames?.checklist || 'Checklist' }
                },
                quote: {
                    class: Quote,
                    inlineToolbar: true,
                    config: {
                        quotePlaceholder: i18n.toolNames?.quote || 'Quote',
                        captionPlaceholder: i18n.toolNames?.quoteAuthor || 'Author'
                    },
                    toolbox: { title: i18n.toolNames?.quote || 'Quote' }
                },
                table: {
                    class: Table,
                    inlineToolbar: true,
                    toolbox: { title: i18n.toolNames?.table || 'Table' }
                },
                delimiter: {
                    class: Delimiter,
                    toolbox: { title: i18n.toolNames?.delimiter || 'Delimiter' }
                },
                code: {
                    class: CodeTool,
                    toolbox: { title: i18n.toolNames?.code || 'Code' }
                },
                alert: {
                    class: Alert,
                    inlineToolbar: true,
                    toolbox: { title: i18n.toolNames?.alert || 'Alert' },
                    config: {
                        defaultType: 'info',
                        alertTypes: ['primary', 'secondary', 'info', 'success', 'warning', 'danger', 'light', 'dark'],
                    }
                },
                collapsible: {
                    class: Accordion,
                    inlineToolbar: true,
                    toolbox: {
                        title: i18n.toolNames?.accordion || 'Accordion'
                    },
                    config: {
                        maxBlockCount: 30,
                        defaultExpanded: false,
                        disableAnimation: false,
                        overrides: {
                            styles: {
                                blockContent: 'border-left: 1px solid #ccc;border-right: 1px solid #ccc;',
                                lastBlockContent: 'border-bottom: 1px solid #ccc;margin-bottom: 1rem;',
                                insideContent: 'background-color: #e9e0c324;',
                            },
                        },
                    },
                },
                gallery: {
                    class: Gallery,
                    inlineToolbar: true,
                    config: {
                        endpoints: {
                            byFile: uploadConfig.url,
                        },
                        additionalRequestHeaders: {
                            'X-CSRF-TOKEN': csrfToken
                        },
                        sortableJs: Sortable
                    },
                    toolbox: {
                        title: i18n.toolNames?.gallery || 'Gallery'
                    }
                }
            };

            const enabledTools = {
                paragraph: {
                    inlineToolbar: true,
                    config: { preserveBlank: true }
                }
            };

            if (Array.isArray(this.toolsConfig)) {
                this.toolsConfig.forEach(toolKey => {
                    if (library[toolKey]) {
                        enabledTools[toolKey] = library[toolKey];
                    }
                });
            }

            let initialData = {};
            try {
                if (typeof this.state === 'string') {
                    initialData = JSON.parse(this.state);
                } else {
                    initialData = this.state || {};
                }
            } catch (e) {
                console.error('Editor.js JSON parse error:', e);
                initialData = {};
            }

            this.instance = new EditorJS({
                holder: this.$el.querySelector('[id$="-editorjs"]'),
                data: initialData,
                readOnly: readOnly,
                placeholder: placeholder,
                tools: enabledTools,
                i18n: { messages: i18n },
                defaultBlock: 'paragraph',

                onChange: (api, event) => {
                    if (readOnly) return;
                    clearTimeout(this.saveTimeout);
                    this.saveTimeout = setTimeout(async () => {
                        try {
                            const outputData = await api.saver.save();
                            this.state = outputData;
                        } catch (error) {
                            console.error('Editor.js saving failed:', error);
                        }
                    }, 300);
                },
            });
        },

        destroy() {
            if (this.instance) {
                try {
                    if (typeof this.instance.destroy === 'function') {
                        this.instance.destroy();
                    }
                } catch (e) {
                    console.error('Editor.js destroy error:', e);
                }
                this.instance = null;
            }
        }
    }));
});
