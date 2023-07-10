<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'birthday',
        'google_id',
        'phone',
        'account_active',
        'company_id',
        'team_id',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $with = ['team'];

    protected $appends = ['hasPlanning'];

    public function plannings()
    {
        return $this->hasMany(Planning::class);
    }

    public function team(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function isCoordinateur(): bool
    {
        return $this->hasRole(['Coordinateur', 'Responsable', 'Admin']);
    }

    public function isResponsable(): bool
    {
        return $this->hasRole(['Responsable', 'Admin']);
    }

    public function isAdmin(): bool
    {
        return $this->hasRole(['Admin']);
    }

    public function isActivated(): bool
    {
        return $this->account_active;
    }

    public function getLinks(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ShareLink::class);
    }

    public function getHasPlanningAttribute(): bool
    {
        return $this->plannings()->count() > 0;
    }
}
