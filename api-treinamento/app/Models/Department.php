<?php

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
    ];

    public function collaborators(){
        return $this->belongsToMany(Collaborator::class, 'collaborator_department');
    }
}