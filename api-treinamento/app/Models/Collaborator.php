<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;

class Collaborator extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'cpf',
        'password',
        'birth_date',
        'photo',
        'is_active',
        'company_id'
    ];

    protected $hidden = [
        'password',
        'remember_token'
    ];


    protected $casts = [
        'birth_date' => 'date',
        'email_verified_at' => 'datetime',
        'is_active' => 'boolean'
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function departments()
    {
        return $this->belongsToMany(Department::class, 'collaborator_department');
    }

    public function lessons()
    {
        return $this->belongsToMany(Lesson::class, 'lesson_collaborator')
            ->withPivot('completed', 'completed_at')
            ->withTimestamps();
    }

    public function questions(){
        return $this->belongsToMany(Question::class, 'collaborator_question')
        ->withPivot('selected_option', 'is_correct')
        ->withTimestamps();
    }

    public function hasAnsweredCorrestly($questionId){
        return $this->questions()
                    ->wherePivot('question_id', $questionId)
                    ->wherePivot('is_correct', true)
                    ->exists();
    }

    public function courses(){
        return $this->belongsToMany(Course::class,'course_collaborator')
                    ->withPivot('progress','completed', 'completed_at')
                    ->withTimestamps();
    }

    public function tracks(){
        return $this->belongsToMany(Track::class, 'track_collaborator')
                    ->withPivot('progress', 'completed', 'completed_at')
                    ->withTimestamps();

    }

    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = bcrypt($value);
    }

}
