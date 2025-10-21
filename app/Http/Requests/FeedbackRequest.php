<?php
// app/Http/Requests/FeedbackRequest.php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class FeedbackRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'message_id' => [
                'required',
                'integer',
                'exists:ai_chat_messages,id'
            ],
            'rating' => [
                'required',
                'string',
                'in:helpful,not-helpful'
            ],
            'session_id' => [
                'required',
                'string',
                'exists:ai_chat_sessions,session_id'
            ]
        ];
    }

    /**
     * Get custom validation messages.
     */
    public function messages(): array
    {
        return [
            'message_id.required' => 'ID pesan diperlukan.',
            'message_id.exists' => 'Pesan tidak ditemukan.',
            'rating.required' => 'Rating diperlukan.',
            'rating.in' => 'Rating harus helpful atau not-helpful.',
            'session_id.required' => 'ID sesi diperlukan.',
            'session_id.exists' => 'Sesi tidak ditemukan.'
        ];
    }
}