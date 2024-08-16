<template>
    <div id="filters">
        <div class="mt-3 actions-global clearfix">
            <div class='float-start'>
                <select @change='getFilters()' v-model="selectedCategoryId" id='category-select' class="form-select">
                    <option :value="null" selected>{{ __('Category select') }} ...</option>
                    <option v-for="option in categoryOptions" :value="option.id">
                        {{ option.patchName }}
                    </option>
                </select>
            </div>
            <button @click='editFilter = null' data-bs-toggle="modal" data-bs-target="#addFilter"
                class="btn btn-success float-end mt-1 mt-sm-0">
                <i class="fa-solid fa-plus"></i> {{ __('Add filter') }}
            </button>
            <button class="btn btn-danger float-end me-1 mt-1 mt-sm-0" @click="deleteFilters">
                <i class="fa-solid fa-trash"></i> {{ __('Remove') }}
            </button>
        </div>
    </div>
    <AdminPanelAddFilter :getFilters='getFilters' :editFilter='editFilter'
        :adminPanelAddFilterUrl='adminPanelAddFilterUrl' />
    <div v-if='filters.length' id='products-container' class='clearfix pt-3'>
        <div v-for="(filter, index) in filters" :key="filter.id"
            :class='{ "bg-primary bg-opacity-75": filter.selected }' class="product pt-1 pb-1">
            <div class="card">
                <div @click='filter.selected = !filter.selected' class="card-img border-bottom">
                    <input class="m-1 form-check-input position-absolute" type="checkbox" v-model="filter.selected">
                    <button @click.stop='editFilter = filter' data-bs-toggle="modal" data-bs-target="#addFilter"
                        class="btn btn-warning btn-sm edit-product"><i class="fa-solid fa-pen-to-square"></i> <span>{{
                            __('Edit') }}</span></button>
                </div>
                <div class="card-body">
                    <h5 class="card-title fw-bolder">{{ filter.filter.name }}</h5>
                    <p>descStr descStr descStr</p>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

import AdminPanelAddFilter from './AdminPanelAddFilter.vue'

export default {
    components: { AdminPanelAddFilter },
    props: ['adminPanelGetFiltersUrl', 'adminPanelAddFilterUrl', 'adminPanelDeleteFilterUrl', 'categoryOptionsProp'],
    data() {
        return {
            categoryOptions: JSON.parse(this.categoryOptionsProp),
            selectedCategoryId: null,
            editFilter: null,
            filters: [],
        }
    },
    methods: {
        getFilters: function (url = this.adminPanelGetFiltersUrl) {
            this.filters = [];
            var that = this;
            axios
                .post(url, { categoryId: this.selectedCategoryId })
                .then(function (response) {
                    that.pagination = response.data.filters;
                    that.arrangeFilters(that.pagination.data);
                })
                .catch(function (error) {
                    that.globalError = error.message;
                    console.log(error.message);
                });
        },
        arrangeFilters: function (filters) {
            var that = this;
            _.forEach(filters, function (filter) {
                that.filters.push({
                    filter: filter,
                    selected: false,
                });
            });
        },
    },
    created() {
        this.getFilters();
    },
    updated() {
    },
    mounted() {
    }
}
</script>
