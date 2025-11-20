<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Ticket extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'title',
        'description',
        'status_id',
        'category_id',
        'priority_id',
        'created_by',
        'assigned_to',
        'team_id',
        'due_date',
        'closed_at',
    ];

    protected $casts = [
        'due_date' => 'datetime',
        'closed_at' => 'datetime',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['title', 'description', 'status_id', 'category_id', 'priority_id', 'assigned_to', 'due_date'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function status()
    {
        return $this->belongsTo(TicketStatus::class);
    }

    public function category()
    {
        return $this->belongsTo(TicketCategory::class);
    }

    public function priority()
    {
        return $this->belongsTo(TicketPriority::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function assignee()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function comments()
    {
        return $this->hasMany(TicketComment::class);
    }

    public function attachments()
    {
        return $this->hasMany(TicketAttachment::class);
    }

    public function tags()
    {
        return $this->belongsToMany(TicketTag::class, 'ticket_tag', 'ticket_id', 'tag_id');
    }

    public function histories()
    {
        return $this->hasMany(TicketHistory::class);
    }

    public function subscribers()
    {
        return $this->belongsToMany(User::class, 'ticket_subscriptions', 'ticket_id', 'user_id');
    }

    public function isClosed()
    {
        return $this->status->is_closed || $this->closed_at !== null;
    }

    public function isOverdue()
    {
        return $this->due_date && $this->due_date->isPast() && ! $this->isClosed();
    }

    public function scopeOpen($query)
    {
        return $query->whereHas('status', function ($q) {
            $q->where('is_closed', false);
        })->whereNull('closed_at');
    }

    public function scopeClosed($query)
    {
        return $query->where(function ($q) {
            $q->whereHas('status', function ($sq) {
                $sq->where('is_closed', true);
            })->orWhereNotNull('closed_at');
        });
    }

    public function scopeOverdue($query)
    {
        return $query->whereNotNull('due_date')
            ->where('due_date', '<', now())
            ->whereHas('status', function ($q) {
                $q->where('is_closed', false);
            })
            ->whereNull('closed_at');
    }

    public function scopeForTeam($query, $teamId)
    {
        return $query->where('team_id', $teamId);
    }

    public function scopeAssignedTo($query, $userId)
    {
        return $query->where('assigned_to', $userId);
    }

    public function scopeCreatedBy($query, $userId)
    {
        return $query->where('created_by', $userId);
    }
}
