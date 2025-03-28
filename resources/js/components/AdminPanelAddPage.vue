<template>
    <div
        class="modal fade"
        id="addPage"
        tabindex="-1"
        aria-labelledby="modalLabel"
        aria-hidden="true"
    >
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="modalLabel">
                        {{ title }}
                    </h1>
                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Close"
                    ></button>
                </div>
                <form
                    @submit="addPage"
                    ref="addPage"
                    method="post"
                    class="position-relative"
                >
                    <div class="modal-body">
                        <div class="clearfix">
                            <div
                                class="form-floating mb-3 float-start"
                                style="width: 49%"
                            >
                                <input
                                    @input="editedStringForSlug(page.title)"
                                    v-model="page.title"
                                    name="title"
                                    ref="title"
                                    :class="{
                                        'is-invalid': failedValidation.title,
                                    }"
                                    class="form-control"
                                    id="title"
                                    :placeholder="__('Title')"
                                />
                                <label for="name">{{ __('Title') }}</label>
                                <div class="invalid-feedback">
                                    {{
                                        failedValidation.title
                                            ? failedValidation.title[0]
                                            : ''
                                    }}
                                </div>
                            </div>
                            <div
                                class="input-group mb-3 float-start"
                                style="width: 49%; margin-left: 2%"
                            >
                                <div class="form-floating">
                                    <input
                                        v-model="page.slug"
                                        name="slug"
                                        :class="{
                                            'is-invalid': failedValidation.slug,
                                        }"
                                        class="form-control"
                                        :placeholder="__('Slug')"
                                    />
                                    <label for="slug">{{ __('Slug') }}</label>
                                    <div class="invalid-feedback">
                                        {{
                                            failedValidation.slug
                                                ? failedValidation.slug[0]
                                                : ''
                                        }}
                                    </div>
                                </div>
                                <button
                                    @click="
                                        slugCustomized = false;
                                        editedStringForSlug(page.title);
                                    "
                                    style="max-height: 58px"
                                    class="btn btn-outline-secondary"
                                    type="button"
                                >
                                    {{ __('Reset') }}
                                </button>
                            </div>
                        </div>
                        <div class="border border-2 padding-form-control">
                            <label class="form-label"
                                ><b>{{ __('Body') }}</b></label
                            >
                            <div
                                id="editorjs"
                                :class="{
                                    'is-invalid border-danger':
                                        failedValidation.body,
                                }"
                                class="border color bg-white padding-form-control"
                            ></div>
                            <div class="invalid-feedback">
                                {{
                                    failedValidation.body
                                        ? failedValidation.body[0]
                                        : ''
                                }}
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button
                            ref="closeModal"
                            type="button"
                            class="btn btn-secondary"
                            data-bs-dismiss="modal"
                        >
                            {{ __('Close') }}
                        </button>
                        <button type="submit" class="btn btn-success">
                            <i class="fa-solid fa-plus"></i>
                            {{ title }}
                        </button>
                        <div class="btn-group">
                            <button
                                type="button"
                                class="btn btn-outline-primary"
                                @click="addPage($event, true)"
                            >
                                {{ __('Refresh') }}
                            </button>
                            <button
                                type="button"
                                class="btn btn-outline-primary"
                                @click="previewPage"
                                :disabled="!previewPageUrl"
                            >
                                <i class="fa-solid fa-eye"></i>
                                {{ __('Preview') }}
                            </button>
                        </div>
                        <div
                            v-if="globalError"
                            class="text-bg-danger float-end mt-1"
                        >
                            {{ globalError }}
                        </div>
                        <div
                            v-if="globalSuccess"
                            class="text-bg-success float-end mt-1"
                        >
                            {{ globalSuccess }}
                        </div>
                    </div>
                    <loading-overlay v-if="addingPage" />
                </form>
            </div>
        </div>
    </div>
</template>

<script>
import TextVariantTune from '@editorjs/text-variant-tune';
import ChangeCase from 'editorjs-change-case';
import createGenericInlineTool, {
    ItalicInlineTool,
    StrongInlineTool,
    UnderlineInlineTool,
} from '../editorjs/editorjs-inline-tool';
import Marker from '@editorjs/marker';
import RawTool from '@editorjs/raw';
import editorjsColumns from '@calumk/editorjs-columns';
import Table from '@editorjs/table';
import EditorJS from '@editorjs/editorjs';
import Header from '@editorjs/header';
import ImageTool from '@editorjs/image';
import List from '@editorjs/list';
import AttachesTool from '@editorjs/attaches';
import Embed from '@editorjs/embed';
import Gallery from '@vtchinh/gallery-editorjs';
import SimpleImage from '../editorjs/simple-image-tutorial/simple-image.js';
import TextAlign from '../editorjs/text-align/text-align';
import generateSlug from './generateSlug.js';

export default {
    mixins: [generateSlug],
    props: [
        'adminPanelFetchUrlUrl',
        'adminPanelUploadFileUrl',
        'adminPanelUploadAttachmentUrl',
        'adminPanelAddPageUrl',
        'adminPanelGetPageUrl',
        'getPages',
        'page',
        'pageId',
    ],
    data() {
        return {
            previewPageUrl: '',
            addingPage: false,
            globalError: '',
            globalSuccess: '',
            failedValidation: {},
            title: __('Add page'),
        };
    },
    watch: {
        pageId(newVal) {
            this.setPage();
            this.title = __('Add page');
            if (newVal) {
                this.getPage();
                this.title = __('Edit page');
            }
        },
    },
    methods: {
        previewPage: function () {
            window.open(this.previewPageUrl, '_blank').focus();
        },
        setSlug: function (slug) {
            this.page.slug = slug;
        },
        getPage: function () {
            var that = this;
            axios
                .post(this.adminPanelGetPageUrl, { pageId: this.pageId })
                .then(function (response) {
                    that.setPage(response.data.page);
                });
        },
        setPage: function (data) {
            if (data) {
                if (data.body) {
                    this.editor.blocks.render(JSON.parse(data.body));
                }
                this.page.title = data.title;
                this.page.slug = data.slug;
                this.slugCustomized =
                    this.generateSlug(this.title) !== this.page.slug;
            } else {
                this.editor.blocks.clear();
                this.page.title = '';
                this.page.slug = '';
                this.page.slugCustomized = false;
            }
        },
        addPage: function (e, preview = false) {
            var that = this;
            this.addingPage = true;
            e.preventDefault();
            let formData = new FormData(this.$refs.addPage);
            formData.append('preview', preview);
            if (this.pageId) {
                formData.append('pageId', this.pageId);
            }
            this.editor
                .save()
                .then((body) => {
                    that.failedValidation = {};
                    that.globalError = '';
                    if (body.blocks.length) {
                        formData.append('body', JSON.stringify(body));
                    }
                    axios
                        .post(this.adminPanelAddPageUrl, formData)
                        .then(function (response) {
                            if (preview) {
                                that.previewPageUrl = response.data.previewUrl;
                            } else {
                                that.globalSuccess = `"${
                                    that.$refs.title.value
                                }" ${__('saved!')}`;
                                that.getPages();
                                that.$refs.closeModal.click();
                            }
                        })
                        .catch(function (error) {
                            if (
                                _.has(error, 'response.data.failedValidation')
                            ) {
                                that.failedValidation =
                                    error.response.data.failedValidation;
                            } else {
                                that.globalError = error.message;
                            }
                        })
                        .then(() => {
                            this.addingPage = false;
                        });
                })
                .catch((error) => {
                    console.log('Saving failed: ', error);
                });
        },
    },
    updated() {
        console.debug('updated-AdminPanelAddPage');
    },
    created() {},
    mounted() {
        let tools = {
            alignVariant: TextAlign,
            textVariant: TextVariantTune,
            changeCase: ChangeCase,
            bold: StrongInlineTool,
            italic: ItalicInlineTool,
            underline: UnderlineInlineTool,
            Marker: {
                class: Marker,
                shortcut: 'CMD+SHIFT+M',
            },
            raw: RawTool,
            table: Table,
            embed: {
                class: Embed,
                inlineToolbar: true,
            },
            attaches: {
                class: AttachesTool,
                config: {
                    endpoint: this.adminPanelUploadAttachmentUrl,
                },
            },
            list: {
                class: List,
                inlineToolbar: true,
                config: {
                    defaultStyle: 'unordered',
                },
            },
            imageExternal: {
                class: SimpleImage,
                inlineToolbar: true,
                config: {
                    placeholder: __('Paste an image URL') + '...',
                },
            },
            gallery: {
                class: Gallery,
                config: {
                    endpoints: {
                        byFile: this.adminPanelUploadFileUrl, // Your backend file uploader endpoint
                        byUrl: this.adminPanelFetchUrlUrl, // Your endpoint that provides uploading by Url
                    },
                    additionalRequestData: {
                        thumbnail: true,
                        displayType: 'gallery',
                    },
                },
            },
            image: {
                class: ImageTool,
                config: {
                    endpoints: {
                        byFile: this.adminPanelUploadFileUrl, // Your backend file uploader endpoint
                        byUrl: this.adminPanelFetchUrlUrl, // Your endpoint that provides uploading by Url
                    },
                    // uploader: {
                    //     uploadByFile(file) {
                    //         const form = new FormData();
                    //         form.append('file', file);
                    //         form.append('pageId', that.pageId);
                    //         return axios
                    //             .post(that.adminPanelUploadFileUrl, form)
                    //             .then(function (response) {
                    //                 return response.data;
                    //             });
                    //     },
                    //     uploadByUrl(url) {},
                    // },
                },
            },
            header: {
                class: Header,
                config: {
                    placeholder: __('Enter a header'),
                    levels: [2, 3, 4],
                    defaultLevel: 2,
                },
                tunes: ['alignVariant'],
            },
            paragraph: {
                tunes: ['textVariant', 'alignVariant'],
            },
        };
        let columnTools = _.clone(tools);
        tools.columns = {
            class: editorjsColumns,
            config: {
                EditorJsLibrary: EditorJS,
                tools: columnTools,
            },
        };
        this.editor = new EditorJS({
            minHeight: 250,
            tools: tools,
        });
    },
};
</script>
