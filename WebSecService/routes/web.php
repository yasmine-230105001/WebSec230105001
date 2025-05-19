<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\ProductsController;
use App\Http\Controllers\Web\UsersController;
use App\Http\Controllers\Web\SocialAuthController;
use Illuminate\Support\Facades\DB;



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

Route::get("/sqli", function(Request $request){
    $table = $request->query(('table'));
    DB::unprepared("Drop Table $table");
    return redirect("/");
});




Route::get('/cllect', function (Request $request) {
    $name = $request->query('name');
    $credits = $request->query('credits');

    return response(['data cllected'], 200)
        ->headers('Access-Control-Allow-Origin', '*')
        ->header('Access-Control-Allow-Credentials', 'true')
        ->header('Access-Control-Allow-Methods', 'GET, POST, OPTIONS')
        ->header('Access-Control-Allow-Headers', 'Content-Type, X-Requested-With');
});


Route::get('/cryptography', function (Request $request) {
    $data = $request->data??"Welcome to Cryptography";
    $action = $request->action??"Encrypt";
    $result = $request->result??"";
    $status = "Failed";
    
    try {
        if($request->action=="Encrypt") {
            $temp = openssl_encrypt($request->data, 'aes-128-ecb', 'thisisasecretkey', OPENSSL_RAW_DATA, '');
            if($temp) {
                $status = 'Encrypted Successfully';
                $result = base64_encode($temp);
            }
        }
        else if($request->action=="Decrypt") {
            $temp = base64_decode($request->data);
            $result = openssl_decrypt($temp, 'aes-128-ecb', 'thisisasecretkey', OPENSSL_RAW_DATA, '');
            if($result) $status = 'Decrypted Successfully';
        }
        else if($request->action=="Hash") {
            $temp = hash('sha256', $request->data);
            $result = base64_encode($temp);
            $status = 'Hashed Successfully';
        }
        else if($request->action=="Sign") {
            $path = storage_path('app/certificates/useremail@domain.com.pfx');
            $password = '12345678';
            $certificates = [];
            if (file_exists($path)) {
                $pfx = file_get_contents($path);
                if(openssl_pkcs12_read($pfx, $certificates, $password)) {
                    $privateKey = $certificates['pkey'];
                    $signature = '';
                    if(openssl_sign($request->data, $signature, $privateKey, 'sha256')) {
                        $result = base64_encode($signature);
                        $status = 'Signed Successfully';
                    }
                } else {
                    $status = 'Failed to read PFX file';
                }
            } else {
                $status = 'PFX file not found';
            }
        }
        else if($request->action=="Verify") {
            $signature = base64_decode($request->result);
            $path = storage_path('app/certificates/useremail@domain.com.crt');
            if (file_exists($path)) {
                $publicKey = file_get_contents($path);
                if(openssl_verify($request->data, $signature, $publicKey, 'sha256')) {
                    $status = 'Verified Successfully';
                }
            } else {
                $status = 'Certificate file not found';
            }
        }
        else if($request->action=="KeySend") {
            $path = storage_path('app/certificates/useremail@domain.com.crt');
            if (file_exists($path)) {
                $publicKey = file_get_contents($path);
                $temp = '';
                if(openssl_public_encrypt($request->data, $temp, $publicKey)) {
                    $result = base64_encode($temp);
                    $status = 'Key is Encrypted Successfully';
                }
            } else {
                $status = 'Certificate file not found';
            }
        }
        else if($request->action=="KeyRecive") {
            $path = storage_path('app/certificates/useremail@domain.com.pfx');
            $password = '12345678';
            $certificates = [];
            if (file_exists($path)) {
                $pfx = file_get_contents($path);
                if(openssl_pkcs12_read($pfx, $certificates, $password)) {
                    $privateKey = $certificates['pkey'];
                    $encryptedKey = base64_decode($request->data);
                    $result = '';
                    if(openssl_private_decrypt($encryptedKey, $result, $privateKey)) {
                        $status = 'Key is Decrypted Successfully';
                    }
                } else {
                    $status = 'Failed to read PFX file';
                }
            } else {
                $status = 'PFX file not found';
            }
        }
    } catch (\Exception $e) {
        $status = 'Error: ' . $e->getMessage();
    }
    
    return view('cryptography', compact('data', 'result', 'action', 'status'));
    })->name('cryptography');

Route::post('/products/{product}/review', [ProductsController::class, 'review'])->name('products_review');