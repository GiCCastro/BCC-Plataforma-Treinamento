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
        DB::beginTransaction();

        try {
            $validator = \Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'banner' => 'nullable',
                'courses' => 'required|string',
                'departments' => 'required|string',
            ], [
                'courses.required' => 'É necessário informar pelo menos um curso.',
                'departments.required' => 'É necessário informar pelo menos um departamento.',
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

            if ($request->hasFile('banner')) {
                $file = $request->file('banner');
                $mime = $file->getClientMimeType();
                $base64 = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($file->getRealPath()));
            } else {
                $base64 = $validated['banner'] ?? null;
            }

            $courses = json_decode($request->courses, true);
            $departments = json_decode($request->departments, true);

            if (!is_array($courses)) {
                return response()->json(['message' => 'Campo "courses" inválido'], 422);
            }

            if (!is_array($departments)) {
                return response()->json(['message' => 'Campo "departments" inválido'], 422);
            }

            $validCourses = $company->courses()->pluck('id')->toArray();
            $invalidCourses = array_diff($courses, $validCourses);

            $validDepartments = $company->departments()->pluck('id')->toArray();
            $invalidDepartments = array_diff($departments, $validDepartments);

            if (count($invalidCourses)) {
                return response()->json([
                    'message' => 'Um ou mais cursos não pertencem à empresa',
                    'invalid_courses' => array_values($invalidCourses)
                ], 422);
            }

            if (count($invalidDepartments)) {
                return response()->json([
                    'message' => 'Um ou mais departamentos não pertencem à empresa',
                    'invalid_departments' => array_values($invalidDepartments)
                ], 422);
            }

            $track = Track::create([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'banner' => $base64,
                'company_id' => $company->id,
            ]);

            $track->courses()->sync($courses);
            $track->departments()->sync($departments);

            DB::commit();

            return response()->json([
                'message' => 'Trilha registrada com sucesso!',
                'track' => $track->load('courses', 'departments')
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Erro ao criar trilha',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function index()
    {
        $company = auth('company')->user();

        $tracks = Track::where('company_id', $company->id)
            ->with([
                'courses.lessons.questions'
            ])
            ->get();

        if ($tracks->isEmpty()) {
            return response()->json(['message' => 'Nenhuma trilha encontrada'], 404);
        }

        return response()->json($tracks);

    }
}
