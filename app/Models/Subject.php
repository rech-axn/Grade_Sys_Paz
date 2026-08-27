<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    use HasFactory;

    protected $fillable = [
        'subject_code',
        'subject_title',
        'units',
        'semester',
        'academic_year',
    ];

    public function grades()
    {
        return $this->hasMany(Grade::class);
    }

    public function getAverageGradeAttribute(): ?float
    {
        $avg = $this->grades()->whereNotNull('final_grade')->avg('final_grade');
        return $avg ? round($avg, 2) : null;
    }
}
