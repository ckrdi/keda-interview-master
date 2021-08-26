<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'sender_id',
        'sent_to_id',
        'subject',
        'message'
    ];

    // A message belongs to a sender
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id')->withTrashed();
    }

    // A message also belongs to a receiver    
    public function receiver()
    {
        return $this->belongsTo(User::class, 'sent_to_id')->withTrashed();
    }
}
