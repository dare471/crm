<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientCrop extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'clientId',
        'area',
        'unit',
        'culture',
        'cultureId',
        'activitySubstance',
    ];
}
