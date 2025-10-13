<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ViolationCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'category_name',
        'description',
        'severity_level',
        'default_sanction',
        'status',
    ];

    public function hearings()
    {
        return $this->hasMany(HearingSchedule::class, 'violation_id', 'id');
    }
}