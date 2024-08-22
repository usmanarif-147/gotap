<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Platform extends Model
{
    use HasFactory;

    protected $fillable = [
        'old_platform_id',
        'category_id',
        'title',
        'icon',
        'pro',
        'status',
        'placeholder_en',
        'placeholder_sv',
        'description_en',
        'description_sv',
        'baseURL',
        'input'
    ];

    public function profiles()
    {
        return $this->belongsToMany(Profile::class, 'user_platforms', 'platform_id', 'profile_id')
            ->withPivot('path', 'direct', 'platform_order', 'label')
            ->orderBy('platform_order');
    }
}
