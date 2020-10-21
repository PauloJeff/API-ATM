<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Resources\UserCollection;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AccountController;
use App\Models\User;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('/users', function() {
    return UserCollection::collection(User::all());
});

Route::get('/user/{id}', [UserController::class, 'show']);

Route::post('/user', [UserController::class, 'store']);

Route::put('/user/{id}', [UserController::class, 'update']);

Route::delete('user/{id}', [UserController::class, 'delete']);

Route::post('/account/create', [AccountController::class, 'store']);

Route::post('/account/login', [AccountController::class, 'login']);

Route::put('/account/deposit', [AccountController::class, 'deposit']);

Route::put('/account/withdraw', [AccountController::class, 'withdraw']);