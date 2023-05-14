<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Calendar extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'date',
        'is_holiday',
        'name_holiday',
        'zone'
    ];

    public function getDateAttribute($value)
    {
        Carbon::setLocale('fr');
        $date = Carbon::parse($value);
        return ucwords($date->isoFormat('dddd D MMMM'));
    }

}
