<?php

use App\Http\Controllers\Api\TestController;
use App\Http\Controllers\CrudController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\FireStoreController;
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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});


// Route('order', [CrudController::class, 'store']);

// Route::post('order', [CrudController::class, 'order']);
// Route::post('driver', [CrudController::class, 'driver']);


Route::post('order', [TestController::class, 'order']);
Route::post('driver', [TestController::class, 'driver']);
Route::post('updateRef', [TestController::class, 'updateRef']);



Route::post('updateOrInsertData', [FireStoreController::class, 'updateOrInsertData']);

Route::post('setDriverOrderStatus', [TestController::class, 'updateDriverOrderStatus']);