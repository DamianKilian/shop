@extends('layouts.basket')

@section('content')
    <div id="basket" class="page-wrapper container-fluid">
        <basket-payment :products-in-basket='productsInBasket' :delivery-method='{{ $deliveryMethod }}'
            :summary='{{ $summary }}' :products-in-basket-data='{{ $productsInBasketData }}'></basket-payment>
    </div>
@endsection
