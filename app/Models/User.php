<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Model;

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
        'last_invitation',
        'avatar',
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

    protected $appends = ['hasPlanning', 'role', 'hasAccessAdmin', 'CanResendInvitation'];

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
        return $this->hasRole(['Coordinateur', 'Responsable', 'Administrateur']);
    }

    public function isResponsable(): bool
    {
        return $this->hasRole(['Responsable', 'Administrateur']);
    }

    public function isAdmin(): bool
    {
        return $this->hasRole(['Administrateur']) || $this->hasPermissionTo('access-admin');
    }

    public function isAdministrateur(): bool
    {
        return $this->hasRole(['Administrateur']);
    }

    public function getHasAccessAdminAttribute (): bool
    {
        return $this->hasPermissionTo('access-admin');
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

    public function getRoleAttribute ()
    {
        return $this->getRoleNames()->first();
    }

    public function getCanResendInvitationAttribute()
    {
        if ($this->last_invitation) {
            $date = Carbon::parse($this->last_invitation);
            return now() > $date->addHours(48) && !$this->isActivated();
        } else if (!$this->isActivated()) {
            return true;
        } else {
            return false;
        }
    }

    public function paidleaves(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(PaidLeave::class);
    }

    public function daysPaidAccepted()
    {
        return $this->paidleaves()->where('status', PaidLeave::STATUS_ACCEPTED)
            ->whereHas('calendars', function ($query) {
                $startDate = Carbon::createFromDate(date('Y'), 6, 1);
                $endDate = Carbon::createFromDate(date('Y'), 5, 31);
                $endDate->addYear();
                $query->where(function ($subQuery) use ($startDate, $endDate) {
                    $subQuery->where('date', '>=', $startDate)
                        ->where('date', '<=', $endDate);
                });
            });
    }
}
