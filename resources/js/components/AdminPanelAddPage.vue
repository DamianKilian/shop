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
                                        failedValidation.description,
                                }"
                                class="border color bg-white padding-form-control"
                            ></div>
                            <div class="invalid-feedback">
                                {{
                                    failedValidation.description
                                        ? failedValidation.description[0]
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
import EditorJS from '@editorjs/editorjs';
import Header from '@editorjs/header';
import ImageTool from '@editorjs/image';
import List from '@editorjs/list';
import generateSlug from './generateSlug.js';

export default {
    mixins: [generateSlug],
    props: [
        'adminPanelFetchUrlUrl',
        'adminPanelUploadFileUrl',
        'adminPanelAddPageUrl',
        'adminPanelGetPageUrl',
        'getPages',
        'page',
        'pageId',
    ],
    data() {
        return {
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
        addPage: function (e) {
            var that = this;
            this.addingPage = true;
            e.preventDefault();
            let formData = new FormData(this.$refs.addPage);
            // formData.append('filesArr', JSON.stringify(this.filesArr));
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
                            that.globalSuccess = `"${
                                that.$refs.title.value
                            }" ${__('saved!')}`;
                            that.getPages();
                            that.$refs.closeModal.click();
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
        var that = this;
        this.editor = new EditorJS({
            minHeight: 250,
            maxWidth: 250,
            width: 111,
            tools: {
                list: {
                    class: List,
                    inlineToolbar: true,
                    config: {
                        defaultStyle: 'unordered',
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
                },
            },
        });
    },
};
</script>
