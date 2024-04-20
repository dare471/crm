<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientContact extends Model
{
    use HasFactory; 

    protected $fillable = [
        'id',
        'clientId',
        'createdBy',
        'createdTime',
        'updateTime',
        'name',
        'lastName',
        'position',
        'tel',
        'email'
    ];
}
