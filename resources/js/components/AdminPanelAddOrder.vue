<template>
    <div
        class="modal fade"
        id="addOrder"
        tabindex="-1"
        aria-labelledby="modalLabel"
        aria-hidden="true"
    >
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="modalLabel">
                        {{ title }}
                    </h1>
                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                    ></button>
                </div>
                <form
                    @submit="addOrder"
                    ref="addOrder"
                    method="post"
                    class="position-relative"
                >
                    <div class="modal-body">
                        <div class="clearfix">
                            <input
                                type="hidden"
                                name="orderId"
                                v-model="order.id"
                            />
                            <div class="row g-2 mb-2">
                                <div class="col-sm-6">
                                    <div
                                        class="form-floating float-start w-100"
                                    >
                                        <input
                                            v-model="order.price"
                                            name="price"
                                            ref="price"
                                            :class="{
                                                'is-invalid':
                                                    failedValidation.price,
                                            }"
                                            class="form-control"
                                            :placeholder="__('Price')"
                                        />
                                        <label for="price"
                                            >{{ __('Price') }} *</label
                                        >
                                        <div class="invalid-feedback">
                                            {{
                                                failedValidation.price
                                                    ? failedValidation.price[0]
                                                    : ''
                                            }}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-floating">
                                        <select
                                            v-model="order.order_status_id"
                                            name="orderStatusId"
                                            ref="order_status_id"
                                            :class="{
                                                'is-invalid':
                                                    failedValidation.order_status_id,
                                                'form-control-plaintext':
                                                    readonly,
                                                'border-0': readonly,
                                            }"
                                            class="form-select w-100"
                                            :readonly="readonly"
                                        >
                                            <option
                                                v-for="orderStatus in orderStatuses"
                                                :key="orderStatus.id"
                                                :value="orderStatus.id"
                                            >
                                                {{ orderStatus.name }}
                                            </option>
                                        </select>
                                        <label for="order_status_id"
                                            >{{ __('Order status') }} *</label
                                        >
                                        <div class="invalid-feedback">
                                            {{
                                                failedValidation.order_status_id
                                                    ? failedValidation
                                                          .order_status_id[0]
                                                    : ''
                                            }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button
                            ref="closeModal"
                            type="button"
                            class="btn btn-secondary"
                            data-bs-dismiss="modal"
                        >
                            {{ __('Close') }}
                        </button>
                        <button type="submit" class="btn btn-success">
                            <i class="fa-solid fa-plus"></i>
                            {{ title }}
                        </button>
                        <div
                            v-if="globalError"
                            class="text-bg-danger float-end mt-1"
                        >
                            {{ globalError }}
                        </div>
                        <div
                            v-if="globalSuccess"
                            class="text-bg-success float-end mt-1"
                        >
                            {{ globalSuccess }}
                        </div>
                    </div>
                    <loading-overlay v-if="addingOrder" />
                </form>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: ['adminPanelAddOrderUrl', 'getOrders', 'order', 'orderStatuses'],
    data() {
        return {
            addingOrder: false,
            globalError: '',
            globalSuccess: '',
            failedValidation: {},
            title: __('Add'),
        };
    },
    methods: {
        addOrder: function (e) {
            var that = this;
            this.addingOrder = true;
            e.preventDefault();
            let formData = new FormData(this.$refs.addOrder);
            axios
                .post(this.adminPanelAddOrderUrl, formData)
                .then(function (response) {
                    that.getOrders();
                    that.$refs.closeModal.click();
                    that.failedValidation = {};
                })
                .catch(function (error) {
                    if (_.has(error, 'response.data.failedValidation')) {
                        that.failedValidation =
                            error.response.data.failedValidation;
                    } else {
                        that.globalError = error.message;
                    }
                })
                .then(() => {
                    that.addingOrder = false;
                });
        },
    },
    updated() {
        console.debug('updated addOrder');
    },
    created() {},
    mounted() {},
};
</script>
