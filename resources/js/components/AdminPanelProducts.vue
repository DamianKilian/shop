<template>
    <div id="admin-panel-products" class='clearfix'>
        <div v-show='showCategories'>
            <nav>
                <ol class="breadcrumb justify-content-center">
                    <li v-for="(b, index) in breadcrumb" :key="b.id" class="breadcrumb-item">
                        <button
                            @click="
                                if (this.selectedCategory) {
                                    this.selectedCategory.selected = false;
                                    getProducts();
                                }
                                goToCategory($event, index);
                            "
                            class="btn btn-sm btn-link"
                            :class="{ active: index == breadcrumb.length - 1 }"
                        >
                            {{ b.name }}
                        </button>
                    </li>
                </ol>
            </nav>
            <div class="app-list clearfix">
                <div class="actions-global clearfix">
                    <button
                        type="button"
                        class="btn btn-secondary float-end"
                        @click="
                            if (this.selectedCategory) {
                                this.selectedCategory.selected = false;
                                getProducts();
                            }
                            goToCategory($event, breadcrumb.length - 2);
                        "
                    >
                        <i class="fa-solid fa-arrow-left"></i>
                    </button>
                </div>
                <AdminPanelProductsList :getProducts='getProducts' :products="products" :breadcrumb="breadcrumb"
                    :currentCategories="currentCategories" :selectedCategory='selectedCategory'
                    :adminPanelGetProductFilterOptionsUrl='adminPanelGetProductFilterOptionsUrl'
                    :adminPanelAddOptionsToSelectedProductsUrl='adminPanelAddOptionsToSelectedProductsUrl'
                    :selectedProducts='selectedProducts'
                    :adminPanelGetProductsUrl='adminPanelGetProductsUrl' :categories='categories' />
            </div>
        </div>
        <div class="mt-3 actions-global clearfix">
            <button @click='editProduct = null' data-bs-toggle="modal" data-bs-target="#addProduct"
                class="btn btn-success float-end mt-1 mt-sm-0">
                <i class="fa-solid fa-plus"></i> {{ __('Add product') }}
            </button>
            <button class="btn float-end me-1 mt-1 mt-sm-0 btn-outline-dark" @click="showCategories = !showCategories">
                <i class="fa-solid fa-plus"></i> {{ __('Add filter options to products') }}
            </button>
            <button class="btn btn-danger float-end me-1 mt-1 mt-sm-0"
                data-bs-toggle="modal"
                data-bs-target="#deleteModal"
                @click="
                    setSelectedProductData();
                    deleteModal.title = `${__(
                        'Do you want to delete'
                    )}: &quot;${selectedProductData.titles}&quot;`;
                    deleteModal.productId = selectedProductData.ids;
                "
             >
                <i class="fa-solid fa-trash"></i> {{ __('Remove') }}
            </button>
        </div>
        <div v-if="globalError" class="text-bg-danger float-start mt-1">{{ globalError }}</div>
    </div>
    <AdminPanelAddProduct :editProduct='editProduct' :getProducts='getProducts'
        :adminPanelFetchUrlUrl="adminPanelFetchUrlUrl"
        :adminPanelUploadFileUrl="adminPanelUploadFileUrl"
        :adminPanelUploadAttachmentUrl="adminPanelUploadAttachmentUrl"
        :adminPanelAddProductUrl='adminPanelAddProductUrl'
        :adminPanelGetProductFilterOptionsUrl='adminPanelGetProductFilterOptionsUrl'
        :adminPanelGetProductDescUrl='adminPanelGetProductDescUrl'
        :selectedCategory='selectedCategory' :categoryOptions='categoryOptions' />
    <div class="mt-3">
        <search @search="(searchValue) => { getProducts(adminPanelGetProductsUrl, searchValue) }"></search>
    </div>
    <search-filters @remove-search-value-submitted="searchValueSubmitted = ''; getProducts()"
        @remove-selected-category="selectedCategory.selected = false; getProducts()"
        :selectedCategory='selectedCategory' :searchValueSubmitted='searchValueSubmitted'></search-filters>
    <div v-if='products.length' id='products-container' class='clearfix pt-3'>
        <div v-for="(product, index) in products" :key="product.id"
            :class='{ "bg-primary bg-opacity-75": product.selected }' class="product pt-1 pb-1">
            <div class="card">
            <div class="btn-group product-actions">
                <button type="button" class="btn btn-secondary dropdown-toggle" data-bs-auto-close="outside" data-bs-toggle="dropdown">
                    <i class="fa-solid fa-ellipsis"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <a
                            :href="'/product/' + (product.product.slug || '')"
                            class="btn btn-light"
                            target="_blank"
                        >
                            {{ __('Show') }}
                            <i class="fa-solid fa-arrow-right"></i>
                        </a>
                    </li>
                    <li>
                        <button @click.stop='editProduct = product' data-bs-toggle="modal" data-bs-target="#addProduct"
                            class="btn btn-primary"><i class="fa-solid fa-pen-to-square"></i> <span>{{ __('Edit') }}</span>
                        </button>
                    </li>
                    <li>
                        <button
                            class="btn btn-success"
                            :disabled="product.applyChangesClicked"
                            @click="applyChanges(product)"
                        >
                            <template v-if="product.applyChangesClicked">
                                <span
                                    class="spinner-border spinner-border-sm"
                                    role="status"
                                    aria-hidden="true"
                                ></span>
                                Loading...
                            </template>
                            <template v-else>
                                {{ __('Apply changes') }}
                            </template>
                        </button>
                    </li>
                    <li>
                        <button
                            class="btn"
                            :class="{
                                'btn-outline-warning': product.product.active,
                                'btn-warning': !product.product.active,
                            }"
                            :disabled="product.toggleActiveClicked"
                            @click="toggleActive(product)"
                        >
                            <template v-if="product.toggleActiveClicked">
                                <span
                                    class="spinner-border spinner-border-sm"
                                    role="status"
                                    aria-hidden="true"
                                ></span>
                                Loading...
                            </template>
                            <template v-else>
                                {{
                                    product.product.active
                                        ? __('Deactivate')
                                        : __('Activate')
                                }}
                            </template>
                        </button>
                    </li>
                    <li>
                        <button
                            class="btn btn-danger"
                            data-bs-toggle="modal"
                            data-bs-target="#deleteModal"
                            @click="
                                deleteModal.title = `${__(
                                    'Do you want to delete'
                                )}: &quot;${product.product.title}&quot;`;
                                deleteModal.productId = product.product.id;
                            "
                        >
                            {{ __('Delete') }}
                        </button>
                    </li>
                </ul>
            </div>
                <div @click='product.selected = !product.selected' class="card-img border-bottom">
                    <input class="m-1 form-check-input position-absolute" type="checkbox" v-model="product.selected">
                    <img v-if='product.product.product_images[0]' :src="product.product.product_images[0].fullUrlSmall"
                        class="card-img-top">
                    <svg v-else xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 400" width="400" height="400">
                        <rect width="400" height="400" fill="#cccccc"></rect>
                        <text x="50%" y="50%" dominant-baseline="middle" text-anchor="middle" font-family="monospace"
                            font-size="26px" fill="#333333">{{ __('No image') }}</text>
                    </svg>
                </div>
                <div class="card-body">
                    <h5 class="card-title fw-bolder">{{ product.product.title }}</h5>
                    <p v-html="product.product.descStr"></p>
                    <p class="card-text"
                        v-html="'<b>' + __('Price') + ': </b>' + product.product.price + '<br><b>' + __('Quantity') + ': </b>' + product.product.quantity">
                    </p>
                </div>
            </div>
        </div>
    </div>
    <div v-else class="alert alert-light mt-3" role="alert">{{ __('No products') }}</div>
    <AdminPanelProductsPagination :pagination='pagination' :getItems='getProducts' v-if='products.length' />
    <DeleteModal
        ref="deleteModal"
        :deleteModal="deleteModal"
        @delete="deleteProducts(deleteModal.productId)"
    />
</template>

<script>
import DeleteModal from './DeleteModal.vue';
import AdminPanelProductsList from './AdminPanelProductsList.vue'
import AdminPanelAddProduct from './AdminPanelAddProduct.vue'
import AdminPanelProductsPagination from './AdminPanelProductsPagination.vue'
import Search from './Search.vue';
import SearchFilters from './SearchFilters.vue';
import { goToCategory, arrangeCategories, setBreadcrumb } from './commonFunctions.js'
import _ from 'lodash';

export default {
    components: { AdminPanelProductsList, AdminPanelAddProduct, AdminPanelProductsPagination, Search, SearchFilters, DeleteModal },
    props: ['categoriesProp',
        'adminPanelFetchUrlUrl',
        'adminPanelUploadFileUrl',
        'adminPanelUploadAttachmentUrl',
        'adminPanelGetProductsUrl',
        'adminPanelAddProductUrl',
        'adminPanelGetProductFilterOptionsUrl',
        'adminPanelAddOptionsToSelectedProductsUrl',
        'adminPanelGetProductDescUrl',
        'adminPanelDeleteProductsUrl',
        'categoryOptionsProp',
        'adminPanelToggleActiveProductUrl',
        'adminPanelApplyChangesProductUrl',
    ],
    data() {
        return {
            categoryOptions: JSON.parse(this.categoryOptionsProp),
            searchValueSubmitted: '',
            pagination: null,
            editProduct: null,
            products: [],
            mainMenuId: 'main-menu',
            breadcrumb: [],
            categories: {},
            showCategories: false,
            globalError: '',
            deleteModal: {
                title: '',
                productId: null,
            },
            selectedProductData: {
                titles: [],
                ids: [],
            },
        }
    },
    computed: {
        currentCategories() {
            return this.categories[_.last(this.breadcrumb).id];
        },
        selectedCategory() {
            return this.getSelectedCategory();
        },
        selectedProducts() {
            return this.getSelectedProducts();
        },
    },
    methods: {
        toggleActive: function (product) {
            var active = !product.product.active;
            product.toggleActiveClicked = true;
            axios
                .post(this.adminPanelToggleActiveProductUrl, {
                    productId: product.product.id,
                    active: active,
                })
                .then(function (response) {
                    product.product.active = active;
                    product.toggleActiveClicked = false;
                });
        },
        applyChanges: function (product) {
            product.applyChangesClicked = true;
            axios
                .post(this.adminPanelApplyChangesProductUrl, { productId: product.product.id })
                .then(function (response) {
                    product.applyChangesClicked = false;
                });
        },
        selectProduct: function (e, product) {
            product.selected = !product.selected;
        },
        setGlobalError: function (errorMessage) {
            this.globalError = errorMessage;
        },
        deleteProducts: function (productIds) {
            var that = this;
            axios
                .post(this.adminPanelDeleteProductsUrl, { productIds: productIds })
                .then(function (response) {
                    that.getProducts();
                    that.$refs.deleteModal.$refs.deleteCloseModal.click();
                })
                .catch(function (error) {
                    that.globalError = error.message;
                });
        },
        getSelectedProducts: function () {
            var selectedProducts = [];
            _.forEach(this.products, function (product, key) {
                if (product.selected) {
                    selectedProducts.push(product.product);
                    return;
                }
            });
            return selectedProducts;
        },
        setSelectedProductData: function () {
            var selectedProductIds = [];
            var selectedProductTitles = [];
            _.forEach(this.products, function (product, key) {
                if (product.selected) {
                    selectedProductIds.push(product.product.id);
                    selectedProductTitles.push(product.product.title);
                    return;
                }
            });
            this.selectedProductData.ids = selectedProductIds;
            this.selectedProductData.titles = selectedProductTitles;
        },
        getProducts: function (url = this.adminPanelGetProductsUrl, searchValue = this.searchValueSubmitted) {
            this.products = [];
            var that = this;
            axios
                .post(url, { category: this.selectedCategory, searchValue: searchValue })
                .then(function (response) {
                    that.pagination = response.data.products;
                    that.arrangeProducts(that.pagination.data);
                    that.searchValueSubmitted = searchValue;
                })
                .catch(function (error) {
                    that.globalError = error.message;
                    console.log(error.message);
                });
        },
        arrangeProducts: function (products) {
            var that = this;
            _.forEach(products, function (product) {
                that.products.push({
                    product: product,
                    selected: false,
                    applyChangesClicked: false,
                    toggleActiveClicked: false,
                });
            });
        },
        getSelectedCategory: function () {
            var selectedCategory = null;
            _.forEach(this.currentCategories, function (category) {
                if (category.selected) {
                    selectedCategory = category;
                    return;
                }
            });
            return selectedCategory;
        },
        goToCategory,
        arrangeCategories,
        setBreadcrumb,
    },
    created() {
        this.setBreadcrumb();
        this.arrangeCategories();
    },
    mounted() {
    }
}
</script>
