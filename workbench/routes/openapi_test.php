<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TestController;


Route::get([TestController::class, 'get'])->name('getAll')->middleware(['auth','rest']);
Route::post([TestController::class, 'post'])->name('createUser');
Route::get([TestController::class, 'get'])->name('getUser')->middleware(['auth','rest']);
Route::put([TestController::class, 'put']);
Route::delete([TestController::class, 'delete'])->name('deleteUser')->middleware(['auth','rest']);
