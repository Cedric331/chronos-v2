<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Team extends Model
{
    use LogsActivity;


    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'logo',
        'departement',
        'code_departement',
        'team_params_id',
        'company_id',
        'user_id',
    ];

    protected $appends = ['logo_url'];

    protected $with = ['params'];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['*']);
    }

    public function users(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(User::class);
    }

    public function getLogoUrlAttribute(): ?string
    {
        if ($this->logo) {
            return asset('storage/'.$this->logo);
        }

        return null;
    }

    public function params(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(TeamParams::class, 'team_params_id');
    }

    public function rotations(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Rotation::class);
    }

    public function teamSchedules(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(TeamSchedule::class);
    }

    public function moduleAlertActive()
    {
        return $this->params->module_alert;
    }

    public function alerts(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(AlertSchedule::class);
    }

    public function links(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(LinkTeam::class);
    }
}
