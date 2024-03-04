<?php

namespace App\Models\Curriculum;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Semester extends Model
{
    use HasFactory;

    protected $table = 'curriculum_structures_semesters';

    protected $fillable = [
        'curriculum_id',
        'semester_name',
        'credit_total',
    ];

    /**
     * Get the curriulum structure that owns the semester.
     */
    public function curriculumstructure(): BelongsTo
    {
        return $this->belongsTo(CurriculumStructure::class, 'curriculum_id');
    }

    /**
     * Get the modules associated with the semester.
     */
    public function modules(): HasMany
    {
        return $this->hasMany(Module::class);
    }
}
