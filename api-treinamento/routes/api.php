<?php

use Illuminate\Support\Facades\Route; // ✅ IMPORTAÇÃO OBRIGATÓRIA
use App\Http\Controllers\Auth\CompanyAuthController;

Route::get('/test-api', function () {
    return response()->json(['message' => 'API.php está sendo lido!']);
});

Route::prefix('company')  // Prefixo para as rotas relacionadas à empresa
    ->controller(CompanyAuthController::class)  // Usando o controller diretamente
    ->group(function () {

        // Rota para registrar uma empresa
        Route::post('/register', 'register');

        // Rota para login de uma empresa
        Route::post('/login', 'login');

        // Rotas protegidas por middleware de autenticação
        Route::middleware('auth:sanctum')->group(function () {

            // Rota para logout de uma empresa
            Route::post('/logout', 'logout');

        });
        


    });
