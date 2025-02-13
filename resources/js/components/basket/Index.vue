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
    <div v-else id="productsInBasket">
        <table class="table table-sm table-striped table-hover fs-5">
            <thead>
                <tr>
                    <th scope="col"></th>
                    <th scope="col" colspan="2">
                        <button class="btn btn-secondary clear-btn">
                            <i class="fa-regular fa-trash-can"></i> Clear
                        </button>
                    </th>
                </tr>
            </thead>
            <tbody class="table-group-divider">
                <tr v-for="(product, id, index) in productsInBasket" :key="id">
                    <td scope="row">{{ index + 1 }}</td>
                    <td class="position-relative">
                        <div class="product-data">
                            <a
                                class="link d-sm-flex align-items-center"
                                :href="productsInBasketData[id].url"
                            >
                                <img
                                    :src="productsInBasketData[id].fullUrlSmall"
                                    class="product-img"
                                />
                                {{ productsInBasketData[id].title }}
                            </a>
                        </div>
                        <div class="product-num">
                            <div class="flex-nowrap input-group input-group-sm">
                                <button class="btn btn-outline-secondary">
                                    <i class="fa-solid fa-minus"></i>
                                </button>
                                <input
                                    v-model="product.num"
                                    class="form-control"
                                />
                                <button class="btn btn-outline-secondary">
                                    <i class="fa-solid fa-plus"></i>
                                </button>
                            </div>
                        </div>
                    </td>
                    <td>
                        <button class="btn btn-secondary clear-btn">
                            <i class="fa-regular fa-trash-can"></i>
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<script>
export default {
    props: ['productsInBasket', 'getProductsInBasketDataUrl'],
    data() {
        return {
            productsInBasketData: {},
        };
    },
    methods: {
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
    },
    mounted() {
        this.getProductsInBasketData();
    },
};
</script>
