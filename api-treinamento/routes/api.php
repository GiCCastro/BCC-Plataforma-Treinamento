<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\CompanyAuthController;
use App\Http\Controllers\Company\CompanyProfileController;
use App\Http\Controllers\Auth\CollaboratorAuthController;
use App\Http\Controllers\Department\DepartmentController;
use App\Http\Controllers\Course\CourseController;
use App\Http\Controllers\Collaborator\CollaboratorController;
use App\Http\Controllers\Track\TrackController;
use App\Http\Controllers\Award\AwardController;
use App\Http\Controllers\Award\AwardCollaboratorController;

Route::prefix('company')->group(function () {

    Route::controller(CompanyAuthController::class)->group(function () {
        Route::post('/auth/register', 'register');
        Route::post('/auth/login', 'login');
    });

    Route::middleware('auth:company')->group(function () {

        Route::post('/auth/logout', [CompanyAuthController::class, 'logout']);

        Route::patch('/profile', [CompanyProfileController::class, 'updateProfile']);
        Route::post('/assets', [CompanyProfileController::class, 'uploadAssets']);
        Route::delete('/{CompanyId}', [CompanyProfileController::class, 'destroy']);

        Route::prefix('department')->group(function () {
            Route::post('/', [DepartmentController::class, 'register']);
            Route::get('/', [DepartmentController::class, 'index']);
        });

        Route::prefix('collaborator')->group(function () {
            Route::post('/', [CollaboratorAuthController::class, 'register']);
            Route::get('/', [CollaboratorController::class, 'index']);
            Route::patch('/{CollaboratorId}', [CollaboratorController::class, 'deactivate']);
        });

        Route::prefix('course')->group(function () {
            Route::post('/', [CourseController::class, 'register']);
            Route::get('/', [CourseController::class, 'index']);
            Route::delete('/{courseId}', [CourseController::class, 'destroy']);
        });

        Route::prefix('track')->group(function () {
            Route::post('/', [TrackController::class, 'register']);
            Route::get('/', [TrackController::class, 'index']);
            Route::delete('/{trackId}', [TrackController::class, 'destroy']);


        });

        Route::prefix('award')->group(function () {
            Route::post('/', [AwardController::class, 'register']);
            Route::get('/', [AwardController::class, 'index']);
            Route::delete('/{awardId}', [AwardController::class, 'destroy']);

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

        Route::prefix('award')->group(function () {
            Route::get('/', [AwardCollaboratorController::class, 'index']);
        });
    });
});

