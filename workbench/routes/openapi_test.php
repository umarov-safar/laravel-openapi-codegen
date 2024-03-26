<?php

use App\Http\ApiV1\Modules\Companies\Controllers\CompaniesController;
use App\Http\ApiV1\Modules\Companies\Controllers\CompanyEmployeesController;
use App\Http\ApiV1\Modules\Companies\Controllers\EmployeeBalancesController;
use Illuminate\Support\Facades\Route;

Route::post([CompaniesController::class, 'search'])->name('searchCompany');
Route::post([CompaniesController::class, 'create'])->name('createCompany')->middleware(['auth', 'admin']);
Route::get([CompaniesController::class, 'get'])->name('getCompany');
Route::delete([CompaniesController::class, 'delete'])->name('deleteCompany');
Route::patch([CompaniesController::class, 'patch'])->name('patchCompany');
Route::post([CompanyEmployeesController::class, 'search'])->name('searchCompanyEmployees');
Route::post([CompanyEmployeesController::class, 'create'])->name('createCompanyEmployee');
Route::get([CompanyEmployeesController::class, 'get'])->name('getCompanyEmployee');
Route::delete([CompanyEmployeesController::class, 'delete'])->name('deleteCompanyEmployee');
Route::patch([CompanyEmployeesController::class, 'patch'])->name('patchCompanyEmployee');
Route::post([EmployeeBalancesController::class, 'search'])->name('searchEmployeeBalance');
