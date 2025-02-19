export default {
    data() {
        return {
            basketReady: false,
            productsInBasket: {},
        };
    },
    methods: {
        removeFromBasketAll: function () {
            this.productsInBasket = {};
            this.setProductsInLocalStorage();
        },
        addToBasket: function (e) {
            let target = e.target;
            if (!target.classList.contains('addToBasket')) {
                return;
            }
            let productId = target.dataset.productId;
            this.productsInBasket[productId] = { num: 1 };
            this.setProductsInLocalStorage();
        },
        setProductsInLocalStorage: function () {
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
        this.basketReady = true;
    },
};