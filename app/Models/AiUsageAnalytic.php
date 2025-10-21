<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AiUsageAnalytic extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'session_id',
        'input_tokens',
        'output_tokens',
        'total_tokens',
        'cost'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function session()
    {
        return $this->belongsTo(AiChatSession::class);
    }
}