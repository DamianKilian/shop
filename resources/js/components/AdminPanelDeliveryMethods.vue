<template>
    <div id="delivery-methods">
        <div class="mt-3 actions-global clearfix">
            <button
                @click="clearDeliveryMethod(deliveryMethod)"
                data-bs-toggle="modal"
                data-bs-target="#addDeliveryMethod"
                class="btn btn-success float-end mt-1 mt-sm-0"
            >
                <i class="fa-solid fa-plus"></i> {{ __('Add') }}
            </button>
            <button
                class="btn btn-danger float-end me-1 mt-1 mt-sm-0"
                @click="deleteDeliveryMethods"
            >
                <i class="fa-solid fa-trash"></i> {{ __('Remove') }}
            </button>
        </div>
        <AdminPanelAddDeliveryMethod
            :getDeliveryMethods="getDeliveryMethods"
            :addDeliveryMethodUrl="addDeliveryMethodUrl"
            :areaCodes="areaCodes"
            :deliveryMethod="deliveryMethod"
        />
        <div
            v-if="Object.keys(deliveryMethods).length"
            id="products-container"
            class="clearfix pt-3"
        >
            <div
                v-for="editDeliveryMethod in deliveryMethods"
                :key="editDeliveryMethod.id"
                :class="{
                    'bg-primary bg-opacity-75': editDeliveryMethod.selected,
                }"
                class="product filter pt-1 pb-1"
                style="width: 100%; max-width: none; height: auto"
            >
                <div class="card" style="width: 100%">
                    <div
                        @click="
                            editDeliveryMethod.selected =
                                !editDeliveryMethod.selected
                        "
                        style="height: 30px"
                    >
                        <input
                            class="m-1 form-check-input position-absolute"
                            type="checkbox"
                            v-model="editDeliveryMethod.selected"
                        />
                        <button
                            @click.stop="
                                deliveryMethod = { ...editDeliveryMethod }
                            "
                            data-bs-toggle="modal"
                            data-bs-target="#addDeliveryMethod"
                            class="btn btn-warning btn-sm edit-product"
                        >
                            <i class="fa-solid fa-pen-to-square"></i>
                            <span>{{ __('Edit') }}</span>
                        </button>
                    </div>
                    <div class="card-body">
                        <AdminPanelDeliveryMethodContent
                            :deliveryMethod="editDeliveryMethod"
                        />
                    </div>
                </div>
            </div>
        </div>
        <div v-else class="alert alert-light mt-3 text-center" role="alert">
            {{ __('No delivery methods') }}
        </div>
    </div>
</template>

<script>
import AdminPanelAddDeliveryMethod from './AdminPanelAddDeliveryMethod.vue';
import AdminPanelDeliveryMethodContent from './AdminPanelDeliveryMethodContent.vue';

export default {
    components: {
        AdminPanelAddDeliveryMethod,
        AdminPanelDeliveryMethodContent,
    },
    props: [
        'addDeliveryMethodUrl',
        'deleteDeliveryMethodsUrl',
        'getDeliveryMethodsUrl',
    ],
    data() {
        return {
            deliveryMethod: {},
            deliveryMethods: {},
        };
    },
    methods: {
        clearDeliveryMethod: function (deliveryMethod) {
            deliveryMethod.id = null;
            deliveryMethod.name = '';
            deliveryMethod.description = '';
            deliveryMethod.active = true;
            deliveryMethod.price = null;
            return deliveryMethod;
        },
        getDeliveryMethods: function () {
            this.deliveryMethods = {};
            var that = this;
            axios
                .post(this.getDeliveryMethodsUrl)
                .then(function (response) {
                    that.deliveryMethods = response.data.deliveryMethods;
                    that.arrangeDeliveryMethods();
                })
                .catch(function (error) {
                    that.globalError = error.message;
                    console.log(error.message);
                });
        },
        arrangeDeliveryMethods: function () {
            var that = this;
            _.forEach(this.deliveryMethods, function (deliveryMethod) {
                deliveryMethod.selected = false;
            });
        },
        deleteDeliveryMethods: function () {
            var that = this;
            axios
                .post(this.deleteDeliveryMethodsUrl, {
                    deliveryMethods: this.getSelectedDeliveryMethods(),
                })
                .then(function () {
                    that.getDeliveryMethods();
                })
                .catch(function (error) {
                    console.log(error);
                    that.globalError = error.message;
                });
        },
        getSelectedDeliveryMethods: function () {
            var selectedDeliveryMethods = [];
            _.forEach(this.deliveryMethods, function (deliveryMethod, key) {
                if (deliveryMethod.selected) {
                    selectedDeliveryMethods.push(deliveryMethod);
                    return;
                }
            });
            return selectedDeliveryMethods;
        },
    },
    created() {
        this.getDeliveryMethods();
    },
    updated() {},
    mounted() {},
};
</script>
