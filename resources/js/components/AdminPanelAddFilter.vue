<template>
    <div class="modal fade" id="addFilter" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="modalLabel">{{ title }}</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form @submit='addFilter' ref='addFilter' method="post" class='position-relative'>
                    <div class="modal-body">
                        <div class="clearfix">
                            <div class="form-floating mb-3 float-start" style="width: 49%;">
                                <input v-model='filter.name' name='name' ref='name'
                                    :class='{ "is-invalid": failedValidation.name }' class="form-control" id="title"
                                    :placeholder="__('Name')">
                                <label for="name">{{ __('Name') }}</label>
                                <div class="invalid-feedback">
                                    {{ failedValidation.name ? failedValidation.name[0] : '' }}
                                </div>
                            </div>
                            <div class="input-group mb-3 float-start" style="width: 49%; margin-left: 2%;">
                                <div class="form-floating">
                                    <input v-model='filter.order_priority' name='order_priority'
                                        :class='{ "is-invalid": failedValidation.order_priority }' class="form-control"
                                        :placeholder="__('Order priority')">
                                    <label for="order_priority">{{ __('Order priority') }}</label>
                                    <div class="invalid-feedback">
                                        {{ failedValidation.order_priority ? failedValidation.order_priority[0] : '' }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button ref='closeModal' type='button' class="btn btn-secondary" data-bs-dismiss="modal">{{
                            __('Close') }}</button>
                        <button type="submit" class="btn btn-success"><i class="fa-solid fa-plus"></i>
                            {{ title }}
                        </button>
                        <div v-if="globalError" class="text-bg-danger float-end mt-1">{{ globalError }}</div>
                        <div v-if="globalSuccess" class="text-bg-success float-end mt-1">{{ globalSuccess }}</div>
                    </div>
                    <loading-overlay v-if='addingFilter' />
                </form>
            </div>
        </div>
    </div>
</template>

<script>

export default {
    props: ['editFilter', 'adminPanelAddFilterUrl', 'getFilters'],
    data() {
        return {
            filter: {
                name: '',
                order_priority: ''
            },
            addingFilter: false,
            globalError: '',
            globalSuccess: '',
            failedValidation: {},
            title: __('Add filter'),
        }
    },
    methods: {
        addFilter: function (e) {
            var that = this;
            this.addingFilter = true;
            e.preventDefault();
            let formData = new FormData(this.$refs.addFilter);
            if (this.editFilter) {
                formData.append("filterId", this.editFilter.filter.id);
            }
            axios.post(this.adminPanelAddFilterUrl, formData)
                .then(function (response) {
                    that.globalSuccess = `"${that.$refs.name.value}" ${__('saved!')}`;
                    that.getFilters();
                    that.$refs.closeModal.click();
                })
                .catch(function (error) {
                    if (_.has(error, 'response.data.failedValidation')) {
                        that.failedValidation = error.response.data.failedValidation;
                    } else {
                        that.globalError = error.message;
                    }
                }).then(() => {
                    this.addingFilter = false;
                });
        }
    },
    updated() {
        console.debug('updated-AdminPanelAddFilter');
    },
    created() { },
    mounted() {
    }
}
</script>
