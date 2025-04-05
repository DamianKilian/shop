<template>
    <!-- <div class="clearfix">
        <div class="fs-5">

        </div>
    </div> -->
    <AddAddressFormContent
        :failedValidation="{}"
        :address="address"
        :areaCodes="areaCodes"
    />
</template>

<script>
import AddAddressFormContent from './AddAddressFormContent.vue';

export default {
    components: { AddAddressFormContent },
    props: ['address', 'areaCodes'],
    data() {
        return {
            checkedOptions: [],
        };
    },
    methods: {},
    updated() {},
    mounted() {},
};
</script>
