<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Companie extends Model
{
    use HasFactory;

    public $fillable = [
        'name',
        'description',
        'logo',
        'is_active',
    ];
}
