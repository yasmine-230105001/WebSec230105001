@extends('layouts.master')
@section('title', 'Insufficient Credit')
@section('content')
<div class="row mt-3">
    <div class="col">
        <h1>Insufficient Credit</h1>
        <div class="alert alert-danger">
            <p>You do not have enough credit to purchase <strong>{{ $product->name }}</strong>.</p>
            <p>Price: {{ $product->price }}</p>
            <p>Your Credit: {{ auth()->user()->credit }}</p>
        </div>
        <a href="{{ route('products_list') }}" class="btn btn-primary">Back to Products</a>
    </div>
</div>
@endsection