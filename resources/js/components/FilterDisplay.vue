<template>
    <div>
        <div v-if='opInfo'>{{ __('Order priority') + ': ' }} <b>{{ filter.order_priority }}</b></div>
        <div class="fs-5"><b>{{ filter.name }}</b></div>
        <div class="form-check" v-for='option in filter.filter_options' :key='option.id'>
            <input :checked='filterOptions && filterOptions[option.id]' class="form-check-input" type="checkbox"
                :id="'fo-' + option.id" name='filterOptions[]' :value="option.id">
            <label class="form-check-label" :for="'fo-' + option.id">
                {{ option.name }}
            </label>
        </div>
    </div>
</template>

<script>

export default {
    props: ['filter', 'opInfo', 'filterOptions'],
    data() {
        return {}
    },
    methods: {
    },
    mounted() {
    }
}
</script>
