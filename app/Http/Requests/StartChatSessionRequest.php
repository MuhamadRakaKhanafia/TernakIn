<?php
// app/Http/Requests/StartChatSessionRequest.php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StartChatSessionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Pastikan user sudah login
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'animal_type_id' => [
                'nullable',
                'integer',
                'exists:animal_types,id'
            ],
            'initial_message' => [
                'nullable', 
                'string', 
                'min:3', 
                'max:1000'
            ]
        ];
    }

    /**
     * Get custom validation messages.
     */
    public function messages(): array
    {
        return [
            'animal_type_id.integer' => 'Jenis ternak harus berupa angka.',
            'animal_type_id.exists' => 'Jenis ternak yang dipilih tidak valid.',
            'initial_message.string' => 'Pesan awal harus berupa teks.',
            'initial_message.min' => 'Pesan awal minimal 3 karakter.',
            'initial_message.max' => 'Pesan awal maksimal 1000 karakter.'
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'animal_type_id' => 'jenis ternak',
            'initial_message' => 'pesan awal'
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Bersihkan data sebelum validasi
        $this->merge([
            'animal_type_id' => $this->animal_type_id ? (int) $this->animal_type_id : null,
            'initial_message' => $this->initial_message ? trim($this->initial_message) : null
        ]);
    }
}