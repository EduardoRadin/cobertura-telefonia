<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LigueAiCoverage extends Model
{
    use HasFactory;

    protected $fillable = [
        'uf',
        'cn',
        'municipio'
    ];

    protected $table = 'ligue_ai_coverage';
}
