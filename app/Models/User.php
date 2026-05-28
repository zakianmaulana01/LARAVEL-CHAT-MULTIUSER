<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'role', 'avatar', 'last_seen', 'is_banned',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_seen' => 'datetime',
            'is_banned' => 'boolean',
            'password' => 'hashed',
        ];
    }

    public function conversations()
    {
        return $this->belongsToMany(Conversation::class)->withPivot('joined_at');
    }

    public function messages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function isSuperadmin(): bool
    {
        return $this->role === 'superadmin';
    }

    public function isOnline(): bool
    {
        return $this->last_seen && $this->last_seen->diffInMinutes(now()) < 5;
    }

    public function aiChatHistories(): HasMany
    {
        return $this->hasMany(AiChatHistory::class);
    }
}
