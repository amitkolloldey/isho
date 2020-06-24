<?php

use Illuminate\Support\Facades\Route;

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

Auth::routes(['verify' => true]);

Route::get('/', 'FrontController@home')->name('home');
Route::get('/product/{slug}', 'FrontController@product')->name('product_show');
Route::get('/product/query/fetch', 'FrontController@productFetch');
Route::get('/product/query/search', 'FrontController@search')->name('product_search');
Route::get('/product/show/price', 'FrontController@productShowPrice');
Route::post('/order/create/', 'FrontController@orderCreate')->name('order_create');
Route::get('/order/create/form', 'FrontController@showOrderCreateForm')->name('order_create_form');
Route::post('/order/store', 'FrontController@orderStore')->name('order_store');

Route::get('/currency/switch/', 'FrontController@currencySwitch')->name('currency_switcher');

Route::group(['prefix' => 'admin', 'middleware' => ['auth','verified']], function () {
    Route::get('/dashboard', 'DashboardController@index')->name('dashboard');

    Route::get('/products', 'ProductController@index')->name('products');
    Route::get('/product/create', 'ProductController@create')->name('product_create');
    Route::get('/product/edit/{id}', 'ProductController@edit')->name('product_edit');

    Route::get('/orders', 'OrderController@index')->name('orders');
    Route::get('/order/show/{id}', 'OrderController@show');

    Route::get('/stocks', 'StockController@index')->name('stocks');
    Route::get('/stock/create', 'StockController@create')->name('stock_create');
    Route::get('/stock/edit/{id}', 'StockController@edit')->name('stock_edit');

    Route::get('/attributes', 'AttributeController@index')->name('attributes');
    Route::get('/attribute/create', 'AttributeController@create')->name('attribute_create');
    Route::get('/attribute/edit/{id}', 'AttributeController@edit')->name('attribute_edit');

    Route::get('/stock/search/download', 'StockController@stockDownload');
});

Route::get('admin/login', 'AuthController@showLogin')->name('showLogin');
Route::get('admin/register', 'AuthController@showRegister')->name('showRegister');

// Defining On Web Routes Because Session can not be access via api
Route::get('/api/stock/search', 'StockController@search')->middleware(['auth','verified']);
Route::get('/api/stock/search/download', 'StockController@stockDownload')->middleware(['auth','verified']);

Route::post('/api/admin/logout', 'AuthController@logout')->name('logout')->middleware(['auth','verified']);
Route::post('/api/admin/login', 'AuthController@login')->name('admin_login');
Route::post('/api/admin/register', 'AuthController@register')->name('admin_register');
