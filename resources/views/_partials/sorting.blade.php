<div id="sorting">
    <select @change='sortProducts' class="form-select" aria-label="Default select example">
        <option value="" selected>{{ __('Sorting') }}</option>
        <option value="price_asc">{{ __('Price') }}: {{ __('from the lowest') }}</option>
        <option value="price_desc">{{ __('Price') }}: {{ __('from the highest') }}</option>
    </select>
</div>
