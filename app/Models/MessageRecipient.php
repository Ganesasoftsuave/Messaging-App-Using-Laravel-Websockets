<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MessageRecipient extends Model
{
    use HasFactory;
    protected $fillable = ['message_id', 'recipient_id', 'seen', 'seen_at'];
    public function message()
    {
        return $this->belongsTo(Message::class, 'message_id');
    }
    
}
