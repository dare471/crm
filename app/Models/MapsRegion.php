<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MapsRegion extends Model
{
    use HasFactory;
    protected $connection = 'AA_DWH_X';
}
