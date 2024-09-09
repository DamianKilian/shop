<template>
    <div class='clearfix'>
        <div v-if='opInfo'>{{ __('Order priority') + ': ' }} <b>{{ filter.order_priority }}</b></div>
        <div class="fs-5"><b>{{ filter.name }}</b></div>
        <div class="form-check" v-for='option in filter.filter_options' :key='option.id'>
            <input class="form-check-input" type="checkbox" :id="'fo-' + option.id" name='filterOptions[]'
                :value="option.id" v-model='checkedOptions' @change='optionToggle(option)'>
            <label class="form-check-label" :for="'fo-' + option.id">
                {{ option.name }}
            </label>
        </div>
        <button v-if='filterChanged' @click="applyFilters()" type="button"
            class="btn btn-warning float-end apply-filters-btn">{{ __('Apply filters') }}</button>
    </div>
</template>

<script>

export default {
    props: [
        'filter',
        'filterOptionsStart',
        'opInfo',
        'queryStrParams',
        'queryStrParamsInitialVals',
        'applyFilters',
        'checkedOptionsGlobal'
    ],
    data() {
        return {
            filterChanged: false,
            checkedOptions: [],
        }
    },
    watch: {
        queryStrParamsInitialVals: function (newVal, oldVal) {
            this.filterChanged = false;
            if(newVal.filterOptions !== oldVal.filterOptions){
                this.getFilterOptionsUpdate();
            }
        },
    },
    computed: {
        filterOptionsInitial() {
            var arr = this.queryStrParamsInitialVals.filterOptions ? this.queryStrParamsInitialVals.filterOptions.split('|') : [];
            var filterOptionsInitial = {};
            _.forEach(arr, function (id) {
                filterOptionsInitial[id] = id;
            });
            return filterOptionsInitial;
        },
    },
    methods: {
        optionToggle: function (option) {
            if (this.applyFilters) {
                this.addOptionToQueryStr(option);
                this.isFilterChanged();
            }
        },
        isFilterChanged: function () {
            var that = this;
            this.filterChanged = false;
            _.forEach(this.filter.filter_options, function (option) {
                if (that.checkedOptions.includes(option.id) !== !!that.filterOptionsInitial[option.id]) {
                    that.filterChanged = true;
                    return false;
                }
            });
        },
        addOptionToQueryStr: function (option) {
            var that = this;
            _.forEach(this.checkedOptionsGlobal, function (optionGlobalId, index) {
                if (option.id == optionGlobalId) {
                    that.checkedOptionsGlobal.splice(index, 1);
                }
            });
            if (this.checkedOptions.includes(option.id)) {
                this.checkedOptionsGlobal.push(option.id);
            }
        },
        getFilterOptionsUpdate: function () {
            var that = this;
            _.forEach(this.filter.filter_options, function (option) {
                if (that.checkedOptionsGlobal.includes(option.id)) {
                    if (!that.checkedOptions.includes(option.id)) {
                        that.checkedOptions.push(option.id);
                    }
                } else if (that.checkedOptions.includes(option.id)) {
                    var i = that.checkedOptions.indexOf(option.id);
                    that.checkedOptions.splice(i, 1);
                }
            });
        },
    },
    updated() {
    },
    mounted() {
        if (this.filterOptionsStart) {
            this.checkedOptions = this.filterOptionsStart;
        } else {
            this.getFilterOptionsUpdate();
        }
    }
}
</script>
