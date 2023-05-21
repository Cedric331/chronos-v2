<?php

namespace App\Models;

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
}
