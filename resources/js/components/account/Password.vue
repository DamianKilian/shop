<template>
    <div id="account-user" class="ms-3">
        <h3>{{ __('Change Password') }}</h3>
    </div>
    <form
        @submit="updatePassword"
        ref="updatePassword"
        method="post"
        class="position-relative"
    >
        <div class="row g-2 mb-2">
            <div class="col-lg-6">
                <div class="form-floating float-start w-100">
                    <input
                        name="current_password"
                        :class="{
                            'is-invalid': failedValidation.current_password,
                        }"
                        class="form-control"
                        :placeholder="__('Current password')"
                        :type="typePassword ? 'password' : 'text'"
                    />
                    <label for="current_password">{{ __('Current password') }} *</label>
                    <div class="invalid-feedback">
                        {{
                            failedValidation.current_password
                                ? failedValidation.current_password[0]
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
                        name="password"
                        :class="{
                            'is-invalid': failedValidation.password,
                        }"
                        class="form-control"
                        :placeholder="__('Password')"
                        :type="typePassword ? 'password' : 'text'"
                    />
                    <label for="password">{{ __('Password') }} *</label>
                    <div class="invalid-feedback">
                        {{
                            failedValidation.password
                                ? failedValidation.password[0]
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
                        name="password_confirmation"
                        class="form-control"
                        :placeholder="__('Confirm Password')"
                        :type="typePassword ? 'password' : 'text'"
                    />
                    <label for="password_confirmation"
                        >{{ __('Confirm Password') }} *</label
                    >
                </div>
            </div>
        </div>
        <div>
            <button type="submit" class="btn btn-success">
                <i class="fa-solid fa-plus"></i>
                {{ __('Save') }}
            </button>
            <button
                type="button"
                class="ms-1 btn-dark btn"
                @click="typePassword = !typePassword"
            >
                <i class="fa-solid fa-eye"></i>
            </button>
            <div v-if="globalError" class="text-bg-danger mt-1">
                {{ globalError }}
            </div>
            <div v-if="globalSuccess" class="text-bg-success mt-1">
                {{ globalSuccess }}
            </div>
        </div>
        <loading-overlay v-if="updatingPassword" />
    </form>
</template>

<script>
export default {
    props: ['updatePasswordUrl'],
    data() {
        return {
            typePassword: true,
            failedValidation: {},
            globalError: '',
            globalSuccess: '',
            updatingPassword: false,
        };
    },
    methods: {
        updatePassword: function (e) {
            var that = this;
            this.updatingPassword = true;
            e.preventDefault();
            let formData = new FormData(this.$refs.updatePassword);
            axios
                .post(this.updatePasswordUrl, formData)
                .then(function (response) {
                    that.globalSuccess = 'saved!';
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
                    this.updatingPassword = false;
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
