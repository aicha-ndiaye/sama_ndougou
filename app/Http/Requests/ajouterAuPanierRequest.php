<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class AjouterAuPanierRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Remplacez cette logique par votre propre logique d'autorisation
        // Par exemple, vérifiez si l'utilisateur est authentifié
        // ou s'il a le rôle approprié pour ajouter au panier, etc.
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
            'produit_id' => 'required|exists:produits,id',
            'quantite' => 'required|integer|min:1',
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
            'produit_id.required' => 'Le champ produit_id est obligatoire.',
            'produit_id.exists' => 'Le produit sélectionné n\'existe pas.',
            'quantite.required' => 'Le champ quantite est obligatoire.',
            'quantite.integer' => 'Le champ quantite doit être un nombre entier.',
            'quantite.min' => 'Le champ quantite doit être d\'au moins 1.'
        ];
    }
}
