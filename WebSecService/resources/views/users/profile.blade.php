@extends('layouts.master')
@section('title', 'User Profile')
@section('content')
<div class="row">
    <div class="m-4 col-sm-6">
        <table class="table table-striped">
            <tr>
                <th>Name</th><td>{{$user->name}}</td>
            </tr>
            <tr>
                <th>Email</th><td>{{$user->email}}</td>
            </tr>
            <!-- Add Credit Display Here -->
            <tr>
                <th>Credit</th><td>{{$credit}}</td>
            </tr>
            <tr>
                <th>Roles</th>
                <td>
                    @foreach($user->roles as $role)
                        <span class="badge bg-primary">{{$role->name}}</span>
                    @endforeach
                </td>
            </tr>
            <tr>
                <th>Permissions</th>
                <td>
                    @foreach($permissions as $permission)
                        <span class="badge bg-success">{{$permission->display_name}}</span>
                    @endforeach
                </td>
            </tr>
        </table>

        <!-- Add Credit Form for Employees/Admins -->
        @if(auth()->user()->hasAnyRole(['Admin', 'Employee']) && $user->hasRole('Customer'))
        <div class="row mb-3">
            <div class="col-sm-6">
                <form action="{{ route('users_add_credit', $user) }}" method="POST">
                    @csrf
                    <div class="input-group">
                        <input type="number" name="credit" class="form-control" placeholder="Add Credit" step="0.01" min="0.01" required>
                        <button type="submit" class="btn btn-success">Add Credit</button>
                    </div>
                    @error('credit')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </form>
            </div>
        </div>
        @endif

        <div class="row">
            <div class="col col-6">
            </div>
            @if(auth()->user()->hasPermissionTo('admin_users') || auth()->id() == $user->id)
            <div class="col col-4">
                <a class="btn btn-primary" href='{{route('edit_password', $user->id)}}'>Change Password</a>
            </div>
            @else
            <div class="col col-4">
            </div>
            @endif
            @if(auth()->user()->hasPermissionTo('edit_users') || auth()->id() == $user->id)
            <div class="col col-2">
                <a href="{{route('users_edit', $user->id)}}" class="btn btn-success form-control">Edit</a>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection