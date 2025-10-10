<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Infraction extends Model
{
    protected $fillable = [
        'student_id', 'datetime', 'violation_category', 'description',
        'severity', 'reported_by', 'sanction_assigned', 'parent_notified', 'status'
    ];

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id', 'student_id');
    }
}