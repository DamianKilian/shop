<template>
    <form class="order-1 pt-2 pb-2 d-flex justify-content-center m-auto" role="search" id="search">
        <div class="d-flex align-items-center search-icon"><i class="fa-solid fa-magnifying-glass"></i></div>
        <div class="input-group">
            <input @input='getSuggestions' v-model='searchValue' class="form-control" type="search"
                :placeholder="__('Search') + ' ...'" aria-label="Search">
            <button @click="$emit('search', searchValue)" class="btn btn-outline-secondary" type="button">{{
                __('Search') }}</button>
        </div>
        <button v-show='searchValue' @click='searchValue = ""' ref="clear" type="button"
            class="btn btn-danger ms-1 d-none"><i class="fa-solid fa-xmark"></i></button>
    </form>
</template>

<script>
export default {
    props: ['suggestionsUrl', 'selectedCategory'],
    data() {
        return {
            suggestions: [],
            searchValue: '',
        }
    },
    methods: {
        getSuggestions: _.debounce(function () {
            // this.suggestions = [];
            // var that = this;
            // axios
            //     .post(suggestionsUrl, { category: this.selectedCategory })
            //     .then(function (response) {
            //         that.suggestions = response.data.suggestions;
            //         console.debug(that.suggestions);//mmmyyy
            //     })
            //     .catch(function (error) {
            //         console.log(error);//mmmyyy
            //     });
        }, 500),
    },
    mounted() {
        if (this.$refs.clear) {
            this.$refs.clear.classList.remove("d-none");
        }
    }
}
</script>
