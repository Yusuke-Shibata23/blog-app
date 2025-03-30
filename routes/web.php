<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// SPAのルート
Route::get('/{any}', function () {
    return view('welcome');
})->where('any', '.*');
