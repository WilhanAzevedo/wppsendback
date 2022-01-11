<?php

use Illuminate\Http\Request;
use Illuminate\Routing\RouteGroup;
use Illuminate\Support\Facades\Route;


Route::post('/register', 'AuthController@register')->middleware('jwt');

Route::post('/login', 'AuthController@login');

Route::group(['middleware' => 'jwt'], function () {

    Route::get('/convert', 'xlsxtojson@index')->middleware('cors');

    Route::get('/lista/{bairro?}', 'Bairros@show')->middleware('cors');
    
    Route::get('/list/todos', 'Bairros@listaBairros')->middleware('cors');
    
    Route::post('/enviar', 'Bairros@enviar')->middleware('cors');
    
    Route::get('/enviar', 'Bairros@enviar')->name('/enviar')->middleware('cors');
    
    Route::get('/status', 'Bairros@verificaStatus')->middleware('cors');
});

