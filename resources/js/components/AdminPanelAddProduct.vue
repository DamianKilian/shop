<template>
    <div class="modal fade" id="addProduct" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="modalLabel">{{ selectedCategory ? (__('Add product to') + ': ' +
                        selectedCategory.name) : __('Select category') }}</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form @submit='addProduct' ref='addProduct' method="post" enctype="multipart/form-data"
                    class='position-relative'>
                    <input v-if='selectedCategory' :value='selectedCategory.id' name='categoryId' type='hidden' />
                    <div class="modal-body">
                        <div class="form-floating mb-3">
                            <input name='title' :class='{ "is-invalid": failedValidation.title }' class="form-control"
                                id="title" :placeholder="__('Title')">
                            <label for="title">{{ __('Title') }}</label>
                            <div class="invalid-feedback">
                                {{ failedValidation.title ? failedValidation.title[0] : '' }}
                            </div>
                        </div>
                        <div class="clearfix">
                            <div class="form-floating mb-3 float-start" style="width: 49%;">
                                <input name='price' :class='{ "is-invalid": failedValidation.price }'
                                    class="form-control" id="price" :placeholder="__('Price')">
                                <label for="price">{{ __('Price') }}</label>
                                <div class="invalid-feedback">
                                    {{ failedValidation.price ? failedValidation.price[0] : '' }}
                                </div>
                            </div>
                            <div class="form-floating mb-3 float-end" style="width: 49%; margin-left: 2%;">
                                <input name='quantity' :class='{ "is-invalid": failedValidation.quantity }'
                                    class="form-control" id="quantity" :placeholder="__('Quantity')">
                                <label for="quantity">{{ __('Quantity') }}</label>
                                <div class="invalid-feedback">
                                    {{ failedValidation.quantity ? failedValidation.quantity[0] : '' }}
                                </div>
                            </div>
                        </div>
                        <DragDropFileUploader :failedValidation='failedValidation' :filesArr='filesArr' />
                        <div class="border border-2 padding-form-control">
                            <label class="form-label"><b>{{ __('Description') }}</b></label>
                            <div id="editorjs" :class='{ "is-invalid border-danger": failedValidation.description }'
                                class='border color bg-white padding-form-control' style="max-width: 725px;"></div>
                            <div class="invalid-feedback">{{ failedValidation.description ?
                                failedValidation.description[0] : '' }}</div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
                        <button type="submit" class="btn btn-success">
                            <i class="fa-solid fa-plus"></i> {{ selectedCategory ? (__('Add product to') + ': ' +
                                selectedCategory.name) : __('Select category') }}
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

export default {
    components: { DragDropFileUploader, LoadingOverlay },
    props: ['currFilesProp', 'adminPanelAddProductUrl', 'selectedCategory'],
    data() {
        return {
            filesArr: this.currFilesProp || [],
            editor: null,
            addingProduct: false,
            globalError: '',
            globalSuccess: '',
            failedValidation: {},
        }
    },
    methods: {
        addProduct: function (e) {
            var that = this;
            this.addingProduct = true;
            e.preventDefault();
            let formData = new FormData(this.$refs.addProduct);
            formData.append("filesArr", JSON.stringify(this.filesArr));
            this.editor.save().then((description) => {
                if (description.blocks.length) {
                    formData.append("description", JSON.stringify(description));
                }
                that.globalSuccess = '';
                that.globalError = '';
                axios.post(this.adminPanelAddProductUrl, formData)
                    .then(function (response) {
                        that.globalSuccess = __('Saved!');
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
        console.log('updated')
    },
    created() { },
    mounted() {
        this.editor = new EditorJS();
    }
}
</script>
