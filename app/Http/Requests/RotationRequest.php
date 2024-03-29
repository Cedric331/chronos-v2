<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\RequiredIf;

class RotationRequest extends FormRequest
{
    /**
     * Indicates if the validator should stop on the first rule failure.
     *
     * @var bool
     */
    protected $stopOnFirstFailure = true;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->isCoordinateur();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'team_id' => $this->method() === 'POST' ? 'required|integer' : 'nullable|integer',
            'name' => [
                'required',
                'max:5',
                'string',
                Rule::unique('rotations')->where(function ($query) {
                    return $query->where('name', $this->name)
                        ->where('team_id', $this->team_id);
                }),
            ],

            'jours' => 'required',

            'jours.lundi' => 'required',
            'jours.lundi.debut_journee' => new RequiredIf($this->jours['lundi']['fin_journee'] !== null && $this->jours['lundi']['is_off'] === false),
            'jours.lundi.fin_journee' => new RequiredIf($this->jours['lundi']['debut_journee'] !== null && $this->jours['lundi']['is_off'] === false),
            'jours.lundi.debut_pause' => new RequiredIf($this->jours['lundi']['fin_pause'] !== null && $this->jours['lundi']['is_off'] === false),
            'jours.lundi.fin_pause' => new RequiredIf($this->jours['lundi']['debut_pause'] !== null && $this->jours['lundi']['debut_pause'] !== 'Pas de pause' && $this->jours['lundi']['is_off'] === false),

            'jours.mardi' => 'required',
            'jours.mardi.debut_journee' => new RequiredIf($this->jours['mardi']['fin_journee'] !== null && $this->jours['mardi']['is_off'] === false),
            'jours.mardi.fin_journee' => new RequiredIf($this->jours['mardi']['debut_journee'] !== null && $this->jours['mardi']['is_off'] === false),
            'jours.mardi.debut_pause' => new RequiredIf($this->jours['mardi']['fin_pause'] !== null && $this->jours['mardi']['is_off'] === false),
            'jours.mardi.fin_pause' => new RequiredIf($this->jours['mardi']['debut_pause'] !== null && $this->jours['mardi']['debut_pause'] !== 'Pas de pause' && $this->jours['mardi']['is_off'] === false),

            'jours.mercredi' => 'required',
            'jours.mercredi.debut_journee' => new RequiredIf($this->jours['mercredi']['fin_journee'] !== null && $this->jours['mercredi']['is_off'] === false),
            'jours.mercredi.fin_journee' => new RequiredIf($this->jours['mercredi']['debut_journee'] !== null && $this->jours['mercredi']['is_off'] === false),
            'jours.mercredi.debut_pause' => new RequiredIf($this->jours['mercredi']['fin_pause'] !== null && $this->jours['mercredi']['is_off'] === false),
            'jours.mercredi.fin_pause' => new RequiredIf($this->jours['mercredi']['debut_pause'] !== null && $this->jours['mercredi']['debut_pause'] !== 'Pas de pause' && $this->jours['mercredi']['is_off'] === false),

            'jours.jeudi' => 'required',
            'jours.jeudi.debut_journee' => new RequiredIf($this->jours['jeudi']['fin_journee'] !== null && $this->jours['jeudi']['is_off'] === false),
            'jours.jeudi.fin_journee' => new RequiredIf($this->jours['jeudi']['debut_journee'] !== null && $this->jours['jeudi']['is_off'] === false),
            'jours.jeudi.debut_pause' => new RequiredIf($this->jours['jeudi']['fin_pause'] !== null && $this->jours['jeudi']['is_off'] === false),
            'jours.jeudi.fin_pause' => new RequiredIf($this->jours['jeudi']['debut_pause'] !== null && $this->jours['jeudi']['debut_pause'] !== 'Pas de pause' && $this->jours['jeudi']['is_off'] === false),

            'jours.vendredi' => 'required',
            'jours.vendredi.debut_journee' => new RequiredIf($this->jours['vendredi']['fin_journee'] !== null && $this->jours['vendredi']['is_off'] === false),
            'jours.vendredi.fin_journee' => new RequiredIf($this->jours['vendredi']['debut_journee'] !== null && $this->jours['vendredi']['is_off'] === false),
            'jours.vendredi.debut_pause' => new RequiredIf($this->jours['vendredi']['fin_pause'] !== null && $this->jours['vendredi']['is_off'] === false),
            'jours.vendredi.fin_pause' => new RequiredIf($this->jours['vendredi']['debut_pause'] !== null && $this->jours['vendredi']['debut_pause'] !== 'Pas de pause' && $this->jours['lundi']['debut_pause'] !== 'Pas de pause' && $this->jours['vendredi']['is_off'] === false),

            'jours.samedi' => 'required',
            'jours.samedi.debut_journee' => new RequiredIf($this->jours['samedi']['fin_journee'] !== null && $this->jours['samedi']['is_off'] === false),
            'jours.samedi.fin_journee' => new RequiredIf($this->jours['samedi']['debut_journee'] !== null && $this->jours['samedi']['is_off'] === false),
            'jours.samedi.debut_pause' => new RequiredIf($this->jours['samedi']['fin_pause'] !== null && $this->jours['samedi']['is_off'] === false),
            'jours.samedi.fin_pause' => new RequiredIf($this->jours['samedi']['debut_pause'] !== null && $this->jours['samedi']['debut_pause'] !== 'Pas de pause' && $this->jours['samedi']['is_off'] === false),

            'jours.dimanche' => 'required',
            'jours.dimanche.debut_journee' => new RequiredIf($this->jours['dimanche']['fin_journee'] !== null && $this->jours['dimanche']['is_off'] === false),
            'jours.dimanche.fin_journee' => new RequiredIf($this->jours['dimanche']['debut_journee'] !== null && $this->jours['dimanche']['is_off'] === false),
            'jours.dimanche.debut_pause' => new RequiredIf($this->jours['dimanche']['fin_pause'] !== null && $this->jours['dimanche']['is_off'] === false),
            'jours.dimanche.fin_pause' => new RequiredIf($this->jours['dimanche']['debut_pause'] !== null && $this->jours['dimanche']['debut_pause'] !== 'Pas de pause' && $this->jours['dimanche']['is_off'] === false),
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'name' => 'Nom de la rotation',

            'jours.lundi' => 'lundi',
            'jours.lundi.debut_journee' => 'Début de journée de lundi',
            'jours.lundi.fin_journee' => 'Fin de journée de lundi',
            'jours.lundi.debut_pause' => 'Début de pause de lundi',
            'jours.lundi.fin_pause' => 'Fin de pause de lundi',

            'jours.mardi' => 'mardi',
            'jours.mardi.debut_journee' => 'Début de journée de mardi',
            'jours.mardi.fin_journee' => 'Fin de journée de mardi',
            'jours.mardi.debut_pause' => 'Début de pause de mardi',
            'jours.mardi.fin_pause' => 'Fin de pause de mardi',

            'jours.mercredi' => 'mercredi',
            'jours.mercredi.debut_journee' => 'Début de journée de mercredi',
            'jours.mercredi.fin_journee' => 'Fin de journée de mercredi',
            'jours.mercredi.debut_pause' => 'Début de pause de mercredi',
            'jours.mercredi.fin_pause' => 'Fin de pause de mercredi',

            'jours.jeudi' => 'jeudi',
            'jours.jeudi.debut_journee' => 'Début de journée de jeudi',
            'jours.jeudi.fin_journee' => 'Fin de journée de jeudi',
            'jours.jeudi.debut_pause' => 'Début de pause de jeudi',
            'jours.jeudi.fin_pause' => 'Fin de pause de jeudi',

            'jours.vendredi' => 'vendredi',
            'jours.vendredi.debut_journee' => 'Début de journée de vendredi',
            'jours.vendredi.fin_journee' => 'Fin de journée de vendredi',
            'jours.vendredi.debut_pause' => 'Début de pause de vendredi',
            'jours.vendredi.fin_pause' => 'Fin de pause de vendredi',

            'jours.samedi' => 'samedi',
            'jours.samedi.debut_journee' => 'Début de journée de samedi',
            'jours.samedi.fin_journee' => 'Fin de journée de samedi',
            'jours.samedi.debut_pause' => 'Début de pause de samedi',
            'jours.samedi.fin_pause' => 'Fin de pause de samedi',

            'jours.dimanche' => 'dimanche',
            'jours.dimanche.debut_journee' => 'Début de journée de dimanche',
            'jours.dimanche.fin_journee' => 'Fin de journée de dimanche',
            'jours.dimanche.debut_pause' => 'Début de pause de dimanche',
            'jours.dimanche.fin_pause' => 'Fin de pause de dimanche',
        ];
    }
}
