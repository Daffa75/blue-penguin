<?php

namespace App\Models\Curriculum;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CurriculumStructure extends Model
{
    use HasFactory;

    protected $table = 'curriculum_structures';

    protected $fillable = [
        'curriculum_name',
        'language',
    ];

    /**
     * Get the semesters associated with the curriculum structure.
     */
    public function semester(): HasMany
    {
        return $this->hasMany(Semester::class, 'curriculum_id');
    }
}
