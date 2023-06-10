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

    public function setDebutJourneeAttribute($value)
    {
        $this->attributes['debut_journee'] = date('H:i:s', strtotime(str_replace('h', ':', $value)));
    }

    public function setDebutPauseAttribute($value)
    {
        $this->attributes['debut_pause'] = date('H:i:s', strtotime(str_replace('h', ':', $value)));
    }

    public function setFinPauseAttribute($value)
    {
        $this->attributes['fin_pause'] = date('H:i:s', strtotime(str_replace('h', ':', $value)));
    }

    public function setFinJourneeAttribute($value)
    {
        $this->attributes['fin_journee'] = date('H:i:s', strtotime(str_replace('h', ':', $value)));
    }

    public function getFinPauseAttribute($value): ?string
    {
        if ($value) {
            return Carbon::parse($value)->format('H\hi');
        }
        return null;
    }

    public function getFinJourneeAttribute($value): ?string
    {
        if ($value) {
            return Carbon::parse($value)->format('H\hi');
        }
        return null;
    }

    public function getDebutJourneeAttribute($value): ?string
    {
        if ($value) {
            return Carbon::parse($value)->format('H\hi');
        }
        return null;
    }

    public function getDebutPauseAttribute($value): ?string
    {
        if ($value) {
            return Carbon::parse($value)->format('H\hi');
        }
        return null;
    }

    public function calendar (): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Calendar::class);
    }
}
