<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventPlanning extends Model
{
    use HasFactory;

    protected $fillable = [
        'planning_id',
        'title',
        'description'
    ];

    public function planning(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Planning::class);
    }
}
