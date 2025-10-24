<?php

namespace App\Http\Controllers\Course;

use App\Http\Controllers\Controller;

use App\Models\Course;
use App\Models\Lesson;
use App\Models\Question;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CourseController extends Controller
{
    public function register(Request $request)
    {
        try {

            $validator = \Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'banner' => 'nullable',
                'lessons' => 'required',
                'lessons.*.name' => 'required|string|max:255',
                'lessons.*.description' => 'nullable|string',
                'lessons.*.link' => 'required|string',
                'lessons.*.questions' => 'required|array',
                'lessons.*.questions.*.question_text' => 'required|string',
                'lessons.*.questions.*.option_a' => 'required|string',
                'lessons.*.questions.*.option_b' => 'required|string',
                'lessons.*.questions.*.correct_option' => 'required|string|in:A,B,C,D',
            ]);

            DB::beginTransaction();


            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Dados inválidos',
                    'errors' => $validator->errors()
                ], 422);
            }

            $company = auth('company')->user();
            if (!$company) {
                return response()->json(['message' => 'Empresa não autenticada'], 401);
            }
            if ($request->hasFile('banner')) {
                $file = $request->file('banner');
                $mime = $file->getClientMimeType();
                $base64 = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($file->getRealPath()));
            } else {
                $base64 = $request->banner ?? null;
            }


            $course = Course::create([
                'name' => $request->name,
                'description' => $request['description'] ?? null,
                'banner' => $base64,
                'company_id' => $company->id,
            ]);

            $lessons = json_decode($request->lessons, true);

            if (!is_array($lessons)) {
                return response()->json([
                    'message' => 'Campo lessons inválido'
                ], 422);
            }

            foreach ($lessons as $lessonData) {
                $lesson = Lesson::create([
                    'name' => $lessonData['name'],
                    'description' => $lessonData['description'] ?? null,
                    'link' => $lessonData['link'] ?? null,
                    'course_id' => $course->id,
                    'company_id' => $company->id,
                ]);

                if (!empty($lessonData['questions'])) {
                    foreach ($lessonData['questions'] as $questionData) {
                        Question::create([
                            'lesson_id' => $lesson->id,
                            'question_text' => $questionData['question_text'],
                            'option_a' => $questionData['option_a'],
                            'option_b' => $questionData['option_b'],
                            'option_c' => $questionData['option_c'] ?? null,
                            'option_d' => $questionData['option_d'] ?? null,
                            'correct_option' => $questionData['correct_option'],
                            'company_id' => $company->id,
                        ]);
                    }
                }
            }

            DB::commit();


            return response()->json([
                'message' => 'Curso criado com sucesso',
                'data' => $course->load('lessons.questions')
            ], 201);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Erro ao criar curso',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function index()
    {
        try {
            $company = auth('company')->user();

            if (!$company) {
                return response()->json(['message' => 'Empresa não autenticada'], 401);
            }

            $courses = $company->courses()->get();

            return response()->json([
                'courses' => $courses
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Erro ao listar cursos',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}