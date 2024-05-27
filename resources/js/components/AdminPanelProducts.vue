<template>
    <div id="admin-panel-products">
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
                <button v-if='Object.keys(products).length !== 0' type="button" class="btn btn-success float-end"
                    @click="addCategory">
                    <i class="fa-solid fa-plus"></i>
                </button>
                <button v-if='Object.keys(products).length === 0' type="button" class="btn btn-secondary float-end"
                    @click="goToCategory($event, breadcrumb.length - 2)">
                    <i class="fa-solid fa-arrow-left"></i>
                </button>
            </div>
            <AdminPanelProductsList :breadcrumb="breadcrumb" :currentCategories="currentCategories"
                :selectedCategory='selectedCategory' :adminPanelGetProductsUrl='adminPanelGetProductsUrl' />
        </div>
        <div class="mt-3 actions-global clearfix">
            <b>{{ selectedCategory ? selectedCategory.name : '' }}</b>
            <button data-bs-toggle="modal" data-bs-target="#addProduct" :disabled="!selectedCategory"
                class="btn btn-success float-end" @click="addCategory">
                <i class="fa-solid fa-plus"></i> {{ selectedCategory ? __('Add product') : __('Select category') }}
            </button>
            <button class="btn float-end me-1 btn-outline-dark" @click="showCategories = !showCategories">
                {{ __('Select category') }}
            </button>
            <button class="btn btn-danger float-end me-1" @click="goToCategory($event, breadcrumb.length - 2)">
                <i class="fa-solid fa-trash"></i> {{ __('Remove') }}
            </button>
        </div>
    </div>
    <AdminPanelAddProduct :adminPanelAddProductUrl='adminPanelAddProductUrl' :selectedCategory='selectedCategory' />
</template>

<script>
import AdminPanelProductsList from './AdminPanelProductsList.vue'
import AdminPanelAddProduct from './AdminPanelAddProduct.vue'
import { goToCategory, arrangeCategories, setBreadcrumb } from './commonFunctions.js'

export default {
    components: { AdminPanelProductsList, AdminPanelAddProduct },
    props: ['categoriesProp', 'adminPanelGetProductsUrl', 'adminPanelAddProductUrl'],
    data() {
        return {
            products: [],
            mainMenuId: 'main-menu',
            breadcrumb: [],
            categories: {},
            showCategories: false,
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
        getSelectedCategory: function () {
            var selectedCategory = null;
            _.forEach(this.currentCategories, function (category, key) {
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
