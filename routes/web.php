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


use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', 'HomeController@index')->name('home');


Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
/*Les méthodes des courses */
Route::post('add-receipt', 'CourseController@addReceipt')->name('add-receipt');
Route::resource('/courses', 'CourseController');

Route::get('/courses.create', 'CourseController@create');


/*Les methodes des produits*/
Route::post('/courses.create', 'ProductController@addCourse')->name('send-products');
Route::resource('/products', 'ProductController');
Route::get('/products/delete/{productId}', 'ProductController@destroy');

Route::post('/products.search', 'ProductController@search')->name('search');

Route::get('/admin', 'UserController@index')->name('admin');

Route::resource('/profile', 'UserController');
Route::resource('/messages', 'MessageController');
Route::get('/contact', 'HomeController@contact');
Route::get('/aide', 'HomeController@help');
Route::post('/checkEmail', 'Auth\LoginController@checkEmail')->name('checkEmail');





