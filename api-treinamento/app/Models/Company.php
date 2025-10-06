<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Company extends Authenticatable
{

    use HasFactory, Notifiable, HasApiTokens;

    protected $fillable = [
        'name',
        'email',
        'password',
        'cnpj',
        'cnae',
        'logo',
        'primary_color',
        'secondary_color',
        'text_color',
        'button_color',
        'banner',
        'font',
    ];

    protected $hidden = [
        'password',
        'remember_token'
    ];

    public function collaborators()
    {
        return $this->hasMany(Collaborator::class);
    }

    public function departments()
    {
        return $this->hasMany(Department::class);
    }

    public function tracks()
    {
        return $this->hasMany(Track::class);
    }

    public function courses()
    {
        return $this->hasMany(Course::class);
    }

    public function lessons()
    {
        return $this->hasMany(Lesson::class);
    }

    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = bcrypt($value);
    }


}