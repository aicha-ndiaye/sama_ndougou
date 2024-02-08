<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rules\Password;

class InscriptionClientRequest extends FormRequest
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
            'nom' => ['required', 'string', 'min:2', 'regex:/^[a-zA-Z]+$/'],
            'prenom' => ['required', 'string', 'min:2', 'regex:/^[a-zA-Z ]+$/'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', Password::min(6)->letters()->numbers()],
            'adresse' => ['required', 'string', 'regex:/^[a-zA-Z0-9 ]+$/'],
            'telephone' => ['required', 'regex:/^7\d{8}$/'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif'],
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
            'nom.required' => 'Le champ nom est obligatoire.',
            'nom.string' => 'Le nom doit être une chaîne de caractères.',
            'nom.min' => 'Le nom doit avoir au moins 2 caractères.',
            'prenom.required' => 'Le champ prénom est obligatoire.',
            'prenom.string' => 'Le prénom doit être une chaîne de caractères.',
            'prenom.min' => 'Le prénom doit avoir au moins 2 caractères.',
            'email.required' => 'Le champ email est obligatoire.',
            'email.email' => 'L\'email doit être une adresse email valide.',
            'email.unique' => 'Cet email est déjà utilisé.',
            'password.required' => 'Le champ mot de passe est obligatoire.',
            'password.min' => 'Le mot de passe doit avoir au moins :min caractères.',
            'password.regex' => 'Le mot de passe doit contenir au moins une lettre et un chiffre.',
            'adresse.required' => 'Le champ adresse est obligatoire.',
            'adresse.string' => 'L\'adresse doit être une chaîne de caractères.',
            'adresse.regex' => 'L\'adresse doit être composée de lettres, de chiffres et d\'espaces.',
            'telephone.required' => 'Le champ téléphone est obligatoire.',
            'telephone.regex' => 'Le téléphone doit commencer par 7 suivi de 8 chiffres.',
            'image.image' => 'Le fichier doit être une image.',
            'image.mimes' => 'Le fichier doit être de type :values.',
 
        ];
    }
}
