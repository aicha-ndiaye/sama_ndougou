<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DeleteAvisRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // Renvoyer vrai si l'utilisateur a le rôle ID 1 ou le rôle ID 2
        return auth()->user() && (auth()->user()->role_id == 1 || auth()->user()->role_id == 2);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            // Les règles de validation, si nécessaires
        ];
    }
}
