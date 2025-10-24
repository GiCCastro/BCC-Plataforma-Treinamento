<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\CompanyAuthController;
use App\Http\Controllers\Company\CompanyProfileController;
use App\Http\Controllers\Auth\CollaboratorAuthController;
use App\Http\Controllers\Department\DepartmentController;
use App\Http\Controllers\Course\CourseController;
use App\Http\Controllers\Collaborator\CollaboratorController;
use App\Http\Controllers\Track\TrackController;


Route::prefix('company')->group(function () {

    Route::controller(CompanyAuthController::class)->group(function () {
        Route::post('/auth/register', 'register');
        Route::post('/auth/login', 'login');
    });

    Route::middleware('auth:company')->group(function () {

        Route::post('/auth/logout', [CompanyAuthController::class, 'logout']);

        Route::patch('/profile', [CompanyProfileController::class, 'updateProfile']);
        Route::patch('/assets', [CompanyProfileController::class, 'uploadAssets']);

        Route::prefix('department')->group(function () {
            Route::post('/', [DepartmentController::class, 'register']);
            Route::get('/', [DepartmentController::class, 'index']);
        });

        Route::prefix('collaborator')->group(function () {
            Route::post('/', [CollaboratorAuthController::class, 'register']);
            Route::get('/', [CollaboratorController::class, 'index']);

        });

        Route::prefix('course')->group(function () {
            Route::post('/', [CourseController::class, 'register']);
            Route::get('/', [CourseController::class, 'index']);
        });

        Route::prefix('track')->group(function () {
            Route::post('/', [TrackController::class, 'register']);
        });
    });
});


Route::prefix('collaborator')->group(function () {
    Route::controller(CollaboratorAuthController::class)->group(function () {
        Route::post('/auth/login', 'login');
    });

    Route::middleware('auth:collaborator')->group(function () {
        Route::post('/auth/logout', [CollaboratorAuthController::class, 'logout']);

        Route::prefix('learning')->group(function () {
            Route::post('/answer', [CollaboratorController::class, 'answerQuestion']);
            Route::get('/progress', [CollaboratorController::class, 'getLearning']);
        });
    });
});

