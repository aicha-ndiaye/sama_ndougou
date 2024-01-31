<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class updateProduitRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            //
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
            'nomProduit.required' => 'Le champ nom du produit est obligatoire.',
            'prix.required' => 'Le champ prix est obligatoire.',
            'prix.numeric' => 'Le champ prix doit être un nombre.',
            'quantiteTotale.required' => 'Le champ quantité totale est obligatoire.',
            'quantiteTotale.integer' => 'La quantité totale doit être un nombre entier.',
            'description.required' => 'Le champ description est obligatoire.',
            'description.string' => 'La description doit être une chaîne de caractères.',
            'image.required' => 'Le champ image est obligatoire.',
            'image.image' => 'Le fichier doit être une image.',
            'image.mimes' => 'Le fichier doit être de type :values.',
            'image.max' => 'Le fichier ne peut pas dépasser 2048 kilo-octets.',
        ];
    }
}
