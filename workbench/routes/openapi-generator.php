<?php

use Illuminate\Support\Facades\Route;
use App\Http\ApiV1\Modules\Companies\Controllers\CompaniesController;
use App\Http\ApiV1\Modules\Companies\Controllers\CompanyEmployeesController;
use App\Http\ApiV1\Modules\Companies\Controllers\EmployeeBalancesController;


Route::post([CompaniesController::class, 'search']);
Route::post([CompaniesController::class, 'create']);
Route::get([CompaniesController::class, 'get']);
Route::patch([CompaniesController::class, 'patch']);
Route::delete([CompaniesController::class, 'delete']);
Route::post([CompanyEmployeesController::class, 'search']);
Route::post([CompanyEmployeesController::class, 'create']);
Route::get([CompanyEmployeesController::class, 'get']);
Route::patch([CompanyEmployeesController::class, 'patch']);
Route::delete([CompanyEmployeesController::class, 'delete']);
Route::post([EmployeeBalancesController::class, 'search']);
