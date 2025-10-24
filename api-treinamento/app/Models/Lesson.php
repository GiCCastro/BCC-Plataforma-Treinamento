<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'link',
        'course_id',
        'company_id'

    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    public function collaborators()
    {
        return $this->belongsToMany(Collaborator::class, 'lesson_collaborator')
            ->withPivot('completed', 'completed_at')
            ->withTimestamps();
    }

    public function isCompletedBy($collaboratorId)
    {
        return $this->collaborators()
            ->wherePivot('collaborator_id', $collaboratorId)
            ->wherePivot('completed', true)
            ->exists();
    }
}