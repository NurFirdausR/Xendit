<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});
// Route::get('/login',[AuthController::class,'form_login'])->name('login.form');
// Route::get('/register',[AuthController::class,'form_register'])->name('register.form');
// Route::post('/login/store',[AuthController::class,'login'])->name('login');
// Route::post('/registe/store',[AuthController::class,'register'])->name('register');

Route::get('/dashboard',[DashboardController::class,'index'])->middleware(['auth'])->name('dashboard');

Route::get('/signout',[AuthController::class,'logout'])->name('logout');

require __DIR__.'/auth.php';
