<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InternshipLogbook extends Model
{
    protected $table = 'internships_logbook';

    use HasFactory;

    protected $fillable = [
        'internship_id',
        'student_id',
        'date',
        'activity',
        'result',
        'feedback',
        'status',
    ];

    public function internship()
    {
        return $this->belongsTo(Internship::class, 'internship_id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }
}
