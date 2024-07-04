<template>
    <div id="admin-panel-products" class='clearfix'>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb justify-content-center">
                <li v-for="(b, index) in breadcrumb" :key="b.id" class="breadcrumb-item">
                    <button @click="goToCategory($event, index)" class="btn btn-sm btn-link"
                        :class="{ active: index == breadcrumb.length - 1 }">
                        {{ b.name }}
                    </button>
                </li>
            </ol>
        </nav>
        <div class="app-list clearfix" v-show='showCategories'>
            <div class="actions-global clearfix">
                <button type="button" class="btn btn-secondary float-end"
                    @click="goToCategory($event, breadcrumb.length - 2)">
                    <i class="fa-solid fa-arrow-left"></i>
                </button>
            </div>
            <AdminPanelProductsList :getProducts='getProducts' :products="products" :breadcrumb="breadcrumb"
                :currentCategories="currentCategories" :selectedCategory='selectedCategory'
                :adminPanelGetProductsUrl='adminPanelGetProductsUrl' :categories='categories' />
        </div>
        <div class="mt-3 actions-global clearfix">
            <button @click='editProduct = null' data-bs-toggle="modal" data-bs-target="#addProduct"
                :disabled="!selectedCategory" class="btn btn-success float-end mt-1 mt-sm-0">
                <i class="fa-solid fa-plus"></i> {{ selectedCategory ? __('Add product') : __('Select category') }}
            </button>
            <button class="btn float-end me-1 mt-1 mt-sm-0 btn-outline-dark" @click="showCategories = !showCategories">
                {{ __('Select category') }}
            </button>
            <button class="btn btn-danger float-end me-1 mt-1 mt-sm-0" @click="deleteProducts()">
                <i class="fa-solid fa-trash"></i> {{ __('Remove') }}
            </button>
        </div>
        <div v-if="globalError" class="text-bg-danger float-start mt-1">{{ globalError }}</div>
    </div>
    <AdminPanelAddProduct :editProduct='editProduct' :getProducts='getProducts'
        :adminPanelAddProductUrl='adminPanelAddProductUrl' :selectedCategory='selectedCategory'
        :categoryOptions='categoryOptions' />
    <search @search="(searchValue) => { getProducts(adminPanelGetProductsUrl, searchValue) }"></search>
    <search-filters @remove-search-value-submitted="searchValueSubmitted = ''; getProducts()"
        @remove-selected-category="selectedCategory.selected = false; getProducts()"
        :selectedCategory='selectedCategory' :searchValueSubmitted='searchValueSubmitted'></search-filters>
    <div v-if='products.length' id='products-container' class='clearfix pt-3'>
        <div v-for="(product, index) in products" :key="product.id"
            :class='{ "bg-primary bg-opacity-75": product.selected }' class="product pt-1 pb-1">
            <div class="card">
                <div @click='product.selected = !product.selected' class="card-img border-bottom">
                    <input class="m-1 form-check-input position-absolute" type="checkbox" v-model="product.selected">
                    <img v-if='product.product.product_photos[0]' :src="product.product.product_photos[0].fullUrlSmall"
                        class="card-img-top">
                    <svg v-else xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 400" width="400" height="400">
                        <rect width="400" height="400" fill="#cccccc"></rect>
                        <text x="50%" y="50%" dominant-baseline="middle" text-anchor="middle" font-family="monospace"
                            font-size="26px" fill="#333333">{{ __('No image') }}</text>
                    </svg>
                    <button @click.stop='editProduct = product' data-bs-toggle="modal" data-bs-target="#addProduct"
                        class="btn btn-warning btn-sm edit-product"><i class="fa-solid fa-pen-to-square"></i> <span>{{
                            __('Edit') }}</span></button>
                </div>
                <div class="card-body">
                    <h5 class="card-title">{{ product.product.title }}</h5>
                    <p class="card-text"
                        v-html="'<b>' + __('Price') + ': </b>' + product.product.price + '<br><b>' + __('Quantity') + ': </b>' + product.product.quantity">
                    </p>
                </div>
            </div>
        </div>
    </div>
    <div v-else class="alert alert-light mt-3" role="alert">{{ __('No products') }}</div>
    <AdminPanelProductsPagination :pagination='pagination' :getProducts='getProducts' />
</template>

<script>
import AdminPanelProductsList from './AdminPanelProductsList.vue'
import AdminPanelAddProduct from './AdminPanelAddProduct.vue'
import AdminPanelProductsPagination from './AdminPanelProductsPagination.vue'
import { goToCategory, arrangeCategories, setBreadcrumb } from './commonFunctions.js'

export default {
    components: { AdminPanelProductsList, AdminPanelAddProduct, AdminPanelProductsPagination },
    props: ['categoriesProp', 'adminPanelGetProductsUrl', 'adminPanelAddProductUrl', 'adminPanelDeleteProductsUrl', 'categoryOptionsProp'],
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
        }
    },
    computed: {
        currentCategories() {
            return this.categories[_.last(this.breadcrumb).id];
        },
        selectedCategory() {
            return this.getSelectedCategory();
        },
    },
    methods: {
        selectProduct: function (e, product) {
            product.selected = !product.selected;
        },
        setGlobalError: function (errorMessage) {
            this.globalError = errorMessage;
        },
        deleteProducts: function () {
            var that = this;
            axios
                .post(this.adminPanelDeleteProductsUrl, { products: this.getSelectedProducts() })
                .then(function (response) {
                    that.getProducts();
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
