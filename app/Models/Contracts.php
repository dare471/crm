<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contracts extends Model
{
    use HasFactory;
    protected $fillable = [
        'ContractGuid',
        'OrganizationName',
        'ContragentName',
        'ManagerGuid',
        'RegionGuid',
        'SeasonGuid',
        'WarehouseGuid',
        'Currency'
    ];
}
