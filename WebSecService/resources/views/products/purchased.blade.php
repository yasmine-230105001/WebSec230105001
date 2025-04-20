@extends('layouts.master')
@section('title', 'Purchased Products')
@section('content')
<div class="row mt-3">
    <div class="col">
        <h1>Your Purchased Products</h1>
        @if($purchasedProducts->isEmpty())
            <p>You have not purchased any products yet.</p>
        @else
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Purchased On</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($purchasedProducts as $product)
                    <tr>
                        <td>{{ $product->name }}</td>
                        <td>{{ $product->price }}</td>
                        <td>{{ $product->pivot->quantity ?? '1' }}</td>
                        <td>{{ $product->pivot->created_at }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
        <a href="{{ route('products_list') }}" class="btn btn-primary">Back to Products</a>
    </div>
</div>
@endsection