<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Collaborator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\QueryException;

class CollaboratorAuthController extends Controller
{
    // Cadastro do colaborador
   public function register(Request $request)
{
    try {
        // Validação inicial
        $validator = \Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:collaborators,email',
            'cpf' => 'required|string|unique:collaborators,cpf',
            'password' => 'required|string|min:8',
            'birth_date' => 'required|string',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'departments' => 'required|array',
            'departments.*' => 'exists:departments,id',
        ], [
            'email.unique' => 'Email já cadastrado',
            'cpf.unique' => 'CPF já cadastrado',
            'departments.*.exists' => 'Um ou mais departamentos informados não existem',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Dados inválidos',
                'errors' => $validator->errors()
            ], 422);
        }

        // Pega a empresa autenticada via guard 'company'
        $company = auth('company')->user();
        if (!$company) {
            return response()->json(['message' => 'Empresa não autenticada'], 401);
        }

        // Verifica se departamentos pertencem à empresa
        $validDepartments = $company->departments()->pluck('id')->toArray();
        $invalidDepartments = array_diff($request->departments, $validDepartments);

        if (count($invalidDepartments)) {
            return response()->json([
                'message' => 'Um ou mais departamentos não pertencem à empresa',
                'invalid_departments' => array_values($invalidDepartments)
            ], 422);
        }

        // Cria o colaborador
        $collaborator = Collaborator::create([
            'name' => $request->name,
            'email' => $request->email,
            'cpf' => $request->cpf,
            'password' => $request->password,
            'birth_date' => $request->birth_date,
            'photo' => $request->photo ?? null,
            'is_active' => true,
            'company_id' => $company->id
        ]);

        // Associa departamentos
        $collaborator->departments()->sync($request->departments);

        // Gera token
        $token = $collaborator->createToken('collaborator_token')->plainTextToken;

        return response()->json([
            'collaborator' => $collaborator,
            'token' => $token
        ], 201);

    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Erro ao cadastrar colaborador',
            'error' => $e->getMessage()
        ], 500);
    }
}


    // Login do colaborador
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string'
        ]);

        $collaborator = Collaborator::where('email', $request->email)->first();

        if (!$collaborator || !Hash::check($request->password, $collaborator->password)) {
            return response()->json([
                'message' => 'Credenciais inválidas'
            ], 401);
        }

        $token = $collaborator->createToken('collaborator_token')->plainTextToken;

        return response()->json([
            'collaborator' => $collaborator,
            'token' => $token
        ], 200);
    }

    // Logout do colaborador
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout realizado com sucesso'
        ], 200);
    }
}
