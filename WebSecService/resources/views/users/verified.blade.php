@extends('layouts.master')
@section('title', 'Email Verified')
@section('content')
<div class="d-flex justify-content-center">
    <div class="card m-4 col-sm-6">
        <div class="card-body text-center">
            <h2 class="text-success">Email Verified Successfully!</h2>
            <p>Your email address has been verified. You can now log in to your account.</p>
            <a href="{{ route('login') }}" class="btn btn-primary">Go to Login</a>
        </div>
    </div>
</div>
@endsection 