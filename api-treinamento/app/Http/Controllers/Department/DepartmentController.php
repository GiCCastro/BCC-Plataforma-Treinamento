<?php

namespace App\Http\Controllers\Department;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use App\Models\Department;

class DepartmentController extends Controller
{
    public function register(Request $request)
    {
        try {

        $validator = \Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:255',
        ]);

         if ($validator->fails()) {
            return response()->json([
                'message' => 'Dados inválidos',
                'errors' => $validator->errors()
            ], 422);
        }

            $company = $request->user();

            $department = Department::create([
                'name' => $request->name,
                'description' => $request->description,
                'company_id'=> $company->id
            ]);

            return response()->json([
                'message' => 'Departamento registrado com sucesso!',
                'department' => $department
            ], 200);
        } catch (Exception $e){
            return response()->json([
                'message' => 'Erro ao registrar departamento'
            ], 500);
        }

    }

    public function index()
    {
        try {
            $company = auth('company')->user();

            $departments = $company->departments()->get();

            if (!$company) {
                return response()->json(['message' => 'Empresa não autenticada'], 401);
            }

            return response()->json([
                'departments' => $departments
            ], 200);
        } catch (Exception $e){
            return response()->json([
                'message' => 'Erro ao listar departamentos',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

