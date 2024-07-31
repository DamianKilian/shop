<template>
    <div v-if='1 < lastPage' id="pagination-widget" class='sticky-top'>
        <div class="input-group">
            <button v-if='pageInputFocus' @focus="pageInputFocus = false; page = currentPage"
                class="btn btn-outline-danger" type="button">
                <i class="fa-solid fa-xmark"></i>
            </button>
            <button v-if='!pageInputFocus' @click='getProductsViewPw({ page: page - 1 })' :disabled='currentPage === 1'
                class="btn btn-outline-secondary" type="button">
                <i class="fa-solid fa-arrow-left"></i>
            </button>
            <input ref='pwi' v-model='page' @focus="pageInputFocus = true"
                @keyup.enter='getProductsViewPw({ page: page }); pageInputFocus = false; $refs.pwi.blur()'
                class="form-control">
            <button v-if='!pageInputFocus' @click='getProductsViewPw({ page: page + 1 })'
                :disabled='currentPage === lastPage' class="btn btn-outline-secondary" type="button">
                <i class="fa-solid fa-arrow-right"></i>
            </button>
            <button v-if='pageInputFocus'
                @click='if (page != currentPage) getProductsViewPw({ page: page }); pageInputFocus = false;'
                :disabled='page > lastPage || page < 1' class="btn btn-outline-success" type="button">
                <i class="fa-solid fa-arrow-right"></i>
            </button>
        </div>
    </div>
</template>

<script>
export default {
    props: ['lastPage', 'currentPage', 'getProductsView', 'getingProductsView'],
    data() {
        return {
            page: null,
            pageInputFocus: false,
        }
    },
    watch: {
        currentPage(newVal) {
            this.page = parseInt(newVal);
        }
    },
    methods: {
        getProductsViewPw: function (queryStrParams) {
            if (this.getingProductsView) {
                console.log('getingProductsView');
                return;
            }
            this.getProductsView(queryStrParams);
        },
    },
    updated() {
        console.debug('updated');//mmmyyy
    },
    created() { },
    mounted() {
        this.page = this.currentPage;
    }
}
</script>
