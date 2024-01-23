<?php

use App\Http\Controllers\ErrorController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Auth\RegisterController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// error pages
Route::get('/error/{code}', [ErrorController::class, 'handleError']);

// auth routes
Route::match(['get', 'post'], '/login', [LoginController::class, 'login']);
Route::match(['get', 'post'], '/register', [RegisterController::class, 'register']);
Route::get('/logout', [LogoutController::class, 'logout']);

// main home controller
Route::get('/', [HomeController::class, 'homePage']);
