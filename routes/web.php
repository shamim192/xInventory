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

Route::get('/', 'App\Http\Controllers\Auth\LoginController@showLoginForm');

Auth::routes(['verify' => true, 'register' => false]);

Route::group(['namespace' => 'App\Http\Controllers\Admin', 'middleware' => ['auth', 'verified']], function () {
    Route::get('dashboard', 'DashboardController@index')->name('dashboard');

    Route::get('profile', 'ProfileController@index')->name('profile');
    Route::post('profile', 'ProfileController@update');
    Route::get('profile/password', 'ProfileController@password')->name('profile.password');
    Route::post('profile/password', 'ProfileController@passwordUpdate');

    Route::resource('user', 'UserController');
    Route::get('customers/due', 'CustomerController@due')->name('customers.due');
    Route::resource('customer', 'CustomerController');
    Route::get('suppliers/due', 'SupplierController@due')->name('suppliers.due');
    Route::resource('supplier', 'SupplierController');
    Route::resource('investor', 'InvestorController');
    Route::get('loanholders/due', 'LoanHolderController@due')->name('loanholders.due');
    Route::resource('loan-holder', 'LoanHolderController');

    Route::resource('base-units', 'BaseUnitController');
    Route::resource('units', 'UnitController');
    Route::resource('categories', 'CategoryController');
    Route::resource('products', 'ProductController');
    Route::resource('income-category', 'Income\IncomeCategoryController');
    Route::resource('expense-category', 'Expense\CategoryController');

    Route::get('banks/due', 'BankController@due')->name('banks.due');
    Route::resource('bank', 'BankController');

    Route::group(['namespace' => 'Stock'], function () {

        Route::get('stock/products-by-category/{category}', 'StockController@getProductsByCategory');
        Route::resource('stock', 'StockController');

        Route::post('supplier-wise-stock-ajax', 'StockReturnController@supplierWiseStock')->name('supplier-wise-stock-ajax');
        Route::post('stock-item-ajax', 'StockReturnController@stockItem')->name('stock-item-ajax');
        Route::resource('stock-return', 'StockReturnController');
    });

    Route::group(['namespace' => 'Sale'], function () {
        Route::get('sale/products-by-category/{category}', 'SaleController@getProductsByCategory');
        Route::get('customer-last-discount', 'SaleController@customerLastDiscount')->name('sale.customer.last.discount');
        Route::resource('sale', 'SaleController');
        Route::post('customer-wise-sale-ajax', 'SaleReturnController@customerWiseSale')->name('customer-wise-sale-ajax');
        Route::post('sale-item-ajax', 'SaleReturnController@saleItem')->name('sale-item-ajax');
        Route::resource('sale-return', 'SaleReturnController');
    });

    Route::group(['namespace' => 'Payment'], function () {

        Route::resource('income', 'IncomeController');
        Route::resource('expense', 'ExpenseController');
        Route::resource('fund-transfer', 'FundTransferController');
        Route::resource('invest', 'InvestController');
        Route::resource('loan', 'LoanController');
        Route::get('supplier-payments/adjustment', 'SupplierPaymentController@adjustment')->name('supplier-payments.adjustment');
        Route::resource('supplier-payments', 'SupplierPaymentController');
        Route::get('customer-payments/adjustment', 'CustomerPaymentController@adjustment')->name('customer-payments.adjustment');
        Route::resource('customer-payments', 'CustomerPaymentController');
        Route::get('loan-holder-payments/adjustment', 'LoanHolderPaymentController@adjustment')->name('loan-holder-payments.adjustment');
        Route::resource('loan-holder-payments', 'LoanHolderPaymentController');
    });
});
