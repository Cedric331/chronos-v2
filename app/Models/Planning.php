<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Planning extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'type_day',
        'debut_journee',
        'debut_pause',
        'fin_pause',
        'fin_journee',
        'is_technician',
        'telework',
        'calendar_id',
        'rotation_id',
        'team_id',
        'user_id'
    ];

    public function getFinPauseAttribute($value)
    {
        if ($value) {
            return Carbon::parse($value)->format('H\hi');
        }
        return null;    }

    public function getFinJourneeAttribute($value)
    {
        if ($value) {
            return Carbon::parse($value)->format('H\hi');
        }
        return null;
    }

    public function getDebutJourneeAttribute($value)
    {
        if ($value) {
            return Carbon::parse($value)->format('H\hi');
        }
        return null;
    }

    public function getDebutPauseAttribute($value)
    {
        if ($value) {
            return Carbon::parse($value)->format('H\hi');
        }
        return null;
    }

    public function calendar ()
    {
        return $this->belongsTo(Calendar::class);
    }
}
