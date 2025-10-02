<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;


class CompanyAuthController extends Controller
{
    // Cadastro da empresa

public function register(Request $request)
{
    $validator = Validator::make($request->all(), [
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|unique:companies',
        'password' => 'required|string|min:8',
        'cnpj' => 'required|string|unique:companies',
        'cnae' => 'required|string'
    ], [
        'email.unique' => 'Email já cadastrado',
        'cnpj.unique' => 'CNPJ já cadastrado',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'message' => 'Erro de validação',
            'errors' => $validator->errors()
        ], 422);
    }

    try {
        $company = Company::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password, // bcrypt no model
            'cnpj' => $request->cnpj,
            'cnae' => $request->cnae,
        ]);

        $token = $company->createToken('company_token')->plainTextToken;

        return response()->json([
            'company' => $company,
            'token' => $token
        ], 201);

    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Erro ao cadastrar empresa',
            'error' => $e->getMessage()
        ], 500);
    }
}

    // Login da empresa
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string'
        ]);

        $company = Company::where('email', $request->email)->first();

        if (!$company || !Hash::check($request->password, $company->password)) {
            return response()->json([
                'message' => 'Credenciais inválidas'
            ], 401);
        }

        $token = $company->createToken('company_token')->plainTextToken;

        return response()->json([
            'company' => $company,
            'token' => $token
        ], 200);
    }

    // Logout da empresa
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout realizado com sucesso'
        ], 200);
    }
}
