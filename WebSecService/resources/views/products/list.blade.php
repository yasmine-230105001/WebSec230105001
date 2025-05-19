@extends('layouts.master')
@section('title', 'Test Page')
@section('content')
<div class="row mt-2">
    <div class="col col-10">
        <h1>Products</h1>
    </div>
    <div class="col col-2">
        @can('add_products')
        <a href="{{route('products_edit')}}" class="btn btn-success form-control">Add Product</a>
        @endcan
    </div>
</div>

<!-- Display Success/Error Messages -->
@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif
@if($errors->any())
    <div class="alert alert-danger">
        {{ $errors->first() }}
    </div>
@endif

<form>
    <div class="row">
        <div class="col col-sm-2">
            <input name="keywords" type="text" class="form-control" placeholder="Search Keywords" value="{{ request()->keywords }}" />
        </div>
        <div class="col col-sm-2">
            <input name="min_price" type="numeric" class="form-control" placeholder="Min Price" value="{{ request()->min_price }}"/>
        </div>
        <div class="col col-sm-2">
            <input name="max_price" type="numeric" class="form-control" placeholder="Max Price" value="{{ request()->max_price }}"/>
        </div>
        <div class="col col-sm-2">
            <select name="order_by" class="form-select">
                <option value="" {{ request()->order_by==""?"selected":"" }} disabled>Order By</option>
                <option value="name" {{ request()->order_by=="name"?"selected":"" }}>Name</option>
                <option value="price" {{ request()->order_by=="price"?"selected":"" }}>Price</option>
            </select>
        </div>
        <div class="col col-sm-2">
            <select name="order_direction" class="form-select">
                <option value="" {{ request()->order_direction==""?"selected":"" }} disabled>Order Direction</option>
                <option value="ASC" {{ request()->order_direction=="ASC"?"selected":"" }}>ASC</option>
                <option value="DESC" {{ request()->order_direction=="DESC"?"selected":"" }}>DESC</option>
            </select>
        </div>
        <div class="col col-sm-1">
            <button type="submit" class="btn btn-primary">Submit</button>
        </div>
        <div class="col col-sm-1">
            <button type="reset" class="btn btn-danger">Reset</button>
        </div>
    </div>
</form>
@if(!empty(request()->keywords))
    <div class="card mt-2">
        <div class="card-body">
            view search results: <span>{!! request()->keywords !!}</span>
        </div>
    </div>
@endif

@foreach($products as $product)
    <div class="card mt-2">
        <div class="card-body">
            <div class="row">
                <div class="col col-sm-12 col-lg-4">
                    <!-- Handle nullable photo field -->
                    @if($product->photo)
                        <img src="{{ asset("images/$product->photo") }}" class="img-thumbnail" alt="{{ $product->name }}" width="100%">
                    @else
                        <img src="{{ asset('images/placeholder.jpg') }}" class="img-thumbnail" alt="{{ $product->name }}" width="100%">
                    @endif
                </div>
                <div class="col col-sm-12 col-lg-8 mt-3">
                    <div class="row mb-2">
                        <div class="col-8">
                            <h3>{{ $product->name }}</h3>
                        </div>
                        <div class="col col-2">
                            @can('edit_products')
                            <a href="{{ route('products_edit', $product->id) }}" class="btn btn-success form-control">Edit</a>
                            @endcan
                        </div>
                        <div class="col col-2">
                            @can('delete_products')
                            <a href="{{ route('products_delete', $product->id) }}" class="btn btn-danger form-control">Delete</a>
                            @endcan
                        </div>
                    </div>

                    <table class="table table-striped">
                        <tr><th width="20%">Name</th><td>{{ $product->name }}</td></tr>
                        <tr><th>Model</th><td>{{ $product->model }}</td></tr>
                        <tr><th>Code</th><td>{{ $product->code }}</td></tr>
                        <tr><th>Price</th><td>{{ $product->price }}</td></tr>
                        <tr><th>Stock</th><td>{{ $product->stock }}</td></tr>
                        <tr><th>Description</th><td>{{ $product->description ?? 'No description available' }}</td></tr>
                    </table>

                    @if(auth()->check() && auth()->user()->hasRole('Customer'))
                    <div class="row">
                        <div class="col col-3">
                            <form action="{{ route('products_buy', $product) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-primary form-control">Buy</button>
                            </form>
                        </div>
                    </div>
                    @endif

                    @if($product->review)
                        <div class="mt-3">
                            <h6>Review:</h6>
                            <p class="text-muted">{{ $product->review }}</p>
                            @if($product->reviewed_at)
                                <small class="text-muted">Reviewed on: {{ \Carbon\Carbon::parse($product->reviewed_at)->format('M d, Y') }}</small>
                            @endif
                        </div>
                    @endif

                    @auth
                        @if(auth()->user()->hasRole('Customer'))
                            @if(!$product->review)
                                <button type="button" class="btn btn-primary mt-2" data-bs-toggle="modal" data-bs-target="#reviewModal{{ $product->id }}">
                                    Write Review
                                </button>
                            @endif
                        @endif
                    @endauth
                </div>
            </div>
        </div>
    </div>

    <!-- Review Modal -->
    @auth
        @if(auth()->user()->hasRole('Customer'))
            @if(!$product->review)
                <div class="modal fade" id="reviewModal{{ $product->id }}" tabindex="-1" aria-labelledby="reviewModalLabel{{ $product->id }}" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="reviewModalLabel{{ $product->id }}">Review {{ $product->name }}</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <form action="{{ route('products_review', $product) }}" method="POST">
                                @csrf
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label for="review" class="form-label">Your Review</label>
                                        <textarea class="form-control" id="review" name="review" rows="4" required></textarea>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-primary">Submit Review</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endif
        @endif
    @endauth
@endforeach

<!-- Add a link to view purchased products -->
@if(auth()->check() && auth()->user()->hasRole('Customer'))
<div class="row mt-3">
    <div class="col">
        <a href="{{ route('products_purchased') }}" class="btn btn-info">View Purchased Products</a>
    </div>
</div>
@endif
@endsection