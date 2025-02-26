@extends('layouts.basket')

@section('content')
    <div id="basket" class="page-wrapper container-fluid">
        <basket-index v-if='basketReady' @remove-from-basket-all="removeFromBasketAll" error = "{{ $errors->all()[0] ?? '' }}"
            :set-products-in-local-storage='setProductsInLocalStorage' :products-in-basket='productsInBasket'
            :delivery-methods='{{ $deliveryMethods }}' get-basket-summary-url="{{ route('get-basket-summary') }}"
            order-store-url="{{ route('order-store') }}"
            get-products-in-basket-data-url="{{ route('get-products-in-basket-data') }}"></basket-index>
    </div>
@endsection
