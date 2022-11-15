<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Analyses extends Model
{
    use HasFactory;
    protected $fillable = [
        'provider',
        'usagearea',
        'cult_1',
        'cult_1_area',
        'cult_2',
        'cult_2_area',
        'cult_3',
        'cult_3_area',
    ];

    /* protected $fillable = [
        'name':'$provider',
        'usageare':'$usageare',
        'data':{
            'top':{
                'top':{
                    'cult':'$cult1',
                    'are':'$cult_1_area' 
                },
                'top2':{
                    'cult':'$cult2',
                    'are':'$cult_2_area'
                },
                'top3':{
                    'cult':'$cult3',
                    'are':'$cult_3_area'
                }
            }
        }
    ]; */
}
