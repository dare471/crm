<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserOld extends Model
{
    use HasFactory;
    protected $fillable = [
        'serverdata_name',
        'name',
        'last_name',
        'email',
        'gender',
        'birth_date',
        'age',
        'start_date',
        'end_date',
        'status',
        'experience',
        'orgstr_id',
        'region_id',
        'devision+od',
        'access_id'
    ];
}
