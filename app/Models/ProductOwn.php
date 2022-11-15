<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductOwn extends Model
{
    use HasFactory;
    protected $fillable = [
        'id',
        'product',
        'product_inf',
        'product_link'
    ];
}
