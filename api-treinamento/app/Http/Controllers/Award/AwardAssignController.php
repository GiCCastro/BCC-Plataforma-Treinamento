<?php

namespace App\Http\Controllers\Award;

use App\Http\Controllers\Controller;
use App\Models\Award;
use App\Models\Collaborator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AwardAssignController extends Controller
{
    public function checkAndAssignToCollaborator($collaboratorId)
    {
        try {
            $collaborator = Collaborator::findOrFail($collaboratorId);
            $company = $collaborator->company;

            $awards = Award::where('company_id', $company->id)->get();

            $progress = DB::table('track_collaborator')
                ->where('collaborator_id', $collaboratorId)
                ->avg('progress') ?? 0;

            $totalQuestions = DB::table('collaborator_question')
                ->where('collaborator_id', $collaboratorId)
                ->count();

            $correctAnswers = DB::table('collaborator_question')
                ->where('collaborator_id', $collaboratorId)
                ->where('is_correct', true)
                ->count();

            $accuracy = $totalQuestions > 0 ? ($correctAnswers / $totalQuestions) * 100 : 0;

            foreach ($awards as $award) {
                $meetsProgress = $progress >= ($award->min_progress ?? 100);
                $meetsAccuracy = $accuracy >= ($award->min_accuracy ?? 90);

                if ($meetsProgress && $meetsAccuracy) {
                    $alreadyAwarded = $collaborator->awards()
                        ->where('award_id', $award->id)
                        ->exists();

                    if (!$alreadyAwarded) {
                        $collaborator->awards()->attach($award->id, [
                            'achieved_at' => now(),
                        ]);

                        Log::info("Premiação '{$award->name}' atribuída automaticamente a {$collaborator->name}");
                    }
                }
            }

        } catch (\Exception $e) {
            Log::error('Erro ao aplicar premiação automática: ' . $e->getMessage());
        }
    }
}
