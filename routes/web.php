<?php

use Illuminate\Support\Facades\Auth;
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


Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('/','App\Http\Controllers\Auth\LoginController@showLoginForm');

Auth::routes(['verify' => true, 'register' =>false]);

Route::group(['namespace' => 'App\Http\Controllers\Admin', 'middleware' => ['auth', 'verified']], function () {    
    Route::get('dashboard','DashboardController@index')->name('dashboard');

    Route::get('profile','ProfileController@index')->name('profile');
    Route::post('profile', 'ProfileController@update');
    Route::get('profile/password', 'ProfileController@password')->name('profile.password');
    Route::post('profile/password', 'ProfileController@passwordUpdate');

    Route::resource('user', 'UserController');
    Route::get('customers/due', 'CustomerController@due')->name('customers.due');
    Route::resource('customer', 'CustomerController');
    Route::get('suppliers/due', 'SupplierController@due')->name('suppliers.due');
    Route::resource('supplier', 'SupplierController');

    Route::resource('base-units', 'BaseUnitController');
    Route::resource('units', 'UnitController');
    Route::resource('categories', 'CategoryController');
    Route::resource('products', 'ProductController');
    
    Route::resource('bank', 'BankController');
    
});