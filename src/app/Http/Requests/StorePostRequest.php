<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePostRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'title' => 'nullable|string|max:150',
            'content' => 'required|string|min:10',
            'model_id' => 'nullable|exists:car_model,model_id',
            'engine_id' => 'nullable|exists:car_engine,engine_id',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'content.required' => 'El contenido del post es obligatorio.',
            'content.min' => 'El contenido debe tener al menos 10 caracteres.',
            'model_id.exists' => 'El modelo seleccionado no existe.',
            'engine_id.exists' => 'El motor seleccionado no existe.',
        ];
    }
}
