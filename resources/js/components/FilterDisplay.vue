<template>
    <div>
        <div v-if='opInfo'>{{ __('Order priority') + ': ' }} <b>{{ filter.order_priority }}</b></div>
        <div class="fs-5"><b>{{ filter.name }}</b></div>
        <div class="form-check" v-for='option in filter.filter_options' :key='option.id'>
            <input class="form-check-input" type="checkbox" value="" :id="'fo-' + option.id">
            <label class="form-check-label" :for="'fo-' + option.id">
                {{ option.name }}
            </label>
        </div>
    </div>
</template>

<script>

export default {
    props: ['filter', 'opInfo'],
    data() {
        return {}
    },
    methods: {
    },
    mounted() {
    }
}
</script>
