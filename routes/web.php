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

Route::get('/', function () {
    return view('welcome');
});
//Route::get('/prueba', 'PruebaController@prueba');
//Route::get('/users/prueba/{name}', 'UserController@prueba');

Route::post('/api/register', 'UserController@register');
Route::post('/api/login', 'UserController@login');
Route::put('/api/user/update', 'UserController@update');
Route::post('/api/user/upload','UserController@upload')->middleware(App\Http\Middleware\ApiAuthMiddleware::class);
Route::get('api/user/avatar/{filename}', 'UserController@getImage');
Route::get('api/user/detail/{id}', 'UserController@detail');

//Rutas para controlar categoria.
Route::resource('/api/category','CategoryController');

//Rutas para controlar el posts:
Route::resource('/api/film','FilmController');

//ruta para subir imagen al film
Route::post('api/film/upload', 'FilmController@upload');
Route::get('api/film/image_film/{filename}','FilmController@getImage');

Route::get('api/film/category/{id}', 'FilmController@getFilmByCategory');

Route::get('api/film/user/{id}','FilmController@getFilmByUser');                                        