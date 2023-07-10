<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeamParams extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'module_alert',
        'update_planning',
        'share_link_planning',
        'share_link',
        'type_day',
        'teams',
    ];

    public function getTypeDayAttribute($value)
    {
        return json_decode($value);
    }
}
