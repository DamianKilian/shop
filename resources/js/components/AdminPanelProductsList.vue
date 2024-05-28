<template>
    <ul class="app-list-ul products-list">
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
</template>

<script>
import { goToSubCategory } from './commonFunctions.js'

export default {
    props: ['currentCategories', 'breadcrumb', 'selectedCategory', 'products', 'adminPanelGetProductsUrl'],
    data() {
        return {}
    },
    methods: {
        viewProduct: function (e, product) {
        },
        goToSubCategory,
        getCategoryProducts: function (e, category = null) {
            if (!e.target.classList.contains('show-products')) {
                return;
            }
            if (this.selectedCategory) {
                if (category.id === this.selectedCategory.id) {
                    category.selected = false;
                } else {
                    _.forEach(this.currentCategories, function (val, key) {
                        if (val.selected) {
                            val.selected = false;
                            return;
                        }
                    });
                    category.selected = true;
                }
            } else {
                category.selected = true;
            }
            this.getProducts(category);
        },
        getProducts: function (category = null) {
            var that = this;
            axios
                .post(this.adminPanelGetProductsUrl, { category: category })
                .then(function (response) {
                    that.arrangeProducts(response.data.products);
                })
                .catch(function (error) {
                    that.$emit('getProductsGlobalError', error.message)
                });
        },
        arrangeProducts: function (products) {
            var that = this;
            _.forEach(products, function (product, key) {
                that.products.push({
                    product: product,
                    selected: false,
                });
            });
        },
    },
    created() {
        this.getProducts();
    },
    mounted() {
    },
}
</script>
