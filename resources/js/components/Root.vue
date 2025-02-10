<script>
import FilterDisplayApp from './FilterDisplayApp.vue';
import SearchApp from './SearchApp.vue';
import SearchFiltersApp from './SearchFiltersApp.vue';
import historyNavigation from './historyNavigation';
import basketRoot from './basket/basketRoot';

export default {
    mixins: [historyNavigation, basketRoot],
    components: { FilterDisplayApp, SearchApp, SearchFiltersApp },
    data() {
        return {
            productsViewLoaded: false,
            currentPage: null,
            lastPage: parseInt(window.lastPage),
            getProductsViewAllCategoriesUrl:
                window.getProductsViewAllCategoriesUrl,
            getProductsViewUrl: window.getProductsViewUrl,
            getProductNumsUrl: window.getProductNumsUrl,
            productNums: {},
            getingProductsView: false,
            queryStrParams: {
                page: null,
                searchValue: '',
                minPrice: null,
                maxPrice: null,
                filterOptions: '',
                sortValue: '',
                categoryChildrenIds: '',
            },
            checkedOptionsGlobal: [],
            queryStrParamsInitialVals: {},
            maxProductsPriceCeil: window.maxProductsPrice
                ? _.ceil(window.maxProductsPrice)
                : null,
            failedValidation: {},
        };
    },
    methods: {
        isFilterChanged: function (filter) {
            if ('price' === filter) {
                if (
                    this.queryStrParamsInitialVals.minPrice !==
                        this.queryStrParams.minPrice ||
                    this.queryStrParamsInitialVals.maxPrice !==
                        this.queryStrParams.maxPrice
                ) {
                    return true;
                }
            }
            return false;
        },
        applyPageChangeEvents: function () {
            var that = this;
            this.$refs.productsView.addEventListener('click', function (e) {
                if (e.target.matches('a.page-link')) {
                    e.preventDefault();
                    that.pageChange(e.target.href);
                }
            });
        },
        applyFilters: function (queryStrParams = {}) {
            this.queryStrParams.filterOptions = this.checkedOptionsGlobal
                .sort()
                .join('|');
            return this.getProductsView(queryStrParams);
        },
        removeFilterSubmitted: function (optionId) {
            var that = this;
            _.forEach(this.checkedOptionsGlobal, function (ogId, index) {
                if (ogId == optionId) {
                    that.checkedOptionsGlobal.splice(index, 1);
                }
            });
            this.queryStrParams.filterOptions = this.checkedOptionsGlobal
                .sort()
                .join('|');
            return this.getProductsView();
        },
        pageChange: function (url) {
            const searchParams = new URLSearchParams(
                url.substring(url.indexOf('?'))
            );
            return this.getProductsView(
                { page: parseInt(searchParams.get('page')) },
                true
            );
        },
        searchProducts: function (searchValue, searchUrl = '') {
            var searchValue = _.trim(searchValue);
            if (searchUrl) {
                window.location.href =
                    searchUrl + '/?searchValue=' + searchValue;
                return;
            }
            return this.getProductsView({ searchValue: searchValue });
        },
        sortProducts: function (e) {
            return this.getProductsView({ sortValue: e.target.value });
        },
        getProductsView: function (
            queryStrParams = {},
            pageChange = false,
            historyPushState = true
        ) {
            this.getingProductsView = true;
            if (!queryStrParams.page) {
                queryStrParams.page = 1;
            }
            this.queryStrParams = Object.assign(
                this.queryStrParams,
                queryStrParams
            );
            var page = this.queryStrParams.page;
            if (0 >= page || page > this.lastPage) {
                this.getingProductsView = false;
                return;
            }
            if (this.queryStrParams.categoryChildrenIds) {
                return this.getProductsViewRequest(
                    this.getProductsViewUrl,
                    pageChange,
                    historyPushState
                );
            } else {
                return this.getProductsViewRequest(
                    this.getProductsViewAllCategoriesUrl,
                    pageChange,
                    historyPushState
                );
            }
        },
        getProductsViewRequest: function (
            url = this.getProductsViewUrl,
            pageChange = false,
            historyPushState = true
        ) {
            var that = this;
            var url = this.setQueryStrParams(url).toString();
            this.failedValidation = {};
            return axios
                .post(url)
                .then(function (response) {
                    that.$refs.productsView.innerHTML = response.data;
                    that.currentPage = that.queryStrParams.page;
                    if (historyPushState) {
                        window.history.pushState(
                            { ...that.queryStrParams },
                            '',
                            that.setQueryStrParams(window.location.href)
                        );
                    }
                    that.lastPage =
                        that.$refs.productsView.querySelector(
                            '#products'
                        ).dataset.lastPage;
                    that.productsViewLoaded = true;
                    if (that.getProductNumsUrl && !pageChange) {
                        that.getProductNums();
                    }
                })
                .catch(function (error) {
                    if (_.has(error, 'response.data.failedValidation')) {
                        that.failedValidation =
                            error.response.data.failedValidation;
                    }
                    console.log(error);
                })
                .then(() => {
                    that.getingProductsView = false;
                    that.queryStrParamsInitialVals = _.clone(
                        this.queryStrParams
                    );
                });
        },
        getProductNums: function () {
            this.productNums = {};
            var that = this;
            var url = this.setQueryStrParams(this.getProductNumsUrl).toString();
            axios
                .post(url)
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
            var showSubMenus = function (topEl, nesting = 0, elBadged = null) {
                let showToggles = topEl.querySelectorAll('.show-toggle');
                _.forEach(showToggles, function (showToggle) {
                    if (elBadged) {
                        if (
                            showToggle.classList.contains('nesting-' + nesting)
                        ) {
                            return false;
                        }
                        let li = showToggle.closest('li');
                        if (li.contains(elBadged)) {
                            showToggle.classList.add('show');
                        }
                    } else {
                        showToggle.classList.remove('show');
                    }
                });
            };
            showSubMenus(menu, 0);
            _.forEach(productNums, function (val) {
                that.productNums[val.slug] = val.product_num;
                let elBadged = menu.querySelector('._' + val.slug);
                showSubMenus(
                    elBadged.closest('.first-li'),
                    elBadged.dataset.nesting,
                    elBadged
                );
            });
        },
        setQueryStrParams: function (url) {
            const newUrl = new URL(url);
            var that = this;
            _.forEach(this.queryStrParams, function (value, key) {
                if ('' === value || null === value) {
                    newUrl.searchParams.delete(key);
                } else if (
                    'maxPrice' === key &&
                    that.maxProductsPriceCeil === value
                ) {
                    newUrl.searchParams.delete(key);
                } else if ('minPrice' === key && 0 === value) {
                    newUrl.searchParams.delete(key);
                } else {
                    newUrl.searchParams.set(key, value);
                }
            });
            return newUrl;
        },
        deleteQueryStrParams: function () {
            const newUrl = new URL(window.location.href);
            var that = this;
            _.forEach(this.queryStrParams, function (value, key) {
                newUrl.searchParams.delete(key);
            });
            window.history.replaceState(null, '', newUrl);
        },
        getQueryStringParameters: function () {
            const searchParams = new URLSearchParams(window.location.search);
            this.currentPage = parseInt(searchParams.get('page') || 1);
            this.queryStrParams.page = this.currentPage;
            this.queryStrParams.searchValue =
                searchParams.get('searchValue') || '';
            this.queryStrParams.sortValue =
                searchParams.get('sortValue') || '';
            this.queryStrParams.minPrice =
                parseInt(searchParams.get('minPrice')) || 0;
            this.queryStrParams.maxPrice =
                parseInt(searchParams.get('maxPrice')) ||
                this.maxProductsPriceCeil;
            this.queryStrParams.filterOptions =
                searchParams.get('filterOptions') || '';
            this.checkedOptionsGlobal = searchParams.get('filterOptions')
                ? searchParams.get('filterOptions').split('|')
                : [];
            this.checkedOptionsGlobal = this.checkedOptionsGlobal.map(Number);
            this.queryStrParams.categoryChildrenIds =
                searchParams.get('categoryChildrenIds') ||
                window.categoryChildrenIds ||
                '';
        },
        getQueryStringParam: function (param) {
            const searchParams = new URLSearchParams(window.location.search);
            return searchParams.get(param);
        },
        preserveFilters: function () {
            var that = this;
            document.addEventListener('click', function (e) {
                if (
                    that.queryStrParams.searchValue &&
                    (e.target.matches('.main-menu-link') ||
                        e.target.matches('.product-breadcrumb-badge-link'))
                ) {
                    e.target.setAttribute(
                        'href',
                        that
                            .setQueryStrParams(e.target.getAttribute('href'))
                            .toString()
                    );
                }
            });
        },
    },
    updated() {
        console.log('updated-root');
    },
    created() {},
    mounted() {
        if (this.$refs.productsView) {
            this.applyPageChangeEvents();
            if ('homePage' === window.pageType) {
                this.preserveFilters();
                if (!this.getQueryStringParam('searchValue')) {
                    this.deleteQueryStrParams();
                }
            }
            this.getQueryStringParameters();
            this.queryStrParamsInitialVals = _.clone(this.queryStrParams);
            if ('homePage' === window.pageType) {
                if (this.queryStrParams.searchValue) {
                    this.getProductNums();
                }
            }
        }
    },
};
</script>
