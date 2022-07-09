<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProductController;

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
    return redirect('login');
  //  return view('welcome');
});

Auth::routes();

Route::get('/home', [HomeController::class, 'index'])->name('home');

Route::group(['middleware' => ['auth']], function() {
    Route::resource('roles', RoleController::class);
    Route::resource('users', UserController::class);
    Route::resource('products', ProductController::class);
});

//fullcalender
Route::get('/fullcalendar','App\Http\Controllers\FullCalendarController@index');
Route::post('/fullcalendar/create','App\Http\Controllers\FullCalendarController@create');
Route::post('/fullcalendar/update','App\Http\Controllers\FullCalendarController@update');
Route::post('/fullcalendar/delete','App\Http\Controllers\FullCalendarController@destroy');
