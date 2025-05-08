<template>
    <div class="form-floating float-start w-100">
        <input
            @focus="!readonly && onInputFocus()"
            @blur="!readonly && onInputBlur()"
            @input="!readonly && onInputInput()"
            ref="country"
            :class="{
                'is-invalid': failedValidation.country,
                'form-control-plaintext': readonly,
            }"
            class="form-control"
            :placeholder="__('Country')"
            :readonly="readonly"
        />
        <input
            v-if="!readonly"
            name="country_id"
            v-model="address.country_id"
            type="hidden"
        />
        <label for="country">{{ __('Country') }} *</label>
        <div class="invalid-feedback">
            {{ failedValidation.country ? failedValidation.country[0] : '' }}
        </div>
        <div
            @mouseleave="hoverOptions = false"
            @mouseover="hoverOptions = true"
            v-show="showOptions"
            class="country-options border w-100"
            :style="{
                height: countryOptionsHeight + 'px',
                maxHeight: maxCountryOptionsHeight + 'px',
                bottom: bottom + 'px',
            }"
        >
            <template v-for="country in countries" :key="country.id">
                <div
                    v-show="country.show"
                    @click="optionSelect(country.id)"
                    class="c-opt"
                >
                    {{ country.name }}
                </div>
            </template>
        </div>
    </div>
</template>

<script>
export default {
    props: ['countries', 'failedValidation', 'address', 'readonly'],
    data() {
        return {
            countryOptionsHeight: 200,
            maxCountryOptionsHeight: 200,
            bottom: null,
            showOptions: false,
            hoverOptions: false,
        };
    },
    watch: {
        'address.country_id': function () {
            this.setCountryValueById();
        },
    },
    methods: {
        setCountryValueById: function () {
            this.$refs.country.value =
                this.countries[this.address.country_id].name;
        },
        optionSelect: function (countryId) {
            if (this.address.country_id !== countryId) {
                this.address.country_id = countryId;
            } else {
                this.setCountryValueById();
            }
            this.resetInput();
        },
        onInputInput: function () {
            if ('' === this.$refs.country.value) {
                _.forEach(this.countries, function (country) {
                    country.show = true;
                });
                return;
            }
            var re = new RegExp(this.$refs.country.value, 'i');
            _.forEach(this.countries, function (country) {
                if (-1 === country.name.search(re)) {
                    country.show = false;
                } else {
                    country.show = true;
                }
            });
        },
        onInputBlur: function () {
            if (!this.hoverOptions) {
                this.resetInput();
                this.setCountryValueById();
            }
        },
        resetInput: function () {
            this.bottom = null;
            this.maxCountryOptionsHeight = this.countryOptionsHeight;
            this.showOptions = false;
        },
        onInputFocus: function () {
            this.$refs.country.value = '';
            this.showOptions = true;
            var bcr = this.$refs.country.getBoundingClientRect();
            var elHeight = bcr.height;
            var distanceToTop = bcr.top;
            var distanceToBottom = window.innerHeight - bcr.bottom;
            if (this.countryOptionsHeight > distanceToBottom) {
                if (distanceToTop > distanceToBottom) {
                    this.maxCountryOptionsHeight = distanceToTop;
                    this.bottom = elHeight;
                } else {
                    this.maxCountryOptionsHeight = distanceToBottom;
                }
            }
        },
    },
    mounted() {
        this.setCountryValueById();
        _.forEach(this.countries, function (country) {
            country.show = true;
        });
    },
};
</script>
