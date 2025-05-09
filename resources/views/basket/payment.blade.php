@extends('layouts.basket')

@section('content')
    <div id="basket" class="page-wrapper container-fluid">
        <basket-payment :addresses='{{ $addresses }}' :products-in-basket='productsInBasket' :delivery-method='{{ $deliveryMethod }}'
            :summary='{{ $summary }}' :products-in-basket-data='{{ $productsInBasketData }}'
            przelewy24-transaction-register-url="{{ route('przelewy24-transaction-register') }}"></basket-payment>
    </div>
@endsection
