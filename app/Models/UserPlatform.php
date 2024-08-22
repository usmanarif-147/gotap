<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserPlatform extends Model
{
    use HasFactory;

    protected $table = 'user_platforms';

    protected $fillable = [
        'user_id',
        'profile_id',
        'platform_id',
        'path',
        'direct',
        'platform_order',
        'label',
    ];
}
