<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActsMS extends Model
{
    use HasFactory;
    protected $connection = 'AA_DWH_X';
    
}
