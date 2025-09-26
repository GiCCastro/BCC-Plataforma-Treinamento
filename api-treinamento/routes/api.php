<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\CompanyAuthController;
use App\Http\Controllers\Company\CompanyProfileController;


Route::prefix('company')  // Prefixo para as rotas relacionadas à empresa
    ->controller(CompanyAuthController::class)  // Usando o controller diretamente
    ->group(function () {

        Route::post('/register', 'register');
        Route::post('/login', 'login');
        Route::middleware('auth:sanctum')->group(function () {

            Route::post('/logout', 'logout');

        });

    });

Route::prefix('company')
    ->middleware('auth:sanctum')
    ->group(function () {
        Route::patch('/update-profile', [CompanyProfileController::class, 'updateProfile']);
        Route::patch('/update-assets', [CompanyProfileController::class, 'uploadAssets']);
    });
    