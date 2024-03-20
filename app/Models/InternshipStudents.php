<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InternshipStudents extends Model
{
    use HasFactory;

    protected $table = 'internship_student';

    protected $fillable = [
        'internship_id',
        'student_id',
    ];
}
