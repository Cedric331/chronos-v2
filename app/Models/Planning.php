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
        'user_id'
    ];

    public function getFinPauseAttribute($value)
    {
        return Carbon::parse($value)->format('H\hi');
    }

    public function getFinJourneeAttribute($value)
    {
        return Carbon::parse($value)->format('H\hi');
    }

    public function getDebutJourneeAttribute($value)
    {
        return Carbon::parse($value)->format('H\hi');
    }

    public function getDebutPauseAttribute($value)
    {
        return Carbon::parse($value)->format('H\hi');
    }

    public function calendar ()
    {
        return $this->belongsTo(Calendar::class);
    }
}
