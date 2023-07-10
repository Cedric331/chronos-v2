<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AlertSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'team_id',
        'message',
        'time',
        'date',
        'is_read',
    ];

    public function markAsRead()
    {
        $this->is_read = true;
        $this->save();
    }
}
