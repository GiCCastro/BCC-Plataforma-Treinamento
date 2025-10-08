<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'lesson_id',
        'question_text',
        'option_a',
        'option_b',
        'option_c',
        'option_d',
        'correct_option',
        'company_id'

    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }


    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }

    public function collaborators()
    {
        return $this->belongsToMany(Collaborator::class, 'collaborator_question')
            ->withPivot('selected_option', 'is_correct')
            ->withTimestamps();
    }

    public function isCorrectFor($collaboratorId)
    {
        return $this->collaborators()
            ->wherePivot('collaborator_id', $collaboratorId)
            ->wherePivot('is_correct', true)
            ->exists();
    }
}