<?php


namespace App\Http\Controllers\Award;

use App\Http\Controllers\Controller;

class AwardCollaboratorController extends Controller
{
    public function index()
    {
        $collaborator = auth('collaborator')->user();

        if (!$collaborator) {
            return response()->json(['message' => 'Colaborador não autenticado'], 401);
        }

        $awards = $collaborator->awards()
            ->withPivot('achieved_at')
            ->orderByDesc('collaborator_award.achieved_at')
            ->get();

        return response()->json([
            'collaborator' => $collaborator->name,
            'awards' => $awards
        ]);
    }
}
