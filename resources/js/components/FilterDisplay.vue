<template>
    <div class='clearfix'>
        <div v-if='opInfo'>{{ __('Order priority') + ': ' }} <b>{{ filter.order_priority }}</b></div>
        <div class="fs-5"><b>{{ filter.name }}</b></div>
        <div class="form-check" v-for='option in filter.filter_options' :key='option.id'>
            <input class="form-check-input" type="checkbox" :id="'fo-' + option.id" name='filterOptions[]'
                :value="option.id" v-model='checkedOptions'>
            <label class="form-check-label" :for="'fo-' + option.id">
                {{ option.name }}
            </label>
        </div>
    </div>
</template>

<script>

export default {
    props: [
        'filter',
        'filterOptionsStart',
        'opInfo',
    ],
    data() {
        return {
            checkedOptions: [],
        }
    },
    watch: {
        checkedOptions(newVal) {
            this.$emit('checkedOptionsChange', {
                filterId: this.filter.id,
                checkedOptions: newVal,
            });
        },
    },
    methods: {},
    updated() {
    },
    mounted() {
        if(this.filterOptionsStart){
            this.checkedOptions = this.filterOptionsStart;
        }
    }
}
</script>
