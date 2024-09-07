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
    </div>
</template>

<script>
export default {
    props: [
        'selectedCategory',
        'searchValueSubmitted',
        'queryStrParamsInitialVals',
        'maxProductsPriceCeil',
    ],
    data() {
        return {};
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
    },
    methods: {},
    mounted() {},
};
</script>
