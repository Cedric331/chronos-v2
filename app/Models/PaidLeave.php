<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaidLeave extends Model
{
    use HasFactory;

    protected $fillable = [
        'status',
        'type',
        'comment',
        'user_id',
        'team_id'
    ];

    protected $appends = [
        'dates',
        'number_days'
    ];

    public function calendars()
    {
        return $this->belongsToMany(Calendar::class, 'calendar_paid_leaves');
    }

    public function getNumberDaysAttribute()
    {
        return $this->calendars->count();
    }

    public function getDatesAttribute()
    {
        $collection = collect();
        $calendars = $this->calendars()->get();
        foreach ($calendars as $calendar) {
            $collection->push($calendar->date_fr);
        }
        return $collection;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }
}
