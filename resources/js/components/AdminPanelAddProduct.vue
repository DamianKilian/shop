<template>
    <div class="modal fade" id="addProduct" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="modalLabel">{{ submitBtnText }}</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form @submit='addProduct' ref='addProduct' method="post" enctype="multipart/form-data"
                    class='position-relative'>
                    <input :value='selectedCategoryId' name='categoryId' type='hidden' />
                    <div class="modal-body">
                        <div class="clearfix">
                            <div class="form-floating mb-3 float-start" style="width: 49%;">
                                <input v-model='title.val' @input='editedTitleVal()' ref='title' name='title'
                                    :class='{ "is-invalid": failedValidation.title }' class="form-control" id="title"
                                    :placeholder="__('Title')">
                                <label for="title">{{ __('Title') }}</label>
                                <div class="invalid-feedback">
                                    {{ failedValidation.title ? failedValidation.title[0] : '' }}
                                </div>
                            </div>
                            <div class="input-group mb-3 float-start" style="width: 49%; margin-left: 2%;">
                                <div class="form-floating">
                                    <input v-model='title.slug' @input='title.slugCustomized = true' ref='slug'
                                        name='slug' :class='{ "is-invalid": failedValidation.title }'
                                        class="form-control" id="slug" :placeholder="__('Slug')">
                                    <label for="slug">{{ __('Slug') }}</label>
                                    <div class="invalid-feedback">
                                        {{ failedValidation.title ? failedValidation.title[0] : '' }}
                                    </div>
                                </div>
                                <button @click='title.slugCustomized = false; editedTitleVal()'
                                    class="btn btn-outline-secondary" type="button" id="button-addon2">{{ __('Reset') }}
                                </button>
                            </div>
                        </div>
                        <div class="clearfix">
                            <div class="form-floating mb-3 float-start" style="width: 49%;">
                                <input ref='price' name='price' :class='{ "is-invalid": failedValidation.price }'
                                    class="form-control" id="price" :placeholder="__('Price')">
                                <label for="price">{{ __('Price') }}</label>
                                <div class="invalid-feedback">
                                    {{ failedValidation.price ? failedValidation.price[0] : '' }}
                                </div>
                            </div>
                            <div class="form-floating mb-3 float-end" style="width: 49%; margin-left: 2%;">
                                <input ref='quantity' name='quantity'
                                    :class='{ "is-invalid": failedValidation.quantity }' class="form-control"
                                    id="quantity" :placeholder="__('Quantity')">
                                <label for="quantity">{{ __('Quantity') }}</label>
                                <div class="invalid-feedback">
                                    {{ failedValidation.quantity ? failedValidation.quantity[0] : '' }}
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="category-select" class="form-label"><b>{{ __('Category select') }}</b></label>
                            <select v-model="selectedCategoryId" id='category-select'
                                :class='{ "is-invalid": failedValidation.categoryId }'
                                class="form-select form-select-lg">
                                <option :value="null" selected>{{ __('Category select') }} ...</option>
                                <option v-for="option in categoryOptions" :value="option.id">
                                    {{ option.patchName }}
                                </option>
                            </select>
                            <div class="invalid-feedback">
                                {{ failedValidation.categoryId ? failedValidation.categoryId[0] : '' }}
                            </div>
                        </div>
                        <DragDropFileUploader :editProduct='editProduct' :failedValidation='failedValidation'
                            :filesArr='filesArr' />
                        <div class="border border-2 padding-form-control">
                            <label class="form-label"><b>{{ __('Description') }}</b></label>
                            <div id="editorjs" :class='{ "is-invalid border-danger": failedValidation.description }'
                                class='border color bg-white padding-form-control' style="max-width: 725px;"></div>
                            <div class="invalid-feedback">{{ failedValidation.description ?
                                failedValidation.description[0] : '' }}</div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button ref='closeModal' type='button' class="btn btn-secondary" data-bs-dismiss="modal">{{
                            __('Close') }}</button>
                        <button type="submit" class="btn btn-success"><i class="fa-solid fa-plus"></i>
                            {{ submitBtnText }}
                        </button>
                        <div v-if="globalError" class="text-bg-danger float-end mt-1">{{ globalError }}</div>
                        <div v-if="globalSuccess" class="text-bg-success float-end mt-1">{{ globalSuccess }}</div>
                    </div>
                    <LoadingOverlay v-if='addingProduct' />
                </form>
            </div>
        </div>
    </div>
</template>

<script>
import DragDropFileUploader from './DragDropFileUploader.vue'
import LoadingOverlay from './LoadingOverlay.vue'
import EditorJS from '@editorjs/editorjs';
import Header from '@editorjs/header';
// import SimpleImage from "@editorjs/simple-image";
import List from "@editorjs/list";

export default {
    components: { DragDropFileUploader, LoadingOverlay },
    props: ['editProduct', 'adminPanelAddProductUrl', 'selectedCategory', 'getProducts', 'categoryOptions'],
    data() {
        return {
            selectedCategoryId: null,
            title: {
                val: '',
                slug: '',
                slugCustomized: false
            },
            filesArr: [],
            editor: null,
            addingProduct: false,
            globalError: '',
            globalSuccess: '',
            failedValidation: {},
        }
    },
    watch: {
        editProduct(newVal) {
            this.createfilesArr();
            this.setEditForm();
            this.failedValidation = {};
            this.globalSuccess = '';
            this.globalError = '';
        },
        categoryId(newVal) {
            this.selectedCategoryId = newVal;
        }
    },
    computed: {
        submitBtnText() {
            if (this.editProduct) {
                return __('Edit product') + ` (${this.editProduct.product.category.name})`
            }
            return this.selectedCategory ? (__('Add product to') + ': ' + this.selectedCategory.name) : __('Select category');
        },
        categoryId() {
            if (this.editProduct) {
                return this.editProduct.product.category.id;
            } else if (this.selectedCategory) {
                return this.selectedCategory.id;
            }
        }
    },
    methods: {
        editedTitleVal: function (e) {
            if (!this.title.slugCustomized) {
                this.title.slug = this.generateSlug();
            }
        },
        generateSlug: function () {
            return this.title.val.trim().replace(/ /g, '-');
        },
        setEditForm: function () {
            if (this.editProduct) {
                this.editor.blocks.render(JSON.parse(this.editProduct.product.description));
                this.title = {
                    val: this.editProduct.product.title,
                    slug: this.editProduct.product.slug,
                };
                this.title.slugCustomized = this.generateSlug() !== this.title.slug;
                this.$refs.price.value = this.editProduct.product.price;
                this.$refs.quantity.value = this.editProduct.product.quantity;
            } else {
                this.editor.blocks.clear();
                this.title = {
                    val: '',
                    slug: '',
                    slugCustomized: false
                };
                this.$refs.price.value = '';
                this.$refs.quantity.value = '';
            }
        },
        createfilesArr: function () {
            var photos = [];
            if (this.editProduct) {
                _.forEach(this.editProduct.product.product_photos, function (photo) {
                    photos.push({
                        src: photo.fullUrlSmall,
                        id: photo.id,
                        removed: false,
                    });
                });
            }
            this.filesArr = photos;
        },
        addProduct: function (e) {
            var that = this;
            this.addingProduct = true;
            e.preventDefault();
            let formData = new FormData(this.$refs.addProduct);
            formData.append("filesArr", JSON.stringify(this.filesArr));
            if (this.editProduct) {
                formData.append("productId", this.editProduct.product.id);
            }
            this.editor.save().then((description) => {
                if (description.blocks.length) {
                    formData.append("description", JSON.stringify(description));
                }
                axios.post(this.adminPanelAddProductUrl, formData)
                    .then(function (response) {
                        that.globalSuccess = `"${that.$refs.title.value}" ${__('saved!')}`;
                        that.getProducts();
                        that.$refs.closeModal.click();
                    })
                    .catch(function (error) {
                        if (_.has(error, 'response.data.failedValidation')) {
                            that.failedValidation = error.response.data.failedValidation;
                        } else {
                            that.globalError = error.message;
                        }
                    }).then(() => {
                        this.addingProduct = false;
                    });
            }).catch((error) => {
                console.log('Saving failed: ', error)
            });
        }
    },
    updated() {
        console.debug('updated');//mmmyyy
    },
    created() { },
    mounted() {
        this.editor = new EditorJS({
            tools: {
                list: {
                    class: List,
                    inlineToolbar: true,
                    config: {
                        defaultStyle: 'unordered'
                    }
                },
                // image: SimpleImage,
                header: {
                    class: Header,
                    config: {
                        placeholder: __('Enter a header'),
                        levels: [2, 3, 4],
                        defaultLevel: 2
                    }
                }
            }
        });
    }
}
</script>
