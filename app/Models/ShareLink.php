<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShareLink extends Model
{
    use HasFactory;

    protected $fillable = [
        'token',
        'expired_at',
        'count_view',
        'user_id',
    ];

    protected $dates = ['expired_at'];

    protected $appends = ['linkValide', 'DateExpired'];

    public function getTokenAttribute(): string
    {
        return url('/planning/'.$this->attributes['token']);
    }

    public function getDateExpiredAttribute()
    {
        return \Carbon\Carbon::parse($this->attributes['expired_at'])->format('d/m/Y H:i');
    }

    public function getLinkValideAttribute(): bool
    {
        return $this->expired_at > now();
    }
}
