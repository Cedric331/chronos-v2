<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LinkTeam extends Model
{
    use HasFactory;

    protected $fillable = [
        'link',
        'description',
        'updated_by',
        'user_id',
        'team_id',
    ];

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
