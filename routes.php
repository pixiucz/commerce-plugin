<?php

use RainLab\User\Facades\Auth;

Route::prefix('api/v1')
    ->namespace('Pixiu\Commerce\api\Controllers')
    ->middleware('web')
    ->group(function() {
        // Categories
        Route::get('/category/{id?}', 'CategoryController@index');
        Route::get('/category/{slug}/products', 'CategoryController@productVariants');

        // Products
        Route::get('/product', 'ProductVariantController@index');
        Route::get('/product/{slug}', 'ProductVariantController@show');

        // User
        // Route::patch('user', 'UserController@update'); TODO: implement edit (?)
        Route::post('user/register', 'UserController@register');
        Route::post('user/login', 'UserController@login');
        Route::post('user/logout', 'UserController@logout');

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
        Route::post('/order', 'OrderController@store');
    });