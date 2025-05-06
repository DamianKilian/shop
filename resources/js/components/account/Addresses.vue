<template>
    <div id="addresses">
        <div class="mt-3 actions-global clearfix">
            <button
                v-if="areaCodes.length"
                @click="clearAddress(address)"
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
            <div class="float-start">
                <AddressesSelect
                    @default-address-id-change="defaultAddressIdChange"
                    :setAddressId="newAddressId"
                    :addresses="addresses"
                    :initDefaultAddressId="initDefaultAddressId"
                    :label="__('Default address')"
                />
                <AddressesSelect
                    @default-address-id-change="
                        defaultAddressIdChange(
                            $event,
                            'defaultAddressInvoiceId'
                        )
                    "
                    :setAddressId="newAddressId"
                    :addresses="addresses"
                    :initDefaultAddressId="initDefaultAddressInvoiceId"
                    :label="__('Default invoice address')"
                />
            </div>
        </div>
        <AddAddress
            v-if="getAreaCodesDone"
            @address-created="(id) => (newAddressId = id)"
            :countries="countries"
            :getAreaCodesDone="getAreaCodesDone"
            :getAddresses="getAddresses"
            :addAddressUrl="addAddressUrl"
            :areaCodes="areaCodes"
            :address="address"
        />
        <div
            v-if="addresses.length"
            id="products-container"
            class="clearfix pt-3 addresses"
        >
            <div
                v-for="editAddress in addresses"
                :key="editAddress.id"
                :class="{ 'bg-primary bg-opacity-75': editAddress.selected }"
                class="product filter pt-1 pb-1"
            >
                <div class="card">
                    <div
                        @click="editAddress.selected = !editAddress.selected"
                        style="height: 30px"
                    >
                        <input
                            class="m-1 form-check-input position-absolute"
                            type="checkbox"
                            v-model="editAddress.selected"
                        />
                        <button
                            @click.stop="address = { ...editAddress }"
                            data-bs-toggle="modal"
                            data-bs-target="#addAddress"
                            class="btn btn-warning btn-sm edit-product"
                        >
                            <i class="fa-solid fa-pen-to-square"></i>
                            <span>{{ __('Edit') }}</span>
                        </button>
                    </div>
                    <div class="card-body">
                        <AddAddressFormContent
                            v-if="getAreaCodesDone"
                            :countries="countries"
                            :failedValidation="{}"
                            :address="editAddress"
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
import AddAddressFormContent from './AddAddressFormContent.vue';
import AddressesSelect from './AddressesSelect.vue';
import getAddresses from './getAddresses.js';

export default {
    mixins: [getAddresses],
    components: { AddAddress, AddAddressFormContent, AddressesSelect },
    props: [
        'addAddressUrl',
        'deleteAddressesUrl',
        'setDefaultAddressUrl',
        'initDefaultAddressId',
        'initDefaultAddressInvoiceId',
    ],
    data() {
        return {
            address: {},
            newAddressId: null,
        };
    },
    methods: {
        defaultAddressIdChange: function (id, type = 'defaultAddressId') {
            axios.post(this.setDefaultAddressUrl, {
                [type]: id,
            });
        },
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
    },
    created() {
        this.getAddressesAndData();
    },
    updated() {},
    mounted() {},
};
</script>
