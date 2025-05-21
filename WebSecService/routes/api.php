<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\UsersController;

Route::post('register', [UsersController::class, 'register']);
Route::post('login', [UsersController::class, 'login']);

Route::get('/users', [UsersController::class, 'users'])->middleware('auth:sanctum');
Route::get('/logout', [UsersController::class, 'logout'])->middleware('auth:sanctum');