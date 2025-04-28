<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\ProductsController;
use App\Http\Controllers\Web\UsersController;
use App\Http\Controllers\Web\SocialAuthController;

Route::get('register', [UsersController::class, 'register'])->name('register');
Route::post('register', [UsersController::class, 'doRegister'])->name('do_register');
Route::get('login', [UsersController::class, 'login'])->name('login');
Route::post('login', [UsersController::class, 'doLogin'])->name('do_login');
Route::get('logout', [UsersController::class, 'doLogout'])->name('do_logout');
Route::get('users', [UsersController::class, 'list'])->name('users');
Route::get('profile/{user?}', [UsersController::class, 'profile'])->name('profile');
Route::get('users/edit/{user?}', [UsersController::class, 'edit'])->name('users_edit');
Route::post('users/save/{user}', [UsersController::class, 'save'])->name('users_save');
Route::get('users/delete/{user}', [UsersController::class, 'delete'])->name('users_delete');
Route::get('users/edit_password/{user?}', [UsersController::class, 'editPassword'])->name('edit_password');
Route::post('users/save_password/{user}', [UsersController::class, 'savePassword'])->name('save_password');
Route::post('/admin/add-employee', [UsersController::class, 'storeEmployee'])->name('admin.storeEmployee');

Route::get('verify', [UsersController::class, 'verify'])->name('verify');

// GitHub Authentication Routes
Route::get('auth/github', [SocialAuthController::class, 'redirectToGithub'])->name('github.login');
Route::get('auth/github/callback', [SocialAuthController::class, 'handleGithubCallback'])->name('github.callback');

Route::get('products', [ProductsController::class, 'list'])->name('products_list');
Route::get('products/edit/{product?}', [ProductsController::class, 'edit'])->name('products_edit');
Route::post('products/save/{product?}', [ProductsController::class, 'save'])->name('products_save');
Route::get('products/delete/{product}', [ProductsController::class, 'delete'])->name('products_delete');
Route::get('/products', [ProductsController::class, 'list'])->name('products_list');
Route::get('/products/purchased', [ProductsController::class, 'purchasedProducts'])->name('products_purchased');
Route::post('/products/{product}/buy', [ProductsController::class, 'buy'])->name('products_buy');
Route::get('/products/edit/{product?}', [ProductsController::class, 'edit'])->name('products_edit');
Route::post('/products/save/{product?}', [ProductsController::class, 'save'])->name('products_save');
Route::post('/products/delete/{product}', [ProductsController::class, 'delete'])->name('products_delete');
Route::post('/products/{product}/buy', [ProductsController::class, 'buy'])->name('products_buy');
Route::get('/users', [UsersController::class, 'list'])->name('users');
Route::post('/users/{user}/add-credit', [UsersController::class, 'addCredit'])->name('users_add_credit');
Route::get('/', function () {
    return view('welcome');
});
Route::post('/users/{user}/add-credit', [UsersController::class, 'addCredit'])->name('users_add_credit');
Route::get('/multable', function (Request $request) {
    $j = $request->number??5;
    $msg = $request->msg;
    return view('multable', compact("j", "msg"));
});

Route::get('/even', function () {
    return view('even');
});

Route::get('/prime', function () {
    return view('prime');
});

Route::get('/test', function () {
    return view('test');
});
