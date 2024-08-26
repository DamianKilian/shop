<div :class="{ 'd-block': !this.$refs.homePage }" v-if="!this.$refs.homePage" id="menu-filters">
    <div class="fs-3 fw-bolder" v-html="__('Filters')"></div>
    <div class="price-filter">
        <div class="label-container">
            <label for="price-filter-min" class="form-label">{{ __('Min. price') }}</label>
            <input class="form-control p-0" :value="queryStrParams.minPrice"
                @input="queryStrParams.minPrice = parseInt($event.target.value)">
        </div>
        <input type="range" class="form-range" id="price-filter-min" min="0" :max="maxProductsPriceCeil"
            step="0.01" :value="queryStrParams.minPrice"
            @input="_.debounce(()=>{queryStrParams.minPrice = parseInt($event.target.value)}, 300)()">
        <div class="label-container">
            <label for="price-filter-max" class="form-label">{{ __('Max. price') }}</label>
            <input class="form-control p-0" :value="queryStrParams.maxPrice"
                @input="queryStrParams.maxPrice = parseInt($event.target.value)">
        </div>
        <input type="range" class="form-range" :class='{ "is-invalid": failedValidation.maxPrice }'
            id="price-filter-max" min="0" :max="maxProductsPriceCeil" step="0.01"
            :value="queryStrParams.maxPrice"
            @input="_.debounce(()=>{queryStrParams.maxPrice = parseInt($event.target.value)}, 300)()">
        <div class="invalid-feedback m-0 mb-2"
            v-html="failedValidation.maxPrice ? __(failedValidation.maxPrice[0]) : ''"></div>
        <button :class="{ 'd-block': isFilterChanged('price') }" @click="applyFilters()" type="button"
            class="btn btn-warning float-end apply-filters-btn">{{ __('Apply filters') }}</button>
    </div>
    @foreach ($filters as $filter)
        <div class="mb-2">
            <filter-display :filter='@json($filter)'></filter-display>
        </div>
    @endforeach
</div>
