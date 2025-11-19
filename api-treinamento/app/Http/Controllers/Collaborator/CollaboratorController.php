<?php

namespace App\Http\Controllers\Collaborator;

use App\Http\Controllers\Controller;


use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Question;
use App\Models\Course;
use App\Models\Track;
use App\Models\Collaborator;
use App\Http\Controllers\Award\AwardAssignController;

class CollaboratorController extends Controller
{
    public function index()
    {
        try {
            $company = auth('company')->user();

            if (!$company) {
                return response()->json(['message' => 'Empresa não autenticada'], 401);
            }

            $collaborators = $company->collaborators()->get();

            return response()->json([
                'collaborators' => $collaborators
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Erro ao listar colaboradores',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function deactivate($id)
{
    try {
        // Autentica a empresa
        $company = auth('company')->user();

        if (!$company) {
            return response()->json([
                'message' => 'Empresa não autenticada'
            ], 401);
        }

        // Busca colaborador pertencente à empresa
        $collaborator = Collaborator::where('id', $id)
            ->where('company_id', $company->id)
            ->first();

        if (!$collaborator) {
            return response()->json([
                'message' => 'Colaborador não encontrado ou não pertence à empresa'
            ], 404);
        }

        // Atualiza para inativo
        $collaborator->update([
            'is_active' => false
        ]);

        return response()->json([
            'message' => 'Colaborador desativado com sucesso',
            'collaborator' => $collaborator
        ], 200);

    } catch (Exception $e) {
        return response()->json([
            'message' => 'Erro ao desativar colaborador',
            'error' => $e->getMessage()
        ], 500);
    }
}


    public function answerQuestion(Request $request)
    {
        try {
            $validator = \Validator::make($request->all(), [
                'question_id' => 'required|exists:questions,id',
                'selected_option' => 'required|in:A,B,C,D',
            ], [
                'question_id.required' => 'A questão é obrigatória.',
                'question_id.exists' => 'A questão informada não existe.',
                'selected_option.required' => 'A opção selecionada é obrigatória.',
                'selected_option.in' => 'A opção deve ser A, B, C ou D.',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Dados inválidos',
                    'errors' => $validator->errors()
                ], 422);
            }

            $collaborator = auth('collaborator')->user();
            $question = Question::findOrFail($request->question_id);

            $isCorrect = $question->correct_option === $request->selected_option;

            $collaborator->questions()->syncWithoutDetaching([
                $question->id => [
                    'selected_option' => $request->selected_option,
                    'is_correct' => $isCorrect,
                ]
            ]);

            $lesson = $question->lesson;
            $allQuestions = $lesson->questions()->pluck('id')->toArray();
            $answeredCount = $collaborator->questions()->whereIn('question_id', $allQuestions)->count();

            if ($answeredCount === count($allQuestions)) {
                $collaborator->lessons()->syncWithoutDetaching([
                    $lesson->id => [
                        'completed' => true,
                        'completed_at' => now(),
                    ]
                ]);

                $this->updateCourseProgress($lesson->course_id, $collaborator->id);

                $tracks = $lesson->course->tracks;
                foreach ($tracks as $track) {
                    $this->updateTrackProgress($track->id, $collaborator->id);
                }

            }

            return response()->json([
                'message' => 'Resposta registrada com sucesso.',
                'correct' => $isCorrect,
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'message' => 'Erro ao responder a questão.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    private function updateCourseProgress($courseId, $collaboratorId)
    {
        $course = Course::with('lessons')->findOrFail($courseId);
        $total = $course->lessons->count();

        $completed = DB::table('lesson_collaborator')
            ->where('collaborator_id', $collaboratorId)
            ->whereIn('lesson_id', $course->lessons->pluck('id'))
            ->where('completed', true)
            ->count();

        $progress = $total > 0 ? ($completed / $total) * 100 : 0;

        DB::table('course_collaborator')->updateOrInsert(
            ['course_id' => $courseId, 'collaborator_id' => $collaboratorId],
            [
                'progress' => $progress,
                'completed' => $progress == 100,
                'completed_at' => $progress == 100 ? now() : null,
            ]
        );
    }

    private function updateTrackProgress($trackId, $collaboratorId)
    {
        $track = Track::with('courses')->findOrFail($trackId);
        $total = $track->courses->count();

        $completed = DB::table('course_collaborator')
            ->where('collaborator_id', $collaboratorId)
            ->whereIn('course_id', $track->courses->pluck('id'))
            ->where('completed', true)
            ->count();

        $progress = $total > 0 ? ($completed / $total) * 100 : 0;

        DB::table('track_collaborator')->updateOrInsert(
            ['track_id' => $trackId, 'collaborator_id' => $collaboratorId],
            [
                'progress' => $progress,
                'completed' => $progress == 100,
                'completed_at' => $progress == 100 ? now() : null,

            ]
        );

        if ($progress == 100) {
            app(AwardAssignController::class)
                ->checkAndAssignToCollaborator($collaboratorId);
        }

    }

    public function getLearning()
    {
        $collaborator = auth('collaborator')->user();

        $tracks = $collaborator->tracks()
            ->with([
                'courses.lessons' => function ($query) use ($collaborator) {
                    $query->with([
                        'questions',
                        'collaborators' => function ($q) use ($collaborator) {
                            $q->where('collaborator_id', $collaborator->id)
                                ->select('collaborator_id', 'lesson_id', 'completed', 'completed_at');
                        }
                    ]);
                }
            ])
            ->get()
            ->map(function ($track) use ($collaborator) {

                $track->progress = $track->progressFor($collaborator->id);
                $track->completed = $track->isCompletedBy($collaborator->id);

                $track->courses->each(function ($course) use ($collaborator) {

                    $course->progress = $course->progressFor($collaborator->id);
                    $course->completed = $course->isCompletedBy($collaborator->id);

                    $course->lessons->each(function ($lesson) use ($collaborator) {
                        $lesson->is_completed = $lesson->isCompletedBy($collaborator->id);
                    });
                });

                return $track;
            });

        return response()->json([
            'tracks' => $tracks
        ]);
    }

}