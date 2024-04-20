<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientVisit extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'clientId',
        'createdBy',
        'createdTime',
        'updatedTime',
        'plannedTime',
        'startTime',
        'finishTime',
        'contactPersonId',
        'placeMeetingId',
        'placeMeetingDescription',
        'purposeOfMeeting',
        'purposeOfMeetingDescription'
    ];
}
