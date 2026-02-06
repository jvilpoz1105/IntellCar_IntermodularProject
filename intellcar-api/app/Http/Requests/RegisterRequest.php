<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
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
            'user_name' => 'required|string|max:90',
            'email_address' => 'required|email|unique:app_user,email_address',
            'phone' => 'required|string|unique:app_user,phone',
            'user_password' => 'required|string|min:6|confirmed',
            'contact_email' => 'nullable|email',
            'address' => 'nullable|string|max:255',
            'paddock_id' => 'nullable|exists:paddock,paddock_id',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'user_name.required' => 'El nombre de usuario es obligatorio.',
            'email_address.required' => 'El correo electrónico es obligatorio.',
            'email_address.email' => 'El correo electrónico debe ser válido.',
            'email_address.unique' => 'Este correo electrónico ya está registrado.',
            'phone.required' => 'El teléfono es obligatorio.',
            'phone.unique' => 'Este número de teléfono ya está registrado.',
            'user_password.required' => 'La contraseña es obligatoria.',
            'user_password.min' => 'La contraseña debe tener al menos 6 caracteres.',
            'user_password.confirmed' => 'Las contraseñas no coinciden.',
            'paddock_id.exists' => 'El paddock seleccionado no existe.',
        ];
    }
}
