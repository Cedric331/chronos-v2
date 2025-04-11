<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class ExchangeRequest extends Model
{
    use HasFactory, LogsActivity;

    /**
     * Les statuts possibles pour une demande d'échange
     */
    const STATUS_PENDING = 'pending';
    const STATUS_ACCEPTED = 'accepted';
    const STATUS_REJECTED = 'rejected';
    const STATUS_CANCELLED = 'cancelled';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'requester_id',
        'requested_id',
        'requester_planning_id',
        'requested_planning_id',
        'team_id',
        'status',
        'requester_comment',
        'requested_comment',
        'responded_at',
        'approved_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'responded_at' => 'datetime',
    ];

    /**
     * Get the activity log options for the model.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['*']);
    }

    /**
     * Get the user who requested the exchange.
     */
    public function requester()
    {
        return $this->belongsTo(User::class, 'requester_id');
    }

    /**
     * Get the user who was requested for the exchange.
     */
    public function requested()
    {
        return $this->belongsTo(User::class, 'requested_id');
    }

    /**
     * Get the planning that the requester wants to exchange.
     */
    public function requesterPlanning()
    {
        return $this->belongsTo(Planning::class, 'requester_planning_id');
    }

    /**
     * Get the planning that the requester wants to obtain.
     */
    public function requestedPlanning()
    {
        return $this->belongsTo(Planning::class, 'requested_planning_id');
    }

    /**
     * Get the team associated with this exchange request.
     */
    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    /**
     * Get the coordinator who approved the exchange.
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Scope a query to only include pending exchange requests.
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope a query to only include accepted exchange requests.
     */
    public function scopeAccepted($query)
    {
        return $query->where('status', self::STATUS_ACCEPTED);
    }

    /**
     * Scope a query to only include rejected exchange requests.
     */
    public function scopeRejected($query)
    {
        return $query->where('status', self::STATUS_REJECTED);
    }

    /**
     * Scope a query to only include cancelled exchange requests.
     */
    public function scopeCancelled($query)
    {
        return $query->where('status', self::STATUS_CANCELLED);
    }
}
