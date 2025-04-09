@extends('layouts.master')
@section('title', 'Purchased Products')
@section('content')

<h1>My Purchased Products</h1>

@forelse($products as $product)
    <div class="card mt-2">
        <div class="card-body">
            <h4>{{ $product->name }}</h4>
            <p>Price: ${{ number_format($product->price, 2) }}</p>
            <p>Description: {{ $product->description }}</p>
        </div>
    </div>
@empty
    <p>You have not purchased any products yet.</p>
@endforelse

@endsection
