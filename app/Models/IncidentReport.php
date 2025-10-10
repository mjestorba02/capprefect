<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IncidentReport extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    public $incrementing = true;

    protected $fillable = [
        'incident_id',
        'student_id',
        'category_id',
        'incident_date',
        'location',
        'description',
        'reported_by',
        'action_taken',
        'status',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function category()
    {
        return $this->belongsTo(Sanction::class, 'category_id', 'sanction_id');
    }
}