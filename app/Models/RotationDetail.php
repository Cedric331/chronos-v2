<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class RotationDetail extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'day',
        'is_off',
        'debut_journee',
        'debut_pause',
        'fin_pause',
        'fin_journee',
        'technicien',
        'teletravail',
        'rotation_id'
    ];

    public function getDebutJourneeAttribute($value)
    {
        return !empty($value) ? Carbon::createFromFormat('H:i:s', $value)->format('H\hi') : null;
    }

    public function getDebutPauseAttribute($value)
    {
        return !empty($value) ? Carbon::createFromFormat('H:i:s', $value)->format('H\hi') : null;
    }

    public function getFinPauseAttribute($value)
    {
        return !empty($value) ? Carbon::createFromFormat('H:i:s', $value)->format('H\hi') : null;
    }

    public function getFinJourneeAttribute($value)
    {
        return !empty($value) ? Carbon::createFromFormat('H:i:s', $value)->format('H\hi') : null;
    }
}
