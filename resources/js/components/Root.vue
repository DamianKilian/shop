<script>
export default {
    data() {
        return {
            currentPage: null,
            lastPage: parseInt(window.lastPage),
            getProductsViewUrl: window.getProductsViewUrl,
            getingProductsView: false,
            queryStrParams: {
                page: null,
            },
            getProductsViewData: {
                categoryChildrenIds: window.categoryChildrenIds
            }
        }
    },
    methods: {
        applyPageChangeEvents: function () {
            if (!this.$refs.productsView) {
                return;
            }
            var that = this;
            this.$refs.productsView.addEventListener("click", function (e) {
                if (e.target.matches('a.page-link')) {
                    e.preventDefault();
                    that.pageChange(e.target.href);
                }
            });
        },
        pageChange: function (url) {
            const searchParams = new URLSearchParams(url.substring(url.indexOf("?")));
            this.getProductsView({ page: parseInt(searchParams.get("page")) });
        },
        getProductsView: function (queryStrParams) {
            this.getingProductsView = true;
            this.queryStrParams = Object.assign(this.queryStrParams, queryStrParams);
            var that = this;
            var page = this.queryStrParams.page;
            if (0 >= page || page > this.lastPage || page === this.currentPage) {
                this.getingProductsView = false;
                return this.currentPage;
            }
            var url = this.setQueryStrParams(this.getProductsViewUrl).toString();
            axios
                .post(url, this.getProductsViewData)
                .then(function (response) {
                    that.$refs.productsView.innerHTML = response.data;
                    that.currentPage = page;
                    window.history.replaceState(null, null, that.setQueryStrParams(window.location.href));
                })
                .catch(function (error) {
                    console.log(error.message);
                }).then(() => {
                    this.getingProductsView = false;
                    return that.currentPage;
                });
        },
        setQueryStrParams: function (url) {
            const newUrl = new URL(url);
            _.forEach(this.queryStrParams, function (value, key) {
                newUrl.searchParams.set(key, value);
            });
            return newUrl;
        },
        getQueryStringParameters: function () {
            const searchParams = new URLSearchParams(window.location.search);
            this.currentPage = parseInt(searchParams.get("page") || 1);
        },
    },
    updated() {
        console.log('updated-root')
    },
    created() {
        this.getQueryStringParameters();
        this.queryStrParams.page = this.currentPage;
    },
    mounted() {
        this.applyPageChangeEvents();
    }
}
</script>