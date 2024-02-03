<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class createCommandeRequest extends FormRequest
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
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'adresse_de_livraison' => 'required|string|regex:/^[a-zA-Z]{3,}[0-9]*$/',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'status_code' => 422,
            'error' => true,
            'message' => 'Erreur de validation',
            'errorList' => $validator->errors()
        ]));
    }

    public function messages()
    {
        return [
            'adresse_de_livraison.required' => 'Le champ adresse de livraison est obligatoire.',
            'adresse_de_livraison.regex' => 'L\'adresse de livraison doit avoir  au moins 3 lettres.',
        ];
    }

}
