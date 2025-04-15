<template>
    <div
        class="modal fade"
        id="addAddress"
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
                    @submit="addAddress"
                    ref="addAddress"
                    method="post"
                    class="position-relative"
                >
                    <div class="modal-body">
                        <AddAddressFormContent
                            :failedValidation="failedValidation"
                            :address="address"
                            :areaCodes="areaCodes"
                        />
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
                    <loading-overlay v-if="addingAddress" />
                </form>
            </div>
        </div>
    </div>
</template>

<script>
import AddAddressFormContent from './AddAddressFormContent.vue';

export default {
    components: { AddAddressFormContent },
    props: [
        'addAddressUrl',
        'getAddresses',
        'areaCodes',
        'address',
    ],
    data() {
        return {
            addingAddress: false,
            globalError: '',
            globalSuccess: '',
            failedValidation: {},
            title: __('Add address'),
        };
    },
    methods: {
        addAddress: function (e) {
            var that = this;
            this.addingAddress = true;
            e.preventDefault();
            let formData = new FormData(this.$refs.addAddress);
            axios
                .post(this.addAddressUrl, formData)
                .then(function (response) {
                    that.getAddresses();
                    if(response.data.newAddressId){
                        that.$emit('address-created', response.data.newAddressId);
                    }
                    that.$refs.closeModal.click();
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
                    that.addingAddress = false;
                });
        },
    },
    updated() {
        console.debug('updated addAddress');
    },
    created() {},
    mounted() {},
};
</script>
