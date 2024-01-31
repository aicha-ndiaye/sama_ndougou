<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class CreateAvisRequest extends FormRequest
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
            'contenu' => 'required|string|min:3',
            'produit_id' => 'required|exists:produits,id',
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
            'contenu.required' => 'Le champ contenu de l\'avis est obligatoire.',
            'contenu.string' => 'Le contenu de l\'avis doit être une chaîne de caractères.',
            'contenu.min' => 'Le contenu de l\'avis doit avoir au moins :min caractères.',
            'produit_id.required' => 'Le champ produit_id est obligatoire.',
            'produit_id.exists' => 'Le produit sélectionné n\'existe pas.',
        ];
    }
}
