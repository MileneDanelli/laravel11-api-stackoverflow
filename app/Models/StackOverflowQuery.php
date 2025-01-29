<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StackOverflowQuery extends Model
{
    use HasFactory;

    protected $table = 'stackoverflow_queries';

    protected $fillable = [
        'query',
        'response',
    ];
}
