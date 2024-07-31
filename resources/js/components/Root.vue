<script>
export default {
    data() {
        return {
            productsViewLoaded: false,
            currentPage: null,
            lastPage: parseInt(window.lastPage),
            getProductsViewAllCategoriesUrl: window.getProductsViewAllCategoriesUrl,
            getProductsViewUrl: window.getProductsViewUrl,
            getProductNumsUrl: window.getProductNumsUrl,
            productNums: {},
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
        searchProducts: function (searchValue) {
            var searchValue = _.trim(searchValue);
            this.getProductsView({ searchValue: searchValue });
        },
        getProductsView: function (queryStrParams = {}) {
            this.getingProductsView = true;
            this.queryStrParams = Object.assign(this.queryStrParams, queryStrParams);
            var page = this.queryStrParams.page;
            if (0 >= page || page > this.lastPage) {
                this.getingProductsView = false;
                return this.currentPage;
            }
            if (this.getProductsViewData.categoryChildrenIds) {
                this.getProductsViewRequest();
            } else {
                this.getProductsViewRequest(this.getProductsViewAllCategoriesUrl);
            }
        },
        getProductsViewRequest: function (url = this.getProductsViewUrl) {
            var that = this;
            var url = this.setQueryStrParams(url).toString();
            axios
                .post(url, this.getProductsViewData)
                .then(function (response) {
                    that.$refs.productsView.innerHTML = response.data;
                    that.currentPage = that.queryStrParams.page;
                    window.history.replaceState(null, null, that.setQueryStrParams(window.location.href));
                    that.lastPage = that.$refs.productsView.querySelector("#products").dataset.lastPage;
                    that.productsViewLoaded = true;
                    if (that.getProductNumsUrl) {
                        that.getProductNums();
                    }
                })
                .catch(function (error) {
                    console.log(error.message);
                }).then(() => {
                    that.getingProductsView = false;
                    return that.currentPage;
                });
        },
        getProductNums: function () {
            this.productNums = {};
            var that = this;
            var url = this.setQueryStrParams(this.getProductNumsUrl).toString();
            axios
                .post(url, this.getProductsViewData)
                .then(function (response) {
                    that.setProductNums(response.data.productNums);
                })
                .catch(function (error) {
                    console.log(error.message);
                });
        },
        setProductNums: function (productNums) {
            var menu = document.getElementById('menu');
            var that = this;
            var showSubMenus = function (topEl, nesting = 0, show = true) {
                let showToggles = topEl.querySelectorAll('.show-toggle');
                _.forEach(showToggles, function (showToggle) {
                    if (show) {
                        if (showToggle.classList.contains('nesting-' + nesting)) {
                            return false;
                        }
                        showToggle.classList.add('show');
                    } else {
                        showToggle.classList.remove('show');
                    }
                });
            };
            showSubMenus(menu, 0, false);
            _.forEach(productNums, function (val) {
                that.productNums[val.slug] = val.product_num;
                let el = menu.querySelector('._' + val.slug);
                showSubMenus(el.closest(".first-li"), el.dataset.nesting);
            });
        },
        setQueryStrParams: function (url) {
            const newUrl = new URL(url);
            _.forEach(this.queryStrParams, function (value, key) {
                if ('' === value) {
                    newUrl.searchParams.delete(key);
                } else {
                    newUrl.searchParams.set(key, value);
                }
            });
            return newUrl;
        },
        getQueryStringParameters: function () {
            if (!this.$refs.productsView) {
                this.currentPage = 1;
            } else {
                const searchParams = new URLSearchParams(window.location.search);
                this.currentPage = parseInt(searchParams.get("page") || 1);
            }
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