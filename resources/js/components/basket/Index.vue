<template>
    <div v-if="!Object.keys(productsInBasket).length" id="empty-basket">
        <div class="card text-center">
            <div class="card-header fs-1">
                <i class="fa-solid fa-basket-shopping"></i>
            </div>
            <div class="card-body">
                <h5 class="card-title">{{ __('Your basket is epmty') }}</h5>
                <a href="/" class="btn btn-primary">Go to homepage</a>
            </div>
        </div>
    </div>
    <div v-else-if="Object.keys(productsInBasketData).length" class="row">
        <div id="productsInBasket" class="col-md-8">
            <table class="table table-sm table-striped table-hover fs-5">
                <thead>
                    <tr>
                        <th colspan="3">
                            <span class="fs-2">{{ __('Products') }}</span>
                            <button
                                @click="$emit('removeFromBasketAll')"
                                class="btn btn-secondary clear-btn"
                            >
                                <i class="fa-regular fa-trash-can"></i>
                                Clear
                            </button>
                        </th>
                    </tr>
                </thead>
                <tbody class="table-group-divider">
                    <template
                        v-for="(product, id, index) in productsInBasket"
                        :key="id"
                    >
                        <tr v-if="productsInBasketData[id]">
                            <td scope="row">{{ index + 1 }}</td>
                            <td class="position-relative">
                                <div class="product-data">
                                    <a
                                        class="link d-lg-flex align-items-center text-decoration-none"
                                        :href="productsInBasketData[id].url"
                                    >
                                        <img
                                            :src="
                                                productsInBasketData[id]
                                                    .fullUrlSmall
                                            "
                                            class="product-img"
                                        />
                                        <span class="flex-grow-1">{{
                                            productsInBasketData[id].title
                                        }}</span>
                                        <span class="ms-3 text-dark"
                                            >{{
                                                productsInBasketData[id].price
                                            }}zł</span
                                        >
                                    </a>
                                </div>
                                <div class="product-num">
                                    <div
                                        class="flex-nowrap input-group input-group-sm"
                                    >
                                        <button
                                            @click="productNum(-1, id)"
                                            class="btn btn-outline-secondary"
                                        >
                                            <i class="fa-solid fa-minus"></i>
                                        </button>
                                        <input
                                            v-model="product.num"
                                            class="form-control"
                                        />
                                        <button
                                            @click="productNum(1, id)"
                                            class="btn btn-outline-secondary"
                                        >
                                            <i class="fa-solid fa-plus"></i>
                                        </button>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <button
                                    @click="removeFromBasket(id)"
                                    class="btn btn-secondary clear-btn"
                                >
                                    <i class="fa-regular fa-trash-can"></i>
                                </button>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
            <div id="deliveryMethods">
                <h2><b>Delivery methods</b></h2>
                <div class="list-group">
                    <label
                        v-for="(method, key) in deliveryMethods"
                        class="list-group-item list-group-item-action"
                        :class="{ active: deliveryMethod === key }"
                    >
                        <div class="d-flex w-100 justify-content-between">
                            <span class="fs-3">
                                <input
                                    @change="deliveryMethodChange"
                                    class="form-check-input"
                                    type="radio"
                                    :name="key"
                                    :value="key"
                                    v-model="deliveryMethod"
                                />
                                {{ method.name }}
                            </span>
                            <span class="fs-3">{{ method.price }}</span>
                        </div>
                    </label>
                </div>
            </div>
        </div>
        <div class="col-md-4 bg-light position-relative">
            <loading-overlay v-if="loadingCalculations"></loading-overlay>
            <div id="order-summary" class="sticky-md-top">
                <h3>
                    <b>{{ __('Summary') }}</b>
                </h3>
                <div>
                    <div class="fs-4">
                        {{ __('Products') }}
                        <b class="float-end">{{ summary.productsPrice }}</b>
                    </div>
                    <div class="fs-4">
                        {{ __('Delivery') }}
                        <span v-if="deliveryMethod"
                            >({{
                                deliveryMethods[deliveryMethod]['name']
                            }})</span
                        >
                        <b class="float-end">{{ summary.deliveryPrice }}</b>
                    </div>
                    <hr />
                    <div class="fs-4">
                        {{ __('Total price') }}
                        <b class="float-end">{{ summary.totalPrice }}</b>
                    </div>
                </div>
                <form :action="orderStoreUrl" method="post">
                    <input type="hidden" name="_token" :value="csrfToken" />
                    <input
                        type="hidden"
                        name="productsInBasket"
                        :value="JSON.stringify(productsInBasket)"
                    />
                    <input
                        type="hidden"
                        name="deliveryMethod"
                        :value="deliveryMethod"
                    />
                    <button
                        type="submit"
                        class="btn btn-primary btn-lg w-100 mt-2"
                    >
                        {{ __('Place your order') }}
                    </button>
                    <div v-if="error" class="text-bg-danger p-1 m-1">
                        {{ error }}
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: [
        'error',
        'productsInBasket',
        'getProductsInBasketDataUrl',
        'getBasketSummaryUrl',
        'orderStoreUrl',
        'deliveryMethods',
        'setProductsInLocalStorage',
    ],
    data() {
        return {
            csrfToken: document.querySelector("meta[name='csrf-token']")
                .content,
            basketLastChange: null,
            loadingCalculations: false,
            productsInBasketData: {},
            deliveryMethod: '',
            summary: {
                productsPrice: null,
                deliveryPrice: null,
                totalPrice: null,
            },
        };
    },
    methods: {
        deliveryMethodChange: function () {
            this.setDeliveryMethodInLocalStorage();
            this.getBasketSummary();
        },
        setDeliveryMethodInLocalStorage: function () {
            var deliveryMethodData = {
                deliveryMethod: this.deliveryMethod,
            };
            localStorage.setItem(
                'deliveryMethodData',
                JSON.stringify(deliveryMethodData)
            );
        },
        getDeliveryMethodFromLocalStorage: function () {
            let deliveryMethodData = localStorage.getItem('deliveryMethodData');
            if (deliveryMethodData) {
                this.deliveryMethod =
                    JSON.parse(deliveryMethodData).deliveryMethod;
            }
        },
        productNum: function (num, id) {
            this.productsInBasket[id]['num'] += num;
            if (!this.productsInBasket[id]['num']) {
                this.removeFromBasket(id);
                return;
            }
            this.setProductsInLocalStorage();
            this.getBasketSummary();
        },
        removeFromBasket: function (id) {
            delete this.productsInBasket[id];
            this.setProductsInLocalStorage();
            this.getBasketSummary();
        },
        getProductsInBasketData: function () {
            var that = this;
            axios
                .post(this.getProductsInBasketDataUrl, {
                    productsInBasket: this.productsInBasket,
                })
                .then(function (response) {
                    that.productsInBasketData =
                        response.data.productsInBasketData;
                });
        },
        getBasketSummary: _.debounce(function () {
            this.basketLastChange = Date.now();
            this.loadingCalculations = true;
            var that = this;
            axios
                .post(this.getBasketSummaryUrl, {
                    basketLastChange: this.basketLastChange,
                    productsInBasket: this.productsInBasket,
                    deliveryMethod: this.deliveryMethod,
                })
                .then(function (response) {
                    if (
                        response.data.basketLastChange !== that.basketLastChange
                    ) {
                        return;
                    }
                    that.summary = response.data.summary;
                    that.loadingCalculations = false;
                });
        }, 500),
    },
    mounted() {
        if (Object.keys(this.productsInBasket).length) {
            this.getDeliveryMethodFromLocalStorage();
            this.getProductsInBasketData();
            this.getBasketSummary();
        }
    },
};
</script>
