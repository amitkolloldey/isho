<?php

use Illuminate\Support\Facades\Route;

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

Route::middleware(['auth:api', 'verified'])->group(function () {

    Route::get('/products', 'ProductController@products');
    Route::post('/product/store', 'ProductController@store')->name('product_store');
    Route::put('/product/update/{id}', 'ProductController@update')->name('product_update');
    Route::delete('/product/delete/{id}', 'ProductController@destroy')->name('product_delete');
    Route::get('/product/search', 'ProductController@search');
    Route::get('/product/fetch', 'ProductController@productFetch');
    Route::post('/product/main_image/upload', 'ProductController@mainImageUpload');
    Route::post('/product/attribute_image/upload', 'ProductController@attributeImageUpload');

    Route::get('/attributes', 'AttributeController@attributes');
    Route::post('/attribute/store', 'AttributeController@store')->name('attribute_store');
    Route::put('/attribute/update/{id}', 'AttributeController@update')->name('attribute_update');
    Route::delete('/attribute/delete/{id}', 'AttributeController@destroy')->name('attribute_delete');
    Route::delete('/value/delete/{id}', 'AttributeController@valueDestroy')->name('value_delete');

    Route::get('/orders', 'OrderController@orders');
    Route::delete('/order/delete/{id}', 'OrderController@destroy')->name('order_delete');

//    Route::get('/stocks', 'StockController@stocks');
    Route::post('/stock/store', 'StockController@store')->name('stock_store');
    Route::put('/stock/update/{id}', 'StockController@update')->name('stock_update');
    Route::delete('/stock/delete/{id}', 'StockController@destroy')->name('stock_delete');



    Route::post('/logout', 'AuthController@logout');
});



