<template>
    <div id="addresses">
        <div class="mt-3 actions-global clearfix">
            <button
                v-if="areaCodes.length"
                @click="editAddress = null"
                data-bs-toggle="modal"
                data-bs-target="#addAddress"
                class="btn btn-success float-end mt-1 mt-sm-0"
            >
                <i class="fa-solid fa-plus"></i> {{ __('Add address') }}
            </button>
            <button
                class="btn btn-danger float-end me-1 mt-1 mt-sm-0"
                @click="deleteAddresses"
            >
                <i class="fa-solid fa-trash"></i> {{ __('Remove') }}
            </button>
        </div>
        <AddAddress
            v-if="areaCodes.length"
            :getAddresses="getAddresses"
            :editAddress="editAddress"
            :addAddressUrl="addAddressUrl"
            :defaultAreaCode="defaultAreaCode"
            :areaCodes="areaCodes"
        />
        <div
            v-if="addresses.length"
            id="products-container"
            class="clearfix pt-3 addresses"
        >
            <div
                v-for="(address, index) in addresses"
                :key="address.id"
                :class="{ 'bg-primary bg-opacity-75': address.selected }"
                class="product filter pt-1 pb-1"
            >
                <div class="card">
                    <div
                        @click="address.selected = !address.selected"
                        style="height: 30px"
                    >
                        <input
                            class="m-1 form-check-input position-absolute"
                            type="checkbox"
                            v-model="address.selected"
                        />
                        <button
                            @click.stop="editAddress = address"
                            data-bs-toggle="modal"
                            data-bs-target="#addAddress"
                            class="btn btn-warning btn-sm edit-product"
                        >
                            <i class="fa-solid fa-pen-to-square"></i>
                            <span>{{ __('Edit') }}</span>
                        </button>
                    </div>
                    <div class="card-body">
                        <AddressDisplay
                            :address="address"
                            :areaCodes="areaCodes"
                            :readonly="true"
                        />
                    </div>
                </div>
            </div>
        </div>
        <div v-else class="alert alert-light mt-3 text-center" role="alert">
            {{ __('No addresses') }}
        </div>
    </div>
</template>

<script>
import AddAddress from './AddAddress.vue';
import AddressDisplay from './AddressDisplay.vue';

export default {
    components: { AddAddress, AddressDisplay },
    props: [
        'getAreaCodesUrl',
        'getAddressesUrl',
        'addAddressUrl',
        'deleteAddressesUrl',
    ],
    data() {
        return {
            editAddress: null,
            defaultAreaCode: null,
            areaCodes: [],
            addresses: [],
        };
    },
    methods: {
        deleteAddresses: function () {
            var that = this;
            axios
                .post(this.deleteAddressesUrl, {
                    addresses: this.getSelectedAddresses(),
                })
                .then(function () {
                    that.getAddresses();
                })
                .catch(function (error) {
                    console.log(error);
                    that.globalError = error.message;
                });
        },
        getSelectedAddresses: function () {
            var selectedAddresses = [];
            _.forEach(this.addresses, function (address, key) {
                if (address.selected) {
                    selectedAddresses.push(address);
                    return;
                }
            });
            return selectedAddresses;
        },
        getAddressesWithAreaCodes: function () {
            var that = this;
            axios
                .post(this.getAreaCodesUrl)
                .then(function (response) {
                    that.areaCodes = response.data.areaCodes;
                    that.defaultAreaCode = response.data.defaultAreaCode;
                    that.getAddresses();
                })
                .catch(function (error) {
                    that.globalError = error.message;
                    console.log(error.message);
                });
        },
        getAddresses: function () {
            this.addresses = [];
            var that = this;
            axios
                .post(this.getAddressesUrl)
                .then(function (response) {
                    that.addresses = response.data.addresses;
                    that.arrangeAddresses();
                })
                .catch(function (error) {
                    that.globalError = error.message;
                    console.log(error.message);
                });
        },
        arrangeAddresses: function () {
            var that = this;
            _.forEach(this.addresses, function (address) {
                address.selected = false;
            });
        },
    },
    created() {
        this.getAddressesWithAreaCodes();
    },
    updated() {},
    mounted() {},
};
</script>
