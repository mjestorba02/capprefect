<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClearanceHold extends Model
{
    protected $fillable = [
        'student_id', 'department', 'reason', 'date_flagged',
        'status', 'cleared_date', 'cleared_by'
    ];

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id', 'student_id');
    }
}