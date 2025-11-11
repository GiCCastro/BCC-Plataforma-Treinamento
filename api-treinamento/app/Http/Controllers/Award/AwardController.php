<?php

namespace App\Http\Controllers\Award;

use App\Http\Controllers\Controller;
use App\Models\Award;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\DB;


class AwardController extends Controller
{
    public function register(Request $request)
    {
        try {
            $validator = \Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'description' => 'nullable|string|max:255',
                'banner' => 'nullable',
                'start_date' => 'nullable|date',
                'end_date' => 'nullable|date|after_or_equal:start_date',
                'min_progress' => 'required|integer|min:0|max:100',
                'min_accuracy' => 'required|integer|min:0|max:100',
                'active' => 'boolean',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Dados inválidos',
                    'errors' => $validator->errors()
                ], 422);
            }

            $validated = $validator->validated();

            $company = auth('company')->user();
            if (!$company) {
                return response()->json(['message' => 'Empresa não autenticada'], 401);
            }

            DB::beginTransaction();

            if ($request->hasFile('banner')) {
                $file = $request->file('banner');
                $mime = $file->getClientMimeType();
                $base64 = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($file->getRealPath()));
            } else {
                $base64 = $request->banner ?? null;
            }

            $award = Award::create([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'banner' => $base64,
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
                'min_progress' => $validated['min_progress'],
                'min_accuracy' => $validated['min_accuracy'],
                'active' => $validated['active'] ?? true,
                'company_id' => $company->id,
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Premiação criada com sucesso',
                'award' => $award
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Erro ao criar premiação',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function index()
    {
        try{
            $company = auth('company')->user();

            $awards = $company->awards()->get();

            if(!$company) {
                return response()->json(['message' => 'Empresa não autenticada'], 401);
            }

            return response()->json([
                'awards' => $awards
            ], 200);
        } catch (Exception $e){
            return response()->json([
                'message' => 'Erro ao listar premiações',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}


