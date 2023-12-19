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
        'zone',
    ];

    protected $appends = ['date_fr', 'date_fr_full'];

    public function getDay(): string
    {
        $explode = explode(' ', $this->date);

        return $explode[0];
    }

    public function plannings()
    {
        return $this->hasMany(Planning::class);
    }

    public function getDateFrAttribute(): ?string
    {
        if ($this->date) {
            Carbon::setLocale('fr');
            $date = Carbon::parse($this->date);

            return ucwords($date->isoFormat('dddd D MMMM'));
        } else {
            return null;
        }
    }

    public function getDateFrFullAttribute(): ?string
    {
        if ($this->date) {
            Carbon::setLocale('fr');
            $date = Carbon::parse($this->date);

            return ucwords($date->isoFormat('dddd D MMMM YYYY'));
        } else {
            return null;
        }
    }

    public function paidLeaves()
    {
        return $this->belongsToMany(PaidLeave::class, 'calendar_paid_leaves');
    }
}
