<template>
    <div id="orders">
        <AdminPanelAddOrder
            :getOrders="getOrders"
            :adminPanelAddOrderUrl="adminPanelAddOrderUrl"
            :orderStatuses="orderStatuses"
            :order="order"
        />
        <div
            v-if="Object.keys(orders).length"
            id="products-container"
            class="clearfix pt-3"
        >
            <table class="table table-bordered table-hover fs-5">
                <colgroup>
                    <col style="width: 40px" />
                    <col style="width: 90px" />
                    <col />
                    <col />
                    <col style="width: 100px" />
                    <col style="width: 140px" />
                </colgroup>
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">
                            <button
                                @click="clearOrder(order)"
                                data-bs-toggle="modal"
                                data-bs-target="#addOrder"
                                class="btn btn-success"
                            >
                                <i class="fa-solid fa-plus"></i> {{ __('Add') }}
                            </button>
                        </th>
                        <th scope="col">{{ __('Date') }}</th>
                        <th scope="col">{{ __('Products') }}</th>
                        <th scope="col">{{ __('Status') }}</th>
                        <th scope="col">{{ __('Price') }} (PLN)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(o, index) in orders" :key="o.id">
                        <td scope="row">{{ index + 1 }}</td>
                        <td scope="row">
                            <button
                                @click.stop="order = { ...o }"
                                data-bs-toggle="modal"
                                data-bs-target="#addOrder"
                                class="btn btn-warning btn-sm"
                            >
                                <i class="fa-solid fa-pen-to-square"></i>
                                <span>{{ __('Edit') }}</span>
                            </button>
                        </td>
                        <td scope="row">{{ o.date }}</td>
                        <td scope="row">{{ o.productsList }}</td>
                        <td scope="row">
                            {{
                                o.order_status_id
                                    ? orderStatuses[o.order_status_id].name
                                    : ''
                            }}
                        </td>
                        <td scope="row">{{ o.price }}</td>
                    </tr>
                </tbody>
            </table>
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
import AdminPanelAddOrder from './AdminPanelAddOrder.vue';
import AdminPanelProductsPagination from './AdminPanelProductsPagination.vue';

export default {
    components: {
        AdminPanelAddOrder,
        AdminPanelProductsPagination,
    },
    props: [
        'adminPanelAddOrderUrl',
        'adminPanelGetOrdersUrl',
        'adminPanelGetOrderDataUrl',
    ],
    data() {
        return {
            pagination: {},
            order: {},
            orders: {},
            orderStatuses: {},
        };
    },
    methods: {
        clearOrder: function (order) {
            order.id = null;
            order.order_status_id = null;
            order.price = '';
            return order;
        },
        getOrdersAndData: function (getOrders = true) {
            var that = this;
            return axios
                .post(this.adminPanelGetOrderDataUrl)
                .then(function (response) {
                    that.orderStatuses = response.data.orderStatuses;
                    that.clearOrder(that.order);
                    if (getOrders) {
                        that.getOrders();
                    }
                })
                .catch(function (error) {
                    that.globalError = error.message;
                    console.log(error.message);
                });
        },
        getOrders: function (url = this.adminPanelGetOrdersUrl) {
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
