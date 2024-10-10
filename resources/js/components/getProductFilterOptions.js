export default {
    data() {
        return {
            productFilterOptions: {
                productId: null,
                categoryId: null,
                filters: [],
                filterOptions: [],
            },
        };
    },
    methods: {
        getProductFilterOptions: function (categoryId) {
            this.productFilterOptions = {
                filters: [],
                filterOptions: [],
            };
            var productId = null;
            if (this.editProduct) {
                productId = this.editProduct.product.id;
                categoryId = categoryId || this.editProduct.product.category.id;
            }
            if (!categoryId) {
                return;
            }
            this.productFilterOptions.productId = productId;
            this.productFilterOptions.categoryId = categoryId;
            var that = this;
            axios
                .post(this.adminPanelGetProductFilterOptionsUrl, {
                    categoryId: categoryId,
                    productId: productId,
                })
                .then(function (response) {
                    if (
                        response.data.categoryId !== that.productFilterOptions.categoryId ||
                        response.data.productId !== that.productFilterOptions.productId
                    ) {
                        return;
                    }
                    that.productFilterOptions = {
                        filters: response.data.filters,
                        filterOptions: response.data.filterOptions,
                    };
                });
        },
    },
};
