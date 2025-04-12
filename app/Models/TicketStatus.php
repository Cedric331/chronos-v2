<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketStatus extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'color',
        'icon',
        'order',
        'is_default',
        'is_closed',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'is_closed' => 'boolean',
    ];

    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'status_id');
    }

    public static function getDefault()
    {
        return self::where('is_default', true)->first() ?? self::first();
    }
}
