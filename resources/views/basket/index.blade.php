@extends('layouts.basket')

@section('content')
    @php
        $authenticated = Auth::check();
    @endphp
    <div id="basket" class="page-wrapper container-fluid">
        <basket-index v-if='basketReady' @remove-from-basket-all="removeFromBasketAll"
            :errors="{{ $errors->getMessages() ? json_encode($errors->getMessages()) : '{}' }}"
            :set-products-in-local-storage='setProductsInLocalStorage' :products-in-basket='productsInBasket'
            :delivery-methods='{{ $deliveryMethods }}' get-basket-summary-url="{{ route('get-basket-summary') }}"
            order-store-url="{{ route('order-store') }}"
            get-products-in-basket-data-url="{{ route('get-products-in-basket-data') }}"
            get-area-codes-url="{{ route('get-area-codes') }}" get-addresses-url="{{ route('get-addresses') }}"
            :authenticated="{{ $authenticated }}"
            :init-default-address-id="{{ $authenticated ? auth()->user()->default_address_id : null }}"
            :init-default-address-invoice-id="{{ $authenticated ? auth()->user()->default_address_invoice_id : null }}"></basket-index>
    </div>
@endsection
