<template>
    <div id="orders">
        <div v-if="Object.keys(orders).length">
            <div id="products-container" class="clearfix pt-3">
                <div
                    v-for="o in orders"
                    :key="o.id"
                    class="product filter pt-1 pb-1"
                    style="width: 100%; max-width: none; height: auto"
                >
                    <div class="card" style="width: 100%">
                        <div class="btn-group edit-product m-1">
                            <button
                                type="button"
                                class="btn btn-secondary dropdown-toggle"
                                data-bs-toggle="dropdown"
                                aria-expanded="false"
                            >
                                <i class="fa-solid fa-ellipsis-vertical"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <a
                                        class="dropdown-item"
                                        type="button"
                                        target="_blank"
                                        :href="o.orderPaymentUrl"
                                        >{{ __('Pay') }}</a
                                    >
                                </li>
                            </ul>
                        </div>
                        <div class="card-body">
                            <div class="d-flex">
                                <div class="flex-fill p-2">{{ o.date }}</div>
                                <div class="flex-fill p-2">
                                    {{ o.productsList }}
                                </div>
                                <div class="flex-fill p-2">
                                    {{ o.priceAndCurr }}
                                </div>
                                <div class="flex-fill p-2">
                                    <b>{{
                                        o.order_status_id
                                            ? orderStatuses[o.order_status_id]
                                                  .name
                                            : ''
                                    }}</b>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <AdminPanelProductsPagination
                :pagination="pagination"
                :getItems="getOrders"
            />
        </div>
        <div v-else class="alert alert-light mt-3 text-center" role="alert">
            {{ __('No orders') }}
        </div>
    </div>
</template>

<script>
import AdminPanelProductsPagination from '../AdminPanelProductsPagination.vue';

export default {
    components: { AdminPanelProductsPagination },
    props: ['getOrdersUrl', 'adminPanelGetOrderDataUrl'],
    data() {
        return {
            pagination: {},
            orders: {},
            orderStatuses: {},
        };
    },
    methods: {
        getOrdersAndData: function (getOrders = true) {
            var that = this;
            return axios
                .post(this.adminPanelGetOrderDataUrl)
                .then(function (response) {
                    that.orderStatuses = response.data.orderStatuses;
                    if (getOrders) {
                        that.getOrders();
                    }
                })
                .catch(function (error) {
                    that.globalError = error.message;
                    console.log(error.message);
                });
        },
        getOrders: function (url = this.getOrdersUrl) {
            this.orders = {};
            var that = this;
            axios
                .post(url)
                .then(function (response) {
                    that.pagination = response.data.orders;
                    that.orders = response.data.orders.data;
                })
                .catch(function (error) {
                    that.globalError = error.message;
                    console.log(error.message);
                });
        },
    },
    created() {
        this.getOrdersAndData();
    },
    updated() {},
    mounted() {},
};
</script>
