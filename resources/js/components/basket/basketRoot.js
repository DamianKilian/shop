export default {
    data() {
        return {
            productsInBasket: {},
        };
    },
    methods: {
        addToBasket: function (e) {
            let target = e.target;
            if (!target.classList.contains('addToBasket')) {
                return;
            }
            let productId = target.dataset.productId;
            this.productsInBasket[productId] = productId;
            localStorage.setItem("productsInBasket", JSON.stringify(this.productsInBasket));
        },
        getProductsFromLocalStorage: function () {
            let productsInBasket = localStorage.getItem("productsInBasket");
            if (productsInBasket) {
                this.productsInBasket = JSON.parse(productsInBasket);
            }
        }
    },
    mounted() {
        this.getProductsFromLocalStorage();
    },
};