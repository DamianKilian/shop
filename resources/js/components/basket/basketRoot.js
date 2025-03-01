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
            this.changeBtnIfAdded(target);
        },
        setProductsInLocalStorage: function () {
            sessionStorage.setItem("productsInBasket", JSON.stringify(this.productsInBasket));
        },
        getProductsFromLocalStorage: function () {
            let productsInBasket = sessionStorage.getItem("productsInBasket");
            if (productsInBasket) {
                this.productsInBasket = JSON.parse(productsInBasket);
            }
        },
        changeBtnIfAdded: function (btn) {
            let productId = btn.dataset.productId;
            if (_.isUndefined(this.productsInBasket[productId])) {
                return;
            }
            btn.classList.add("btn-success");
            btn.innerHTML = 'Added <i class="fa-solid fa-check"></i>';
        },
        changeBtnIfAddedAll: function () {
            var that = this;
            let btns = document.querySelectorAll('#products-view .addToBasket');
            _.forEach(btns, (btn)=>{
                that.changeBtnIfAdded(btn);
            });
        },
    },
    mounted() {
        this.getProductsFromLocalStorage();
        this.basketReady = true;
        this.changeBtnIfAddedAll();
    },
};