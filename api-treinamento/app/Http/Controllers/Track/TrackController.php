<?php

namespace App\Http\Controllers\Track;

use App\Http\Controllers\Controller;

use App\Models\Track;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TrackController extends Controller
{
    public function register(Request $request)
    {
        try {

            $validator = \Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'banner' => 'nullable|string',
                'departments' => 'required|array',
            ], [
                'departments.*.exists' => 'Um ou mais departamentos informados não existem',
            ]);

             if ($validator->fails()) {
            return response()->json([
                'message' => 'Dados inválidos',
                'errors' => $validator->errors()
            ], 422);
        }


            DB::beginTransaction();

            $company = auth('company')->user();
            if (!$company) {
                return response()->json(['message' => 'Empresa não autenticada'], 401);
            }

              if ($request->hasFile('banner')) {
                $file = $request->file('banner');
                $mime = $file->getClientMimeType();
                $base64 = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($file->getRealPath()));
            } else {
                $base64 = $validated['banner'] ?? null;
            }


            $validDepartments = $company->departments()->pluck('id')->toArray();
            $invalidDepartments = array_diff($request->departments, $validDepartments);

            if (count($invalidDepartments)) {
                return response()->json([
                    'message' => 'Um ou mais departamentos não pertencem à empresa',
                    'invalid_departments' => array_values($invalidDepartments)
                ], 422);
            }

            $track = Track::create([
                'name' => $request->name,
                'description' => $request->description ?? null,
                'banner' => $base64,
                'company_id' => $company->id,
            ]);

            $track->departments()->sync($request->departments);

            DB::commit();

            return response()->json([
                'message' => 'Departamento registrado com sucesso!',
                'department' => $track
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Erro ao criar trilha',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}