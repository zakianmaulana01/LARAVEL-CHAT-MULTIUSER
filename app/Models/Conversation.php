<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'type', 'created_by'];

    public function participants()
    {
        return $this->belongsToMany(User::class)->withPivot('joined_at');
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function latestMessage()
    {
        return $this->hasOne(Message::class)->latestOfMany();
    }

    public function unreadMessagesFor(User $user): int
    {
        return $this->messages()
            ->where('sender_id', '!=', $user->id)
            ->where('is_read', false)
            ->count();
    }

    public function getOtherParticipant(User $user)
    {
        return $this->participants->where('id', '!=', $user->id)->first();
    }
}
