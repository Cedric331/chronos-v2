<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketTag extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'color',
    ];

    public function tickets()
    {
        return $this->belongsToMany(Ticket::class, 'ticket_tag', 'tag_id', 'ticket_id');
    }
}
