<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Collaborator extends Authenticatable
{
    use HasFactory;

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

    public function departments(){
        return $this->belongsToMany(Department::class, 'collaborator_department');
    }

    public function setPasswordAttribute($value){
        $this->attributes['password'] = bcrypt($value);
    }

}
