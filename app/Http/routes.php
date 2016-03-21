<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', 'WelcomeController@index');


Route::controllers([
    'auth' => 'Auth\AuthController',
    'password' => 'Auth\PasswordController',
]);
/**
 * Show posts
 */
Route::get('/post/index', 'PostController@index');

/**
 * Add new post
 */
Route::get('/post/add', 'PostController@add');
/**
 *
 */
Route::get('/post/facebook','PostController@getFacebookApi');

Route::get('/user/login','UserController@login');

Route::get('/user/loginFacebook', 'UserController@loginWithFacebook');

Route::get('/user/callBackFacebook', 'UserController@callBackFacebook');