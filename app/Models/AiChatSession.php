<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AiChatSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'session_id',
        'user_id',
        'animal_type_id',
        'title',
        'message_count',
        'token_count',
        'last_activity',
        'is_active'
    ];

    protected $casts = [
        'last_activity' => 'datetime',
        'is_active' => 'boolean',
        'message_count' => 'integer',
        'token_count' => 'integer'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function animalType()
    {
        return $this->belongsTo(AnimalType::class);
    }

    public function messages()
    {
        return $this->hasMany(AiChatMessage::class, 'session_id');
    }

    // Helper method to get the last user message
    public function getLastUserMessage()
    {
        return $this->messages()
            ->where('role', 'user')
            ->orderBy('created_at', 'desc')
            ->first();
    }

    // Helper method to get conversation history
    public function getConversationHistory($limit = 10)
    {
        return $this->messages()
            ->orderBy('created_at', 'asc')
            ->limit($limit)
            ->get();
    }
}