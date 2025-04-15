<template>
    <div
        class="form-floating me-2 float-start"
        style="max-width: 345px; min-width: 200px"
    >
        <select
            v-model="defaultAddressId"
            class="form-select"
            @change="$emit('default-address-id-change', defaultAddressId)"
        >
            <option v-for="addr in addresses" :key="addr.id" :value="addr.id">
                {{ createOptName(addr) }}
            </option>
        </select>
        <label for="area_code_id">{{ __(label) }}</label>
    </div>
</template>

<script>
export default {
    props: ['addresses', 'initDefaultAddressId', 'label', 'setAddressId'],
    watch: {
        setAddressId(newVal) {
            if (newVal) {
                this.defaultAddressId = newVal;
            }
        },
    },
    data() {
        return {
            defaultAddressId: this.initDefaultAddressId,
        };
    },
    methods: {
        createOptName: function (addr) {
            var optName =
                addr.name +
                ' ' +
                addr.surname +
                ' ' +
                addr.street +
                ' ' +
                addr.house_number +
                ' ' +
                addr.company_name;
            if (!optName.trim()) {
                optName = __('New address');
            }
            return optName;
        },
    },
    mounted() {},
};
</script>
