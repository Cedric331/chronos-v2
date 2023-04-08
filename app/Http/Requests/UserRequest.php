<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
       return Gate::allows('has-authorization');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['bail', 'required', 'string', 'max:50'],
            'hub' => ['bail', 'integer'],
            'birthday' => 'bail|nullable|date',
            'phone' => 'bail|nullable|string|size:10',
            'email' => ['bail', 'required', 'email', 'max:255', Rule::unique(User::class)->ignore($this->user()->id)],
        ];
    }

    /**
     * Get the custom messages for the validator instance.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Le nom est obligatoire.',
            'name.max' => 'Le nom ne peut pas dépasser 50 caractères.',

            'hub.integer' => 'Le hub doit être un entier.',

            'birthday.date' => 'La date de naissance doit être une date valide.',

            'phone.size' => 'Le téléphone doit contenir exactement 10 caractères.',

            'email.required' => 'L\'adresse e-mail est obligatoire.',
            'email.email' => 'L\'adresse e-mail doit être valide.',
            'email.max' => 'L\'adresse e-mail ne peut pas dépasser 255 caractères.',
            'email.unique' => 'L\'adresse e-mail est déjà utilisée.',
        ];
    }
}
