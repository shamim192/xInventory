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

    Route::resource('categories', 'CategoryController');
    Route::resource('types', 'TypeController');
    Route::resource('sizes', 'SizeController');
    Route::resource('colors', 'ColorController');
    Route::resource('companies', 'CompanyController');
    Route::resource('factories', 'FactoryController');
    Route::resource('stores', 'StoreController');
    Route::resource('base-units', 'BaseUnitController');
    Route::resource('units', 'UnitController');
    
});