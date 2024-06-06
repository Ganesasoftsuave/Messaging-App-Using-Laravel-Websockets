<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserGroupMember extends Model
{
    use HasFactory;
    protected $fillable = ['sender_id', 'group_id', 'is_subscribe'];
    public function group()
    {
        return $this->belongsTo(UserGroup::class, 'group_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
