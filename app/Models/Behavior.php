<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Behavior extends Model
{
    use HasFactory;

    protected $fillable = [
        'behavior_id',
        'student_id',
        'date_recorded',
        'behavior_type',
        'behavior_category',
        'description',
        'recorded_by',
        'action_taken',
        'points',
        'status',
    ];

    // Optional relationship
    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id', 'student_id');
    }
}