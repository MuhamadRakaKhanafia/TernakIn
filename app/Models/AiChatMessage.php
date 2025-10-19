<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AiChatMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'session_id',
        'role',
        'content',
        'metadata',
        'token_count',
        'cost',
        'model_used'
    ];

    protected $casts = [
        'metadata' => 'array',
        'cost' => 'decimal:6',
        'token_count' => 'integer'
    ];

    public function session()
    {
        return $this->belongsTo(AiChatSession::class, 'session_id');
    }

    // Accessor for formatted content
    public function getFormattedContentAttribute()
    {
        return nl2br(e($this->content));
    }

    // Scope for user messages
    public function scopeUserMessages($query)
    {
        return $query->where('role', 'user');
    }

    // Scope for assistant messages
    public function scopeAssistantMessages($query)
    {
        return $query->where('role', 'assistant');
    }
}