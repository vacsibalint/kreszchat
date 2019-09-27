<?php
Route::get('/', function () {
    return view('welcome');
});

Route::match(['get', 'post'], '/botman', 'BotManController@handle');
Route::get('/botman/tinker', 'BotManController@tinker');

Auth::routes();

//BACKEND
Route::get('/home', 'HomeController@index')->name('home');
Route::get('/profil/{userId}', 'HomeController@profile')->name('profil');
Route::get('/statisztika/{userId}', 'HomeController@stats')->name('stats');

