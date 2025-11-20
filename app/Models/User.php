<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Carbon\Carbon;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser
{
    use HasApiTokens, HasFactory, HasRoles, Notifiable;

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
        'last_invitation' => 'datetime',
    ];

    protected $with = ['team'];

    protected $appends = ['hasPlanning', 'role', 'hasAccessAdmin', 'CanResendInvitation'];

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->isAdmin();
    }

    public function plannings()
    {
        return $this->hasMany(Planning::class);
    }

    /**
     * Get the exchange requests initiated by this user.
     */
    public function sentExchangeRequests()
    {
        return $this->hasMany(ExchangeRequest::class, 'requester_id');
    }

    /**
     * Get the exchange requests received by this user.
     */
    public function receivedExchangeRequests()
    {
        return $this->hasMany(ExchangeRequest::class, 'requested_id');
    }

    /**
     * Get the exchange requests approved by this user (as a coordinator).
     */
    public function approvedExchangeRequests()
    {
        return $this->hasMany(ExchangeRequest::class, 'approved_by');
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

    public function getHasAccessAdminAttribute(): bool
    {
        return $this->hasPermissionTo('access-admin');
    }

    public function isActivated()
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

    public function getRoleAttribute()
    {
        return $this->getRoleNames()->first();
    }

    public function getAvatarAttribute($value): string
    {
        if ($value) {
            return $value;
        } else {
            return '/images/avatar_default.png';
        }
    }

    public function getCanResendInvitationAttribute()
    {
        if ($this->last_invitation) {
            $date = Carbon::parse($this->last_invitation);
            $dateWithOneHour = $date->copy()->addHours(1);

            return now() > $dateWithOneHour && ! $this->isActivated();
        } elseif (! $this->isActivated()) {
            return true;
        } else {
            return false;
        }
    }

    public function paidleaves(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(PaidLeave::class);
    }

    /**
     * Get the user's preferences.
     */
    public function preference()
    {
        return $this->hasOne(UserPreference::class);
    }

    public function daysPaidAccepted()
    {
        return $this->paidleaves()->where('status', PaidLeave::STATUS_ACCEPTED)
            ->whereHas('calendars', function ($query) {

                $currentYear = date('Y');
                $currentMonth = date('m');
                $yearOfStartDate = $currentMonth >= 6 ? $currentYear : $currentYear - 1;

                $startDate = Carbon::createFromDate($yearOfStartDate, 6, 1);
                $endDate = (clone $startDate)->addYear()->subDay();
                $query->where(function ($subQuery) use ($startDate, $endDate) {
                    $subQuery->where('date', '>=', $startDate)
                        ->where('date', '<=', $endDate);
                });
            });
    }
}
