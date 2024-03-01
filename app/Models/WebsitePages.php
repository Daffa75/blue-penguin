<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WebsitePages extends Model
{
    use HasFactory;

    protected $table = 'website_pages';
    protected $primaryKey = 'page_id';
    public $incrementing = true;

    protected $fillable = [
        'page',
        'content',
        'language',
    ];
}
