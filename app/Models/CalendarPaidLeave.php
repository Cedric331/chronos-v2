<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

class CalendarPaidLeave extends Pivot
{
    use HasFactory;

    protected $fillable = [
        'calendar_id',
        'paid_leave_id',
    ];
}
