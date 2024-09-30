<template>
    <div class="search-filters">
        <button @click="$emit('removeSelectedCategory')" v-if='selectedCategory' type="button"
            class="btn btn-outline-primary me-1">
            <i class="fa-solid fa-xmark text-danger"></i> {{ __('Category') }}: <b>{{ selectedCategory.name }}</b>
        </button>
        <button @click="$emit('removeSearchValueSubmitted')" v-if='searchValueSubmitted' type="button"
            class="btn btn-outline-primary">
            <i class="fa-solid fa-xmark text-danger"></i> {{ __('Search') }}: <b>{{ searchValueSubmitted }}</b>
        </button>
    </div>
</template>

<script>
export default {
    props: ['selectedCategory', 'searchValueSubmitted'],
    data() {
        return {
        }
    },
    methods: {
    },
    mounted() {
    }
}
</script>