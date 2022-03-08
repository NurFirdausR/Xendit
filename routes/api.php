<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Payment\XenditController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('xendit/va/list',[XenditController::class,'getListVa']);
Route::post('xendit/va/create',[XenditController::class,'createVa']);
Route::post('xendit/va/callback',[XenditController::class,'callbackVa']);
Route::get('xendit/balance/show',[XenditController::class,'showBalance']);
