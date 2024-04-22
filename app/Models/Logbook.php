<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Logbook extends Model
{
    use HasFactory;

    protected $table = 'logbooks';

    protected $guarded = [];

    public function internship(): BelongsTo
    {
        return $this->belongsTo(Internship::class);
    }

    public function finalProject(): BelongsTo
    {
        return $this->belongsTo(FinalProject::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function commentable(): MorphTo
    {
        return $this->morphTo();
    }
}
