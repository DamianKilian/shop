<template>
    <ul class="app-list-ul products-list">
        <li
            v-for="(category, index) in currentCategories"
            :key="category.id"
            @click="getCategoryProducts($event, category)"
            :class="{
                'text-bg-success':
                    selectedCategory && selectedCategory.id === category.id,
            }"
            class="d-flex show-products"
        >
            <div
                style="pointer-events: none"
                class="cat-name flex-fill align-self-center"
            >
                <span class="text-muted">{{ __('name') }}: </span
                ><b>{{ category.name }}</b>
            </div>
            <div
                class="btn-group btn-group-sm float-end"
                role="group"
                aria-label="Small button group"
            >
                <button
                    class="btn btn-secondary"
                    @click="
                        if (this.selectedCategory) {
                            this.selectedCategory.selected = false;
                            getProducts();
                        }
                        goToSubCategory($event, category);
                    "
                >
                    <i class="fa-solid fa-arrow-right"></i>
                </button>
            </div>
        </li>
    </ul>
    <div class="text-center">
        <div
            v-if="
                productFilterOptions.filters.length &&
                Object.keys(checkedOptionsAllFilters).length
            "
        >
            <button
                type="button"
                class="btn btn-success"
                :disabled="!selectedProducts.length"
                @click="addOptionsToSelectedProducts()"
            >
                <i class="fa-solid fa-plus"></i>
                {{ __('Add options to selected products') }}
            </button>
            <button
                type="button"
                class="btn btn-danger ms-1"
                :disabled="!selectedProducts.length"
                @click="addOptionsToSelectedProducts(true)"
            >
                <i class="fa-solid fa-minus"></i>
                {{ __('Remove options from selected products') }}
            </button>
        </div>
        <div class="d-inline-block ms-1">
            {{
                ' (' +
                checkedOptionsNum +
                ' ' +
                __('options') +
                ')' +
                ' (' +
                selectedProducts.length +
                ' ' +
                __('products') +
                ')'
            }}
            <button
                type="button"
                class="btn btn-link"
                @click="showFilters = !showFilters"
            >
                {{ showFilters ? __('Hide filters') : __('Show filters') }}
            </button>
        </div>
        <div v-if="optionsAdded" class="text-bg-success d-inline-block ms-1">
            {{ optionsAdded }}
        </div>
    </div>
    <Transition>
        <div
            v-show="showFilters"
            id="product-filters"
            class="text-center"
            :class="{ border: productFilterOptions.filters.length }"
        >
            <div
                v-for="(filter, index) in productFilterOptions.filters"
                :key="filter.id"
                class="product filter pt-1 pb-1 d-inline-block me-1 text-start"
            >
                <div class="card" style="min-width: 200px; min-height: 250px">
                    <div class="card-body">
                        <FilterDisplay
                            @checked-options-change="checkedOptionsChange"
                            :filter="filter"
                        />
                    </div>
                </div>
            </div>
        </div>
    </Transition>
</template>

<script>
import { goToSubCategory } from './commonFunctions.js';
import FilterDisplay from './FilterDisplay.vue';
import GetProductFilterOptions from './GetProductFilterOptions.vue';

export default {
    components: { FilterDisplay },
    mixins: [GetProductFilterOptions],
    props: [
        'currentCategories',
        'breadcrumb',
        'selectedCategory',
        'adminPanelGetProductsUrl',
        'getProducts',
        'categories',
        'adminPanelGetProductFilterOptionsUrl',
        'adminPanelAddOptionsToSelectedProductsUrl',
        'selectedProducts',
    ],
    data() {
        return {
            showFilters: true,
            optionsAdded: '',
            checkedOptionsAllFilters: {},
        };
    },
    computed: {
        checkedOptionsNum() {
            var checkedOptionsNum = 0;
            _.forEach(this.checkedOptionsAllFilters, function (val) {
                checkedOptionsNum += val.length;
            });
            return checkedOptionsNum;
        },
    },
    watch: {
        currentCategories: function () {
            this.removeCheckedOptionsAllFilters();
        },
    },
    methods: {
        checkedOptionsChange: function (data) {
            if (!data.checkedOptions.length) {
                delete this.checkedOptionsAllFilters[data.filterId];
            } else {
                this.checkedOptionsAllFilters[data.filterId] =
                    data.checkedOptions;
            }
            this.optionsAdded = '';
        },
        removeCheckedOptionsAllFilters: function () {
            this.checkedOptionsAllFilters = {};
            this.productFilterOptions = {
                productId: null,
                categoryId: null,
                filters: [],
                filterOptions: [],
            };
            this.optionsAdded = '';
        },
        addOptionsToSelectedProducts: function (remove = false) {
            var that = this;
            axios
                .post(this.adminPanelAddOptionsToSelectedProductsUrl, {
                    products: this.selectedProducts,
                    checkedOptionsAllFilters: this.checkedOptionsAllFilters,
                    remove: remove,
                })
                .then(function (response) {
                    that.optionsAdded = remove
                        ? __('Options removed')
                        : __('Options added');
                });
        },
        goToSubCategory,
        getCategoryProducts: function (e, category = null) {
            if (!e.target.classList.contains('show-products')) {
                return;
            }
            if (this.selectedCategory) {
                this.removeCheckedOptionsAllFilters();
                this.selectedCategory.selected = false;
                if (category.id !== this.selectedCategory.id) {
                    category.selected = true;
                }
            } else {
                category.selected = true;
            }
            if (category.selected) {
                this.getProductFilterOptions(category.id);
            }
            this.getProducts();
        },
    },
    created() {
        this.getProducts();
    },
    updated() {},
    mounted() {},
};
</script>
