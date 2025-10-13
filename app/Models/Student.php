<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $primaryKey = 'student_id';

    protected $fillable = [
        'fullname', 'program', 'year_level', 'status'
    ];

    public function sanctions() {
        return $this->hasMany(Sanction::class, 'student_id');
    }

    public function hearings()
    {
        return $this->hasMany(HearingSchedule::class, 'respondent_id', 'student_id');
    }
}