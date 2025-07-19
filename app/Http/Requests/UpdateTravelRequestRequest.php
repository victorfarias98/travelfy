<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTravelRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->role === 'admin';
    }

    public function rules(): array
    {
        return [
            'status' => 'required|in:approved,cancelled'
        ];
    }

    public function messages(): array
    {
        return [
            'status.required' => 'O status é obrigatório',
            'status.in' => 'O status deve ser "approved" ou "cancelled"'
        ];
    }
}