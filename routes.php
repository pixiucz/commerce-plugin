<?php

use RainLab\User\Facades\Auth;

Route::prefix('api/v1')
    ->namespace('Pixiu\Commerce\api\Controllers')
    ->middleware('web')
    ->group(function() {
        // Categories
        Route::get('/category/{id?}', 'CategoryController@index');
        Route::get('/category/{id}/product-variant', 'CategoryController@productVariants');

        // Products
        Route::get('/product-variant', 'ProductVariantController@index');
        Route::get('/product-variant/{id}', 'ProductVariantController@show');

        // User
        // Route::patch('user', 'UserController@update'); TODO: implement edit (?)
        Route::post('user/register', 'UserController@register');
        Route::post('user/login', 'UserController@login');

        Route::middleware('Pixiu\Commerce\api\Middlewares\CheckLoginMiddleware')->group(function() {
            Route::get('user', 'UserController@show');
            Route::get('user/history', 'UserController@history');
        });


        // User's address
        Route::middleware('Pixiu\Commerce\api\Middlewares\CheckLoginMiddleware')->group(function() {
            Route::post('/address', 'AddressController@add');
            Route::put('/address/{id}', 'AddressController@edit');
            Route::delete('/address/{id}', 'AddressController@delete');
        });


        // Order
        Route::post('/cart', 'CartController@store'); // TODO: implement / change database
    });