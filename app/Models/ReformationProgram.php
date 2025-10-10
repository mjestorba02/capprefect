<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReformationProgram extends Model
{
    use HasFactory;

    protected $fillable = [
        'program_id',
        'program_name',
        'description',
        'duration',
        'responsible_office',
        'type',
        'status',
    ];
}