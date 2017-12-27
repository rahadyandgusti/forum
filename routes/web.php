<?php

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

// Route::get('/', function () {
//     return view('welcome');
// });


Route::group(['prefix' => 'admin'], function () {
    Voyager::routes();
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
Route::post('/upload/image', 'HomeController@uploadImage')->name('upload.image');
Route::get('/profile/{username}', 'ProfileController@index')->name('profile');
Route::get('/profile/{username}/category/{slug}', 'ProfileController@index')->name('profile.category');
