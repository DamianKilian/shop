<template>
    <form
        class="order-1 pt-2 pb-2 d-flex justify-content-center m-auto"
        role="search"
        id="search"
    >
        <div class="d-flex align-items-center search-icon">
            <i class="fa-solid fa-magnifying-glass"></i>
        </div>
        <div class="input-group">
            <input
                @keypress.enter.prevent="$emit('search', searchValue)"
                v-model="searchValue"
                class="form-control"
                type="search"
                :placeholder="__('Search') + categoryNameText() + ' ...'"
                aria-label="Search"
            />
            <button
                @click="$emit('search', searchValue)"
                class="btn btn-outline-secondary"
                type="button"
            >
                {{ __('Search') }}
            </button>
        </div>
        <button
            v-show="searchValue"
            @click="searchValue = ''"
            ref="clear"
            type="button"
            class="btn btn-danger ms-1 d-none"
        >
            <i class="fa-solid fa-xmark"></i>
        </button>
    </form>
</template>

<script>
export default {
    props: ['categoryName'],
    data() {
        return {
            searchValue: '',
        };
    },
    methods: {
        categoryNameText: function () {
            return this.categoryName
                ? ' ' + __('in') + ' "' + this.categoryName + '"'
                : '';
        },
    },
    mounted() {
        if (this.$refs.clear) {
            this.$refs.clear.classList.remove('d-none');
        }
    },
};
</script>
