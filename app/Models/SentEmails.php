<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SentEmails extends Model
{
    use HasFactory;
 
    protected $fillable = [
        'id',
        'orderGuid',
        'order',
        'clientName',
        'iinBin',
        'dateStatus',
        'tel',
        'email',
        'type',
        'sent',
        'created_at',
        'updated_at'
    ];
}
