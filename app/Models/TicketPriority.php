<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketPriority extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'color',
        'icon',
        'level',
        'is_default',
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'priority_id');
    }

    public static function getDefault()
    {
        return self::where('is_default', true)->first() ?? self::first();
    }
}
