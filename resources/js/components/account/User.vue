<template>
    <div id="account-user" class="ms-3">
        <h3>{{ __('User') }}</h3>
    </div>
    <form
        @submit="updateUser"
        ref="updateUser"
        method="post"
        class="position-relative"
    >
        <div class="row g-2 mb-2">
            <div class="col-lg-6">
                <div class="form-floating float-start w-100">
                    <input
                        v-model="email"
                        name="email"
                        :class="{
                            'is-invalid': failedValidation.email,
                        }"
                        class="form-control"
                        :placeholder="__('Email')"
                    />
                    <label for="email">{{ __('Email') }} *</label>
                    <div class="invalid-feedback">
                        {{
                            failedValidation.email
                                ? failedValidation.email[0]
                                : ''
                        }}
                    </div>
                </div>
            </div>
        </div>
        <div class="row g-2 mb-2">
            <div class="col-lg-6">
                <div class="form-floating float-start w-100">
                    <input
                        v-model="name"
                        name="name"
                        :class="{
                            'is-invalid': failedValidation.name,
                        }"
                        class="form-control"
                        :placeholder="__('Name')"
                    />
                    <label for="name">{{ __('Name') }} *</label>
                    <div class="invalid-feedback">
                        {{
                            failedValidation.name
                                ? failedValidation.name[0]
                                : ''
                        }}
                    </div>
                </div>
            </div>
        </div>
        <div>
            <button type="submit" class="btn btn-success">
                <i class="fa-solid fa-plus"></i>
                {{ __('Save') }}
            </button>
            <div v-if="globalError" class="text-bg-danger mt-1">
                {{ globalError }}
            </div>
            <div v-if="globalSuccess" class="text-bg-success mt-1">
                {{ globalSuccess }}
            </div>
        </div>
        <loading-overlay v-if="updatingUser" />
    </form>
</template>

<script>
export default {
    props: ['updateUserUrl', 'userName', 'userEmail'],
    data() {
        return {
            email: '',
            name: '',
            failedValidation: {},
            globalError: '',
            globalSuccess: '',
            updatingUser: false,
        };
    },
    methods: {
        updateUser: function (e) {
            var that = this;
            this.updatingUser = true;
            e.preventDefault();
            let formData = new FormData(this.$refs.updateUser);
            axios
                .post(this.updateUserUrl, formData)
                .then(function (response) {
                    that.globalSuccess = `"${that.$refs.name.value}" ${__(
                        'saved!'
                    )}`;
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
                    this.updatingUser = false;
                });
        },
        init: function () {
            this.email = this.userEmail;
            this.name = this.userName;
        },
    },
    mounted() {
        this.init();
    },
};
</script>
