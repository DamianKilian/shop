<template>
    <div
        class="modal fade"
        id="addDeliveryMethod"
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
                    @submit="addDeliveryMethod"
                    ref="addDeliveryMethod"
                    method="post"
                    class="position-relative"
                >
                    <div class="modal-body">
                        <div class="clearfix">
                            <input
                                type="hidden"
                                name="deliveryMethodId"
                                v-model="deliveryMethod.id"
                            />
                            <div class="row g-2 mb-2">
                                <div class="col-sm-4">
                                    <div
                                        class="form-floating float-start w-100"
                                    >
                                        <input
                                            v-model="deliveryMethod.name"
                                            name="name"
                                            ref="name"
                                            :class="{
                                                'is-invalid':
                                                    failedValidation.name,
                                            }"
                                            class="form-control"
                                            :placeholder="__('Name')"
                                        />
                                        <label for="name"
                                            >{{ __('Name') }} *</label
                                        >
                                        <div class="invalid-feedback">
                                            {{
                                                failedValidation.name
                                                    ? failedValidation.name[0]
                                                    : ''
                                            }}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div
                                        class="form-floating float-start w-100"
                                    >
                                        <input
                                            v-model="deliveryMethod.price"
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
                                <div class="col-sm-4">
                                    <div class="form-check">
                                        <input
                                            v-model="deliveryMethod.active"
                                            name="active"
                                            value='true'
                                            class="form-check-input"
                                            type="checkbox"
                                            id="active"
                                        />
                                        <label
                                            class="form-check-label"
                                            for="active"
                                        >
                                            {{ __('Active') }}
                                        </label>
                                    </div>
                                </div>
                                <div>
                                    <label
                                        for="description"
                                        class="form-label"
                                        >{{ __('Description') }}</label
                                    >
                                    <textarea
                                        v-model="deliveryMethod.description"
                                        name="description"
                                        class="form-control"
                                        id="description"
                                        rows="3"
                                    ></textarea>
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
                    <loading-overlay v-if="addingDeliveryMethod" />
                </form>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: ['addDeliveryMethodUrl', 'getDeliveryMethods', 'deliveryMethod'],
    data() {
        return {
            addingDeliveryMethod: false,
            globalError: '',
            globalSuccess: '',
            failedValidation: {},
            title: __('Add'),
        };
    },
    methods: {
        addDeliveryMethod: function (e) {
            var that = this;
            this.addingDeliveryMethod = true;
            e.preventDefault();
            let formData = new FormData(this.$refs.addDeliveryMethod);
            axios
                .post(this.addDeliveryMethodUrl, formData)
                .then(function (response) {
                    that.getDeliveryMethods();
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
                    that.addingDeliveryMethod = false;
                });
        },
    },
    updated() {
        console.debug('updated addDeliveryMethod');
    },
    created() {},
    mounted() {},
};
</script>
