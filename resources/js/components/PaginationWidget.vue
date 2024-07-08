<template>
    <div id="pagination-widget" class='sticky-top'>
        <div class="input-group">
            <button v-if='pageInputFocus' @focus="pageInputFocus = false; page = currentPage"
                class="btn btn-outline-danger" type="button">
                <i class="fa-solid fa-xmark"></i>
            </button>
            <button v-if='!pageInputFocus' @click='goToPage(page - 1)' :disabled='currentPage === 1'
                class="btn btn-outline-secondary" type="button">
                <i class="fa-solid fa-arrow-left"></i>
            </button>
            <input v-model='page' @focus="pageInputFocus = true" @keyup.enter='goToPage(page)' class="form-control">
            <button v-if='!pageInputFocus' @click='goToPage(page + 1)' :disabled='currentPage === lastPage'
                class="btn btn-outline-secondary" type="button">
                <i class="fa-solid fa-arrow-right"></i>
            </button>
            <button v-if='pageInputFocus' @click='goToPage(page)' :disabled='page > lastPage || page < 1'
                class="btn btn-outline-success" type="button">
                <i class="fa-solid fa-arrow-right"></i>
            </button>
        </div>
    </div>
</template>

<script>
export default {
    props: ['lastPage'],
    data() {
        return {
            page: null,
            currentPage: null,
            pageInputFocus: false,
        }
    },
    watch: {
        page(newVal) {
            this.page = parseInt(newVal);
        }
    },
    methods: {
        goToPage: function (page) {
            console.debug(page);//mmmyyy
            if (0 >= page || page > this.lastPage) {
                return;
            }
            const searchParams = new URLSearchParams(window.location.search);
            searchParams.set("page", page);
            window.location = window.location.pathname + '?' + searchParams.toString();
        },
        setCurrentPageFromUrl: function () {
            const searchParams = new URLSearchParams(window.location.search);
            this.currentPage = parseInt(searchParams.get("page") || 1);
        },
    },
    updated() {
        console.debug('updated');//mmmyyy
    },
    created() {
        this.setCurrentPageFromUrl();
        this.page = this.currentPage;
    },
    mounted() {
    }
}
</script>
