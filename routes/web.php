<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ArticleController;

Route::get('/', function () {
    return view('welcome');
});


Route::get('/export-articles', [ArticleController::class, 'exportCSV'])->name('articles.export');




Route::resource('articles', ArticleController::class);
