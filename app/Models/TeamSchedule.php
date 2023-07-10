<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeamSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'day',
        'time',
        'value',
        'team_id',
    ];
}
