<?php

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Company extends Authenticatable
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
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
}