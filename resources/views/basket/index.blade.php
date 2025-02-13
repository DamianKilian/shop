@extends('layouts.basket')

@section('content')
    <div id="basket" class="page-wrapper">
        <basket-index v-if='Object.keys(productsInBasket).length' :products-in-basket='productsInBasket'
            get-products-in-basket-data-url="{{ route('get-products-in-basket-data') }}"></basket-index>
    </div>
@endsection
