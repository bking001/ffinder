<?php

namespace App\Models;

 
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AllOntStatsModel extends Model
{
    use HasFactory;

    protected $table = 'AllStatsResult';

    protected $fill = [
        'id',
        'olt',
        'Type',
        'device_name',
        'descr',
        'ponPort',
        'onuMac',
        'onuStatus',
        'reason',
        'distance',
        'dbmRX',
        'last_update'
    ];

 
}
