<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'student_id_number',
        'course_section',
        'year_level',
        'gender',
        'contact_number',
        'address',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function grades()
    {
        return $this->hasMany(Grade::class);
    }

    /**
     * Compute General Weighted Average (GWA)
     */
    public function getGwaAttribute(): float
    {
        $grades = $this->grades()->with('subject')->get();
        $totalUnits = 0;
        $weightedSum = 0;

        foreach ($grades as $grade) {
            if ($grade->final_grade !== null && $grade->subject) {
                $units = $grade->subject->units ?? 3;
                $totalUnits += $units;
                $weightedSum += ($grade->final_grade * $units);
            }
        }

        return $totalUnits > 0 ? round($weightedSum / $totalUnits, 2) : 0.0;
    }

    /**
     * Total Enrolled Units
     */
    public function getTotalUnitsAttribute(): int
    {
        return (int) $this->grades()->join('subjects', 'grades.subject_id', '=', 'subjects.id')->sum('subjects.units');
    }

    /**
     * Total Passed Units
     */
    public function getPassedUnitsAttribute(): int
    {
        return (int) $this->grades()
            ->where('remarks', 'Passed')
            ->join('subjects', 'grades.subject_id', '=', 'subjects.id')
            ->sum('subjects.units');
    }

    /**
     * Academic Standing
     */
    public function getAcademicStatusAttribute(): string
    {
        $gwa = $this->gwa;
        if ($gwa >= 88.0) return "Dean's Honor List";
        if ($gwa >= 75.0) return "In Good Standing";
        if ($gwa > 0) return "Academic Review";
        return "Ongoing Term";
    }
}
