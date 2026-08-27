<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Grade extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'subject_id',
        'prelim',
        'midterm',
        'finals',
        'final_grade',
        'remarks',
        'notes',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    /**
     * Compute and assign final grade & remarks automatically before saving
     */
    public static function boot()
    {
        parent::boot();

        static::saving(function ($grade) {
            if ($grade->prelim !== null && $grade->midterm !== null && $grade->finals !== null) {
                $grade->final_grade = round(($grade->prelim * 0.3) + ($grade->midterm * 0.3) + ($grade->finals * 0.4), 2);
                $grade->remarks = ($grade->final_grade >= 75.0) ? 'Passed' : 'Failed';
            } else {
                $grade->final_grade = null;
                $grade->remarks = 'Pending';
            }
        });
    }
}
