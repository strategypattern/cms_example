<?php

Auth::routes();

Route::get('/', 'Admin\ContentController@index');
Route::get('/home', 'Admin\ContentController@index');

Route::resource('/admin/content', 'Admin\ContentController')->middleware('auth');

Route::post('/admin/content-reversions/{revision}', 'Admin\ContentReversionsController@store')->middleware('auth');