<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Award extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'banner',
        'start_date',
        'end_date',
        'min_progress',
        'min_accuracy',
        'active',
        'company_id',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function collaborators()
    {
        return $this->belongsToMany(Collaborator::class, 'collaborator_award')
                    ->withPivot('achieved_at')
                    ->withTimestamps();
    }
}
