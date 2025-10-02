<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\CompanyAuthController;
use App\Http\Controllers\Company\CompanyProfileController;
use App\Http\Controllers\Auth\CollaboratorAuthController;
use App\Http\Controllers\Department\DepartmentController;


Route::prefix('company')->group(function () {

    Route::controller(CompanyAuthController::class)->group(function () {
        Route::post('/auth/register', 'register'); 
        Route::post('/auth/login', 'login');       
    });

    Route::middleware('auth:company')->group(function () {

        Route::post('/auth/logout', [CompanyAuthController::class, 'logout']);

        Route::patch('/profile', [CompanyProfileController::class, 'updateProfile']);
        Route::patch('/assets', [CompanyProfileController::class, 'uploadAssets']);

        Route::prefix('departments')->group(function () {
            Route::post('/', [DepartmentController::class, 'register']);
            Route::get('/', [DepartmentController::class, 'index']);     
        });

        Route::prefix('collaborators')->group(function () {
            Route::post('/', [CollaboratorAuthController::class, 'register']); 
        });
    });
});


Route::prefix('collaborator')->group(function () {
    Route::controller(CollaboratorAuthController::class)->group(function () {
        Route::post('/auth/login', 'login'); // Login do colaborador
    });
});
