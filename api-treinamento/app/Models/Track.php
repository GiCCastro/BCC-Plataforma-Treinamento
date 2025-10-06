<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Track extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'banner',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }


    public function courses()
    {
        return $this->belongsToMany(Course::class, 'course_track')
            ->withTimestamps();
    }


    public function departments()
    {
        return $this->belongsToMany(Department::class, 'track_department')
            ->withTimestamps();
    }


    public function collaborators()
    {
        return $this->belongsToMany(Collaborator::class, 'track_collaborator')
            ->withPivot('progress', 'completed', 'completed_at')
            ->withTimestamps();
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
