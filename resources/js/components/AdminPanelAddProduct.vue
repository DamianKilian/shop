<template>
    <div
        class="modal fade"
        id="addProduct"
        tabindex="-1"
        aria-labelledby="modalLabel"
        aria-hidden="true"
    >
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="modalLabel">
                        {{ submitBtnText }}
                    </h1>
                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Close"
                    ></button>
                </div>
                <form
                    @submit="addProduct"
                    ref="addProduct"
                    method="post"
                    enctype="multipart/form-data"
                    class="position-relative"
                >
                    <div class="modal-body">
                        <div class="clearfix">
                            <div
                                class="form-floating mb-3 float-start"
                                style="width: 49%"
                            >
                                <input
                                    v-model="title"
                                    @input="editedStringForSlug(title)"
                                    ref="title"
                                    name="title"
                                    :class="{
                                        'is-invalid': failedValidation.title,
                                    }"
                                    class="form-control"
                                    id="title"
                                    :placeholder="__('Title')"
                                />
                                <label for="title">{{ __('Title') }}</label>
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
                                        v-model="slug"
                                        @input="slugCustomized = true"
                                        ref="slug"
                                        name="slug"
                                        :class="{
                                            'is-invalid': failedValidation.slug,
                                        }"
                                        class="form-control"
                                        id="slug"
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
                                        editedStringForSlug(title);
                                    "
                                    style="max-height: 58px"
                                    class="btn btn-outline-secondary"
                                    type="button"
                                >
                                    {{ __('Reset') }}
                                </button>
                            </div>
                        </div>
                        <div class="clearfix">
                            <div
                                class="form-floating mb-3 float-start"
                                style="width: 49%"
                            >
                                <input
                                    ref="price"
                                    name="price"
                                    :class="{
                                        'is-invalid': failedValidation.price,
                                    }"
                                    class="form-control"
                                    id="price"
                                    :placeholder="__('Price')"
                                />
                                <label for="price">{{ __('Price') }}</label>
                                <div class="invalid-feedback">
                                    {{
                                        failedValidation.price
                                            ? failedValidation.price[0]
                                            : ''
                                    }}
                                </div>
                            </div>
                            <div
                                class="form-floating mb-3 float-end"
                                style="width: 49%; margin-left: 2%"
                            >
                                <input
                                    ref="quantity"
                                    name="quantity"
                                    :class="{
                                        'is-invalid': failedValidation.quantity,
                                    }"
                                    class="form-control"
                                    id="quantity"
                                    :placeholder="__('Quantity')"
                                />
                                <label for="quantity">{{
                                    __('Quantity')
                                }}</label>
                                <div class="invalid-feedback">
                                    {{
                                        failedValidation.quantity
                                            ? failedValidation.quantity[0]
                                            : ''
                                    }}
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="category-select" class="form-label"
                                ><b>{{ __('Category select') }}</b></label
                            >
                            <select
                                name="categoryId"
                                v-model="selectedCategoryId"
                                @change="
                                    getProductFilterOptions(selectedCategoryId)
                                "
                                id="category-select"
                                :class="{
                                    'is-invalid': failedValidation.categoryId,
                                }"
                                class="form-select form-select-lg"
                            >
                                <option value="" selected>
                                    {{ __('Category select') }} ...
                                </option>
                                <option
                                    v-for="option in categoryOptions"
                                    :value="option.id"
                                >
                                    {{ option.patchName }}
                                </option>
                            </select>
                            <div
                                v-for="(
                                    filter, index
                                ) in productFilterOptions.filters"
                                :key="filter.id"
                                class="product filter pt-1 pb-1 d-inline-block me-1"
                            >
                                <div
                                    class="card"
                                    style="min-width: 200px; min-height: 250px"
                                >
                                    <div class="card-body">
                                        <FilterDisplay
                                            :filter="filter"
                                            :filterOptionsStart="
                                                productFilterOptions.filterOptions
                                            "
                                        />
                                    </div>
                                </div>
                            </div>
                            <div class="invalid-feedback">
                                {{
                                    failedValidation.categoryId
                                        ? failedValidation.categoryId[0]
                                        : ''
                                }}
                            </div>
                        </div>
                        <DragDropFileUploader
                            :editProduct="editProduct"
                            :failedValidation="failedValidation"
                            :filesArr="filesArr"
                        />
                        <div class="border border-2 padding-form-control">
                            <label class="form-label"
                                ><b>{{ __('Description') }}</b></label
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
                            {{ submitBtnText }}
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
                    <loading-overlay v-if="addingProduct" />
                </form>
            </div>
        </div>
    </div>
</template>

<script>
import DragDropFileUploader from './DragDropFileUploader.vue';
import RawTool from '@editorjs/raw';
import editorjsColumns from '@calumk/editorjs-columns';
import editorjsParagraphLinebreakable from '@calumk/editorjs-paragraph-linebreakable';
import Table from '@editorjs/table';
import EditorJS from '@editorjs/editorjs';
import Header from '@editorjs/header';
import ImageTool from '@editorjs/image';
import List from '@editorjs/list';
import AttachesTool from '@editorjs/attaches';
import Embed from '@editorjs/embed';
import Gallery from '@vtchinh/gallery-editorjs';
import SimpleImage from '../editorjs/simple-image-tutorial/simple-image.js';
import FilterDisplay from './FilterDisplay.vue';
import getProductFilterOptions from './getProductFilterOptions.js';
import generateSlug from './generateSlug.js';

export default {
    mixins: [getProductFilterOptions, generateSlug],
    components: { DragDropFileUploader, FilterDisplay },
    props: [
        'editProduct',
        'adminPanelAddProductUrl',
        'adminPanelGetProductFilterOptionsUrl',
        'adminPanelGetProductDescUrl',
        'adminPanelFetchUrlUrl',
        'adminPanelUploadFileUrl',
        'adminPanelUploadAttachmentUrl',
        'selectedCategory',
        'getProducts',
        'categoryOptions',
    ],
    data() {
        return {
            selectedCategoryId: null,
            title: '',
            slug: '',
            filesArr: [],
            editor: null,
            addingProduct: false,
            globalError: '',
            globalSuccess: '',
            failedValidation: {},
        };
    },
    watch: {
        editProduct(newVal) {
            this.createfilesArr();
            this.setEditForm();
            this.failedValidation = {};
            this.globalSuccess = '';
            this.globalError = '';
            this.getProductFilterOptions();
            this.getProductDesc();
        },
        categoryId(newVal) {
            this.selectedCategoryId = newVal;
        },
    },
    computed: {
        submitBtnText() {
            if (this.editProduct) {
                return (
                    __('Edit product') +
                    ` (${this.editProduct.product.category.name})`
                );
            }
            return 'Add product';
        },
        categoryId() {
            if (this.editProduct) {
                return this.editProduct.product.category.id;
            } else if (this.selectedCategory) {
                return this.selectedCategory.id;
            }
            return null;
        },
    },
    methods: {
        setSlug: function (slug) {
            this.slug = slug;
        },
        setEditForm: function () {
            if (this.editProduct) {
                this.title = this.editProduct.product.title;
                this.slug = this.editProduct.product.slug;
                this.slugCustomized =
                    this.generateSlug(this.title) !== this.slug;
                this.$refs.price.value = this.editProduct.product.price;
                this.$refs.quantity.value = this.editProduct.product.quantity;
            } else {
                this.title = '';
                this.slug = '';
                this.slugCustomized = false;
                this.$refs.price.value = '';
                this.$refs.quantity.value = '';
            }
        },
        createfilesArr: function () {
            var photos = [];
            if (this.editProduct) {
                _.forEach(
                    this.editProduct.product.product_photos,
                    function (photo) {
                        photos.push({
                            src: photo.fullUrlSmall,
                            id: photo.id,
                            removed: false,
                        });
                    }
                );
            }
            this.filesArr = photos;
        },
        getProductDesc: function () {
            this.editor.blocks.clear();
            if (!this.editProduct) {
                return;
            }
            var that = this;
            axios
                .post(this.adminPanelGetProductDescUrl, {
                    productId: this.editProduct.product.id,
                })
                .then(function (response) {
                    if (
                        !that.editProduct ||
                        response.data.productId !== that.editProduct.product.id
                    ) {
                        return;
                    }
                    that.editor.blocks.render(JSON.parse(response.data.desc));
                });
        },
        addProduct: function (e) {
            var that = this;
            this.addingProduct = true;
            e.preventDefault();
            let formData = new FormData(this.$refs.addProduct);
            formData.append('filesArr', JSON.stringify(this.filesArr));
            if (this.editProduct) {
                formData.append('productId', this.editProduct.product.id);
            }
            this.editor
                .save()
                .then((description) => {
                    if (description.blocks.length) {
                        formData.append(
                            'description',
                            JSON.stringify(description)
                        );
                    }
                    axios
                        .post(this.adminPanelAddProductUrl, formData)
                        .then(function (response) {
                            that.globalSuccess = `"${
                                that.$refs.title.value
                            }" ${__('saved!')}`;
                            that.getProducts();
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
                            this.addingProduct = false;
                        });
                })
                .catch((error) => {
                    console.log('Saving failed: ', error);
                });
        },
    },
    updated() {
        console.debug('updated');
    },
    created() {},
    mounted() {
        let tools = {
            raw: RawTool,
            paragraph: editorjsParagraphLinebreakable,
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
