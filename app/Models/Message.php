<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;
    protected $fillable = ['sender_id','receiver_id', 'content', 'type','sender_name','group_name','group_id'];

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function recipients()
    {
        return $this->hasMany(MessageRecipient::class, 'message_id');
    }

    public function group()
    {
        return $this->belongsTo(UserGroup::class, 'group_id');
    }
}
