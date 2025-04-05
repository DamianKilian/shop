<template>
    <div class="clearfix">
        <input type="hidden" name="addressId" v-model="address.id" />
        <div class="row g-2 mb-2">
            <div class="col-lg-6">
                <div class="form-floating float-start w-100">
                    <input
                        v-model="address.email"
                        name="email"
                        ref="email"
                        :class="{
                            'is-invalid': failedValidation.email,
                            'form-control-plaintext': readonly,
                        }"
                        class="form-control"
                        :placeholder="__('Email')"
                        :readonly="readonly"
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
            <div class="col-4 col-lg-2">
                <div class="form-floating">
                    <select
                        v-model="address.area_code_id"
                        name="area_code_id"
                        ref="area_code_id"
                        :class="{
                            'is-invalid': failedValidation.area_code_id,
                            'form-control-plaintext': readonly,
                            'border-0': readonly,
                        }"
                        class="form-select w-100"
                        :readonly="readonly"
                    >
                        <option
                            v-for="areaCode in areaCodes"
                            :key="areaCode.id"
                            :value="areaCode.id"
                        >
                            + {{ areaCode.code }}
                        </option>
                    </select>
                    <label for="area_code_id">{{ __('Area code') }} *</label>
                    <div class="invalid-feedback">
                        {{
                            failedValidation.area_code_id
                                ? failedValidation.area_code_id[0]
                                : ''
                        }}
                    </div>
                </div>
            </div>
            <div class="col-8 col-lg-4">
                <div class="form-floating float-start w-100">
                    <input
                        v-model="address.phone"
                        name="phone"
                        ref="phone"
                        :class="{
                            'is-invalid': failedValidation.phone,
                            'form-control-plaintext': readonly,
                        }"
                        class="form-control"
                        :placeholder="__('Phone')"
                        :readonly="readonly"
                    />
                    <label for="phone">{{ __('Phone') }} *</label>
                    <div class="invalid-feedback">
                        {{
                            failedValidation.phone
                                ? failedValidation.phone[0]
                                : ''
                        }}
                    </div>
                </div>
            </div>
        </div>
        <div class="row g-2 mb-2">
            <div class="col-6">
                <div class="form-floating float-start w-100">
                    <input
                        v-model="address.name"
                        name="name"
                        ref="name"
                        :class="{
                            'is-invalid': failedValidation.name,
                            'form-control-plaintext': readonly,
                        }"
                        class="form-control"
                        :placeholder="__('Name')"
                        :readonly="readonly"
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
            <div class="col-6">
                <div class="form-floating float-start w-100">
                    <input
                        v-model="address.surname"
                        name="surname"
                        ref="surname"
                        :class="{
                            'is-invalid': failedValidation.surname,
                            'form-control-plaintext': readonly,
                        }"
                        class="form-control"
                        :placeholder="__('Surname')"
                        :readonly="readonly"
                    />
                    <label for="surname">{{ __('Surname') }} *</label>
                    <div class="invalid-feedback">
                        {{
                            failedValidation.surname
                                ? failedValidation.surname[0]
                                : ''
                        }}
                    </div>
                </div>
            </div>
        </div>
        <div class="row g-2 mb-2">
            <div class="col-sm-6">
                <div class="form-floating float-start w-100">
                    <input
                        v-model="address.nip"
                        name="nip"
                        ref="nip"
                        :class="{
                            'is-invalid': failedValidation.nip,
                            'form-control-plaintext': readonly,
                        }"
                        class="form-control"
                        placeholder="NIP"
                        :readonly="readonly"
                    />
                    <label for="nip">NIP</label>
                    <div class="invalid-feedback">
                        {{
                            failedValidation.nip ? failedValidation.nip[0] : ''
                        }}
                    </div>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-floating float-start w-100">
                    <input
                        v-model="address.company_name"
                        name="company_name"
                        ref="company_name"
                        :class="{
                            'is-invalid': failedValidation.company_name,
                            'form-control-plaintext': readonly,
                        }"
                        class="form-control"
                        :placeholder="__('Company name')"
                        :readonly="readonly"
                    />
                    <label for="company_name">{{ __('Company name') }}</label>
                    <div class="invalid-feedback">
                        {{
                            failedValidation.company_name
                                ? failedValidation.company_name[0]
                                : ''
                        }}
                    </div>
                </div>
            </div>
        </div>
        <div class="row g-2 mb-2">
            <div class="col-sm-6">
                <div class="form-floating float-start w-100">
                    <input
                        v-model="address.street"
                        name="street"
                        ref="street"
                        :class="{
                            'is-invalid': failedValidation.street,
                            'form-control-plaintext': readonly,
                        }"
                        class="form-control"
                        :placeholder="__('Street')"
                        :readonly="readonly"
                    />
                    <label for="nip">{{ __('Street') }} *</label>
                    <div class="invalid-feedback">
                        {{
                            failedValidation.street
                                ? failedValidation.street[0]
                                : ''
                        }}
                    </div>
                </div>
            </div>
            <div class="col-6 col-sm-3">
                <div class="form-floating float-start w-100">
                    <input
                        v-model="address.house_number"
                        name="house_number"
                        ref="house_number"
                        :class="{
                            'is-invalid': failedValidation.house_number,
                            'form-control-plaintext': readonly,
                        }"
                        class="form-control"
                        :placeholder="__('House number')"
                        :readonly="readonly"
                    />
                    <label for="house_number">{{ __('House number') }} *</label>
                    <div class="invalid-feedback">
                        {{
                            failedValidation.house_number
                                ? failedValidation.house_number[0]
                                : ''
                        }}
                    </div>
                </div>
            </div>
            <div class="col-6 col-sm-3">
                <div class="form-floating float-start w-100">
                    <input
                        v-model="address.apartment_number"
                        name="apartment_number"
                        ref="apartment_number"
                        :class="{
                            'is-invalid': failedValidation.apartment_number,
                            'form-control-plaintext': readonly,
                        }"
                        class="form-control"
                        :placeholder="__('Apartment number')"
                        :readonly="readonly"
                    />
                    <label for="apartment_number">{{
                        __('Apartment number')
                    }}</label>
                    <div class="invalid-feedback">
                        {{
                            failedValidation.apartment_number
                                ? failedValidation.apartment_number[0]
                                : ''
                        }}
                    </div>
                </div>
            </div>
        </div>
        <div class="row g-2 mb-2">
            <div class="col-6">
                <div class="form-floating float-start w-100">
                    <input
                        v-model="address.postal_code"
                        name="postal_code"
                        ref="postal_code"
                        :class="{
                            'is-invalid': failedValidation.postal_code,
                            'form-control-plaintext': readonly,
                        }"
                        class="form-control"
                        :placeholder="__('Postal code')"
                        :readonly="readonly"
                    />
                    <label for="postal_code">{{ __('Postal code') }} *</label>
                    <div class="invalid-feedback">
                        {{
                            failedValidation.postal_code
                                ? failedValidation.postal_code[0]
                                : ''
                        }}
                    </div>
                </div>
            </div>
            <div class="col-6">
                <div class="form-floating float-start w-100">
                    <input
                        v-model="address.city"
                        name="city"
                        ref="city"
                        :class="{
                            'is-invalid': failedValidation.city,
                            'form-control-plaintext': readonly,
                        }"
                        class="form-control"
                        :placeholder="__('City')"
                        :readonly="readonly"
                    />
                    <label for="city">{{ __('City') }} *</label>
                    <div class="invalid-feedback">
                        {{
                            failedValidation.city
                                ? failedValidation.city[0]
                                : ''
                        }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: ['failedValidation', 'address', 'areaCodes', 'readonly'],
    mounted() {},
};
</script>
