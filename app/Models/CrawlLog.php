<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CrawlLog extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $casts = [
        "last_modified_at" => "datetime"
    ];

    public $fillable = ['url', 'last_modified_at'];
}