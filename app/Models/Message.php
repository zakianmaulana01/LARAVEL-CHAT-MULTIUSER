<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'conversation_id', 'sender_id', 'body', 'file_path', 'is_read', 'deleted_by_superadmin',
        'edited_at', 'deleted_by_sender',
    ];

    protected function casts(): array
    {
        return [
            'is_read' => 'boolean',
            'deleted_by_superadmin' => 'boolean',
            'edited_at' => 'datetime',
            'deleted_by_sender' => 'boolean',
        ];
    }

    public function canBeEditedBy(User $user): bool
    {
        return $this->sender_id === $user->id
            && $this->created_at->diffInMinutes(now()) < 5;
    }

    public function canBeDeletedBy(User $user): bool
    {
        return $this->sender_id === $user->id
            && $this->created_at->diffInMinutes(now()) < 5;
    }

    public function conversation()
    {
        return $this->belongsTo(Conversation::class);
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }
}
