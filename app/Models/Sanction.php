<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sanction extends Model
{
    use HasFactory;

    protected $primaryKey = 'sanction_id';

    protected $fillable = [
        'student_id', 'offense', 'date_issued', 'sanction_type', 'severity', 'status'
    ];

    public function student() {
        return $this->belongsTo(Student::class, 'student_id');
    }
}