<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Passport\HasApiTokens;


class User extends Authenticatable
{
    use HasFactory, SoftDeletes, HasApiTokens;

    protected $fillable = [
        'user_type_id',
        'email',
        'password'
    ];

    protected $hidden = [
        'password'
    ];
    
    public function userType()
    {
        return $this->belongsTo(UserType::class);
    }

    // A user can send a message
    public function sent()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    // A user can also receive a message
    public function received()
    {
        return $this->hasMany(Message::class, 'sent_to_id');
    }
}
