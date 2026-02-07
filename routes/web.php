<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UrlController;

Route::get('/', function () {
    return view('welcome');
});

Route::post('/urls', [UrlController::class, 'store'])->name('urls.store');
Route::get('/{code}', [UrlController::class, 'redirect'])
    ->where('code', '[A-Za-z0-9]{6}')
    ->name('urls.redirect');
