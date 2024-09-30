<template>
    <div class="search-filters">
        <button
            @click="$emit('removeSelectedCategory')"
            v-if="selectedCategory"
            type="button"
            class="btn btn-outline-primary me-1"
        >
            <i class="fa-solid fa-xmark text-danger"></i> {{ __('Category') }}:
            <b>{{ selectedCategory.name }}</b>
        </button>
        <button
            @click="$emit('removeSearchValueSubmitted')"
            v-if="searchValueSubmitted"
            type="button"
            class="btn btn-outline-primary me-1"
        >
            <i class="fa-solid fa-xmark text-danger"></i> {{ __('Search') }}:
            <b>{{ searchValueSubmitted }}</b>
        </button>
        <button
            @click="$emit('removeMaxPriceSubmitted')"
            v-if="maxPrice"
            type="button"
            class="btn btn-outline-primary me-1"
        >
            <i class="fa-solid fa-xmark text-danger"></i>
            {{ __('Max. price') }}:
            <b>{{ queryStrParamsInitialVals.maxPrice }}</b>
        </button>
        <button
            @click="$emit('removeMinPriceSubmitted')"
            v-if="minPrice"
            type="button"
            class="btn btn-outline-primary me-1"
        >
            <i class="fa-solid fa-xmark text-danger"></i>
            {{ __('Min. price') }}:
            <b>{{ queryStrParamsInitialVals.minPrice }}</b>
        </button>
        <button
            v-for="option in filterOptions"
            @click="$emit('removeFilterSubmitted', option.optionId)"
            type="button"
            class="btn btn-outline-primary me-1"
        >
            <i class="fa-solid fa-xmark text-danger"></i>
            {{ option.filterName }}:
            <b>{{ option.optionName }}</b>
        </button>
    </div>
</template>

<script>
export default {
    props: [
        'selectedCategory',
        'searchValueSubmitted',
        'queryStrParamsInitialVals',
        'maxProductsPriceCeil',
        'filters',
    ],
    data() {
        return {
            filterOptionToFilter: this.getFilterOptionToFilter(),
        };
    },
    computed: {
        maxPrice() {
            if (!this.queryStrParamsInitialVals) {
                return false;
            }
            return (
                this.maxProductsPriceCeil !==
                this.queryStrParamsInitialVals.maxPrice
            );
        },
        minPrice() {
            if (!this.queryStrParamsInitialVals) {
                return false;
            }
            return 0 !== this.queryStrParamsInitialVals.minPrice;
        },
        filterOptions() {
            var fo = this.queryStrParamsInitialVals
                ? this.queryStrParamsInitialVals.filterOptions
                : null;
            if (!fo) {
                return [];
            }
            var foArr = fo.split('|');
            var filterOptions = [];
            var that = this;
            _.forEach(foArr, function (optionId) {
                filterOptions.push(that.filterOptionToFilter[optionId]);
            });
            filterOptions.sort((a, b) => {
                const nameA = a.filterName.toUpperCase();
                const nameB = b.filterName.toUpperCase();
                if (nameA < nameB) {
                    return -1;
                }
                if (nameA > nameB) {
                    return 1;
                }
                return 0;
            });
            return filterOptions;
        },
    },
    methods: {
        getFilterOptionToFilter: function () {
            var filterOptionToFilter = {};
            _.forEach(this.filters, function (filter) {
                _.forEach(filter.filter_options, function (option) {
                    filterOptionToFilter[option.id] = {
                        filterName: filter.name,
                        optionName: option.name,
                        optionId: option.id,
                    };
                });
            });
            return filterOptionToFilter;
        },
    },
    mounted() {},
};
</script>
