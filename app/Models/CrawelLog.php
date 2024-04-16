<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CrawelLog extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $casts = [
        "last_modified_at" => "datetime"
    ];

    public $fillable = ['url', 'last_modified_at'];
}