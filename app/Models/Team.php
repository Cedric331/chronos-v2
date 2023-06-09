<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    use HasFactory;

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
        'user_id'
    ];

    protected $appends = ['logo_url'];
    protected $with = ['params'];

    public function users (): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(User::class);
    }

    public function getLogoUrlAttribute(): ?string
    {
        if ($this->logo) {
            return asset('storage/' . $this->logo);
        }
        return null;
    }

    public function params (): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(TeamParams::class, 'team_params_id');
    }

    public function rotations (): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Rotation::class);
    }
}
