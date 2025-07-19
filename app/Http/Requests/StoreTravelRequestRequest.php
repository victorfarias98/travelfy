<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth; 

class StoreTravelRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules(): array
    {
        return [
            'destination' => 'required|string|max:255|min:2',
            'departure_date' => 'required|date|after:today',
            'return_date' => 'required|date|after:departure_date'
        ];
    }

    public function messages(): array
    {
        return [
            'destination.required' => 'O destino é obrigatório',
            'destination.string' => 'O destino deve ser um texto',
            'destination.max' => 'O destino não pode ter mais de 255 caracteres',
            'destination.min' => 'O destino deve ter pelo menos 2 caracteres',
            'departure_date.required' => 'A data de partida é obrigatória',
            'departure_date.date' => 'A data de partida deve ser uma data válida',
            'departure_date.after' => 'A data de partida deve ser posterior a hoje',
            'return_date.required' => 'A data de retorno é obrigatória',
            'return_date.date' => 'A data de retorno deve ser uma data válida',
            'return_date.after' => 'A data de retorno deve ser posterior à data de partida'
        ];
    }
}