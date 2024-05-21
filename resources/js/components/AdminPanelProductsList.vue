<template>
    <ul v-if='Object.keys(products).length === 0' class="app-list-ul products-list">
        <li v-for="(category, index) in currentCategories" :key="category.id"
            @click="getCategoryProducts($event, category)"
            :class="{ 'text-bg-success': selectedCategory && selectedCategory.id === category.id }"
            class="d-flex show-products">
            {{ category.name }}
            <div class="btn-group btn-group-sm float-end" role="group" aria-label="Small button group">
                <button class="btn btn-secondary" @click="goToSubCategory($event, category)"><i
                        class="fa-solid fa-arrow-right"></i></button>
            </div>
        </li>
    </ul>
    <ul v-if='Object.keys(products).length !== 0' class="app-list-ul products">
        <li v-for="(product, index) in products.products" :key="product.id" @click="viewProduct($event, product)"
            class="d-flex show-products">
            {{ product.name }}
            <div class="btn-group btn-group-sm float-end" role="group" aria-label="Small button group">
                <button class="btn btn-secondary" @click="goToSubCategory($event, category)"><i
                        class="fa-solid fa-arrow-right"></i></button>
            </div>
        </li>
    </ul>
</template>

<script>
import { goToSubCategory } from './commonFunctions.js'

export default {
    props: ['currentCategories', 'breadcrumb', 'categories', 'products', 'adminPanelGetProductsUrl',
        'getCategoryProducts', 'arrangeProducts', 'getProducts', 'selectedCategory'],
    data() {
        return {
        }
    },
    methods: {
        viewProduct: function (e, product) {
        },
        goToSubCategory,
    },
    created() {
    },
    mounted() {
    },
}
</script>
