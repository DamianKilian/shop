<template>
    <div class="row">
        <div id="productsInBasket" class="col-md-8">
            <table class="table table-sm table-striped table-hover fs-5">
                <thead>
                    <tr>
                        <th colspan="2">
                            <span class="fs-2">{{ __('Products') }}</span>
                        </th>
                    </tr>
                </thead>
                <tbody class="table-group-divider">
                    <tr
                        v-for="(product, id, index) in productsInBasket"
                        :key="id"
                    >
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
                        </td>
                    </tr>
                </tbody>
            </table>
            <div id="deliveryMethods" class="mb-4">
                <h2><b>Delivery method:</b></h2>
                <div class="list-group">
                    <AdminPanelDeliveryMethodContent
                        :deliveryMethod="deliveryMethod"
                        :active="true"
                    />
                </div>
            </div>
            <div id="addresses" class="mb-4">
                <h2><b>Addresses</b></h2>
                <h4>{{ __('Delivery address') }}</h4>
                <AddAddressFormContent
                    :countries="{
                        [addresses.address.country.id]:
                            addresses.address.country,
                    }"
                    :failedValidation="{}"
                    :address="addresses.address"
                    :areaCodes="[addresses.address.area_code]"
                    :readonly="true"
                />
                <h4>{{ __('Invoice address') }}</h4>
                <div
                    v-if="addressInvoiceTheSame"
                    class="form-check form-switch p-0"
                >
                    <label class="form-check-label">{{
                        __('Invoice address same as delivery address')
                    }}</label>
                </div>
                <div v-else>
                    <AddAddressFormContent
                        :countries="{
                            [addresses.addressInvoice.country.id]:
                                addresses.addressInvoice.country,
                        }"
                        :failedValidation="{}"
                        :address="addresses.addressInvoice"
                        :areaCodes="[addresses.addressInvoice.area_code]"
                        :readonly="true"
                    />
                </div>
            </div>
        </div>
        <div class="col-md-4 bg-light position-relative">
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
                        <span>({{ deliveryMethod.name }})</span>
                        <b class="float-end">{{ summary.deliveryPrice }}</b>
                    </div>
                    <hr />
                    <div class="fs-4">
                        {{ __('Total price') }}
                        <b class="float-end">{{ summary.totalPrice }}</b>
                    </div>
                </div>
                <form :action="paymentPayUrl" method="post">
                    <input type="hidden" name="_token" :value="csrfToken" />
                    <button
                        :disabled="!regulationAccept"
                        type="submit"
                        class="btn btn-primary btn-lg w-100 mt-2"
                    >
                        {{ __('Pay') }}
                    </button>
                    <div class="form-check py-2">
                        <input
                            class="form-check-input"
                            type="checkbox"
                            name="regulationAccept"
                            v-model="regulationAccept"
                        />
                        <label class="form-check-label">
                            <b>
                                „Oświadczam, że zapoznałem się z
                                <a href="https://www.przelewy24.pl/regulamin"
                                    >regulaminem</a
                                >
                                i
                                <a
                                    href="https://www.przelewy24.pl/obowiazek-informacyjny-platnik"
                                    >obowiązkiem informacyjnym</a
                                >
                                serwisu Przelewy24”</b
                            >
                        </label>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
import AddAddressFormContent from '../account/AddAddressFormContent.vue';
import AdminPanelDeliveryMethodContent from '../AdminPanelDeliveryMethodContent.vue';

export default {
    components: { AddAddressFormContent, AdminPanelDeliveryMethodContent },
    props: [
        'addresses',
        'productsInBasketData',
        'productsInBasket',
        'summary',
        'deliveryMethod',
        'paymentPayUrl',
    ],
    data() {
        return {
            regulationAccept: false,
            csrfToken: document.querySelector("meta[name='csrf-token']")
                .content,
        };
    },
    computed: {
        addressInvoiceTheSame() {
            return _.isUndefined(this.addresses.addressInvoice);
        },
    },
    methods: {},
    mounted() {},
};
</script>
