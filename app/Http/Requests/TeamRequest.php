<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class TeamRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::user()->isCoordinateur();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        if ($this->input('logo') === 'null') {
            $this->merge(['logo' => null]);
        }

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('teams')->ignore($this->team),
            ],
            'logo' => 'nullable|file|image|mimes:jpeg,png,jpg,gif|max:2048',
            'departement' => 'required|string|max:255',
            'code_departement' => 'required|integer|min:1|max:999',
            'user_id' => 'nullable|exists:users,id',
        ];
    }
}
