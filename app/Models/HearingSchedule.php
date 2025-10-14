<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Student;
use App\Models\Violation;

class HearingSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'record_no',
        'respondent_id',
        'violation_id',
        'complainant',
        'offense',
        'date_of_hearing',
        'time',
        'venue',
        'officer_panel',
        'status',
    ];

    public function respondent()
    {
        return $this->belongsTo(Student::class, 'respondent_id', 'student_id');
    }

    public function violation()
    {
        return $this->belongsTo(ViolationCategory::class, 'violation_id', 'id');
    }
}