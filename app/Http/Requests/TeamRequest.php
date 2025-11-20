<?php

namespace App\Http\Requests;

use App\Rules\DepartmentCode;
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

        if ($this->code_departement) {
            $departments = array_map(function ($item) {
                if ($item['num_dep'] == $this->code_departement) {
                    return $item['dep_name'];
                }
            }, config('region'));
        }
        $this->departement = $departments[$this->code_departement] ?? null;

        if ($this->departement) {
            $this->merge(['departement' => $this->departement]);
        }

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('teams')->ignore($this->team),
            ],
            'logo' => 'nullable|file|image|mimes:jpeg,png,jpg,gif|max:2048',
            'departement' => ['nullable', 'string', 'max:255'],
            'code_departement' => ['nullable', 'integer', new DepartmentCode],
            'user_id' => 'nullable|exists:users,id',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Le nom de l\'équipe est obligatoire',
            'name.string' => 'Le nom de l\'équipe doit être une chaîne de caractères',
            'name.max' => 'Le nom de l\'équipe ne doit pas dépasser 255 caractères',
            'name.unique' => 'Le nom de l\'équipe est déjà utilisé',
            'logo.file' => 'Le logo doit être un fichier',
            'logo.image' => 'Le logo doit être une image',
            'logo.mimes' => 'Le logo doit être une image de type jpeg, png, jpg, gif',
            'logo.max' => 'Le logo ne doit pas dépasser 2048 kilo-octets',
            'departement.string' => 'Le département doit être une chaîne de caractères',
            'departement.max' => 'Le département ne doit pas dépasser 255 caractères',
            'code_departement.integer' => 'Le code du département doit être un entier',
            'code_departement.min' => 'Le code du département doit être supérieur à 0',
            'code_departement.max' => 'Le code du département ne doit pas dépasser 999',
            'user_id.exists' => 'L\'utilisateur n\'existe pas',
        ];
    }
}
