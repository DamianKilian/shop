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
                :adminPanelGetProductsUrl='adminPanelGetProductsUrl' />
        </div>
        <div class="mt-3 actions-global clearfix">
            <button data-bs-toggle="modal" data-bs-target="#addProduct" :disabled="!selectedCategory"
                class="btn btn-success float-end mt-1 mt-sm-0" @click="addCategory">
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
    <AdminPanelAddProduct :getProducts='getProducts' :adminPanelAddProductUrl='adminPanelAddProductUrl'
        :selectedCategory='selectedCategory' />
    <div class='text-center'><b>{{ selectedCategory ? selectedCategory.name : '' }}</b></div>
    <div id='products-container' class='clearfix'>
        <div v-for="(product, index) in products" :key="product.id"
            :class='{ "bg-primary bg-opacity-75": product.selected }' class="product">
            <div class="card">
                <div @click='product.selected = !product.selected' class="card-img border-bottom">
                    <input class="m-1 form-check-input position-absolute" type="checkbox" v-model="product.selected">
                    <img :src="product.product.product_photos[0] ? product.product.product_photos[0].fullUrlSmall : 'https://placehold.co/400'"
                        class="card-img-top">
                    <button @click.stop class="btn btn-warning btn-sm edit-product"><i
                            class="fa-solid fa-pen-to-square"></i> <span>{{ __('Edit') }}</span></button>
                </div>
                <div class="card-body">
                    <h5 class="card-title">{{ product.product.title }}</h5>
                    <p class="card-text"
                        v-html="'<b>' + __('Price') + ': </b>' + product.product.price + ' &nbsp;' + '<b>' + __('Quantity') + ': </b>' + product.product.quantity">
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import AdminPanelProductsList from './AdminPanelProductsList.vue'
import AdminPanelAddProduct from './AdminPanelAddProduct.vue'
import { goToCategory, arrangeCategories, setBreadcrumb } from './commonFunctions.js'

export default {
    components: { AdminPanelProductsList, AdminPanelAddProduct },
    props: ['categoriesProp', 'adminPanelGetProductsUrl', 'adminPanelAddProductUrl', 'adminPanelDeleteProductsUrl'],
    data() {
        return {
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
            console.debug(e.target);//mmmyyy
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
        getProducts: function (category = null) {
            this.products = [];
            var that = this;
            axios
                .post(this.adminPanelGetProductsUrl, { category: category })
                .then(function (response) {
                    that.arrangeProducts(response.data.products);
                })
                .catch(function (error) {
                    that.globalError = error.message;
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
