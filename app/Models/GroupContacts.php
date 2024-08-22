<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupContacts extends Model
{
    use HasFactory;

    protected $table = 'group_contacts';

    protected $fillable = [
        'group_id',
        'contact_id'
    ];
}
