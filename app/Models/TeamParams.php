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
        'send_coordinateur',
        'module_alert',
        'update_planning',
        'share_link_planning',
        'share_link',
        'type_day',
        'color1',
        'color2',
        'color3',
        'color4'
    ];

    public function team(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Team::class, 'team_params_id');
    }


    public function getTypeDayAttribute($value)
    {
        return json_decode($value);
    }
}
