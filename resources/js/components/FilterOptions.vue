<template>
    <div>
        <div>
            <div class='fs-4'>
                <button type="button" @click='addOption' class="btn btn-success btn-sm mb-1">
                    {{ __('Add option') }}</button>
            </div>
            <div>
                <div v-for="(filterOption, index) in filterOptions" :key="filterOption.uniqueNumber || filterOption.id">
                    <input type="hidden" name='filterOptionIds[]' :value='filterOption.id'>
                    <input type="hidden" name='filterOptionRemoves[]' :value='filterOption.remove'>
                    <div class="input-group mb-1" :class="{ 'border border-danger': filterOption.remove }">
                        <span class="input-group-text">{{ __('Name') }}:</span>
                        <input name='filterOptionNames[]' v-model='filterOption.name' class="form-control"
                            placeholder="Name">
                        <span class="input-group-text">{{ __('Order priority') }}:</span>
                        <input @blur='sortOptions()' name='filterOptionOrderPriorities[]'
                            v-model='filterOption.order_priority' class="form-control"
                            :placeholder="__('Order priority')">
                        <button @click='removeOption(index, filterOption.id)' type="button"
                            class="btn btn-danger input-group-text">X</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { generateUniqueNumber } from './commonFunctions.js'

export default {
    props: ['filterOptions'],
    data() {
        return {}
    },
    methods: {
        generateUniqueNumber,
        addOption: function () {
            this.filterOptions.push({
                id: '',
                name: '',
                order_priority: '',
                uniqueNumber: this.generateUniqueNumber()
            });
        },
        removeOption: function (index, id) {
            if (id) {
                this.filterOptions[index].remove = !this.filterOptions[index].remove;
            } else {
                this.filterOptions.splice(index, 1);
            }
        },
        sortOptions: function () {
            this.filterOptions.sort((a, b) => parseInt(a.order_priority) - parseInt(b.order_priority));
        }
    },
    mounted() {
    }
}
</script>
