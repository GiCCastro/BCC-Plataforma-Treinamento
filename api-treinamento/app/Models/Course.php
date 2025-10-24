<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'banner',
        'company_id'

    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function tracks()
    {
        return $this->belongsToMany(Track::class, 'course_track')
            ->withTimestamps();
    }


    public function collaborators()
    {
        return $this->belongsToMany(Collaborator::class, 'course_collaborator')
            ->withPivot('progress', 'completed', 'completed_at')
            ->withTimestamps();
    }

    public function lessons()
    {
        return $this->hasMany(Lesson::class);
    }

    public function isCompletedBy($collaboratorId)
    {
        return $this->collaborators()
            ->wherePivot('collaborator_id', $collaboratorId)
            ->wherePivot('completed', true)
            ->exists();
    }

    public function progressFor($collaboratorId)
    {
        $pivot = $this->collaborators()->wherePivot('collaborator_id', $collaboratorId)->first();
        return $pivot ? $pivot->pivot->progress : 0;
    }
}