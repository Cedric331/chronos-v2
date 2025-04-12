<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'color',
        'icon',
        'description',
        'is_default',
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'category_id');
    }

    public static function getDefault()
    {
        return self::where('is_default', true)->first() ?? self::first();
    }
}
