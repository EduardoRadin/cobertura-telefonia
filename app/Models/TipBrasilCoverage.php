<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipBrasilCoverage extends Model
{
    use HasFactory;

    protected $fillable = [
        'municipio',
        'cnl_al',
        'sigla_estado',
        'cn',
        'cnl',
        'maestro',
        'area_local'
    ];

    protected $table = 'tip_brasil_coverage';
}
