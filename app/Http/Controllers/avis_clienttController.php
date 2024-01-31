<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\AvisClient;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\createAvisRequest;
use App\Http\Requests\deleteAvisRequest;
use Illuminate\Support\Facades\Validator;


class avis_clienttController extends Controller
{


    public function createAvis(createAvisRequest $request)
{
    $user = Auth::guard('api')->user();
    $usernom = Auth::guard('api')->user()->nom;
    $userprenom = Auth::guard('api')->user()->prenom;

    if ($user && $user->role_id == 2) {
        $avis = AvisClient::create([
            'contenu' => $request->contenu,
            'user_id' => $user->id,
            'produit_id' => $request->produit_id,
        ]);


        return response()->json([
            'user' => $userprenom,
            'prenom' => $usernom,
            'message' => 'Avis bien ajouté',
            'commentaire' => $avis,

        ]);
    }

    return response()->json(['message' => 'Non autorisé'], 401);
}


public function indexAvis()
{
    $avis = AvisClient::all();
    return response()->json([
        "La listes de tous les avis "=>$avis
    ], 200);
}



    /**
     * Update the specified resource in storage.
     */
    public function updateAvis(Request $request, string $id)
    {
        $user = Auth::guard('api')->user();
        if ($user->role_id == 1) { $validator=Validator::make($request->all(),[
            'contenu' => 'required|string|min:3',
            'id' => 'required|numeric',

        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $avis = AvisClient::find($id);
        $avis->contenu = $request->contenu;
        $avis->save();

        return response()->json(['message' => 'avis modifié avec succès', 'avis' => $avis], 200);
    }
        }


        public function deleteAvis(deleteAvisRequest $request, $id)
        {
            $user = auth()->user();

            if ($user) {
                if ($user) {
                    // Utilisez find pour obtenir une instance du modèle
                    $avis = AvisClient::find($id);

                    if (!$avis) {
                        return response()->json(['message' => 'Avis non trouvé'], 404);
                    }

                    // Appel de la méthode delete() sur l'instance du modèle
                    $avis->delete();

                    return response()->json(['message' => 'Avis supprimé avec succès'], 200);
                } else {
                    return response()->json(['message' => 'Vous n\'êtes pas autorisé à effectuer cette action'], 403);
                }
            } else {
                return response()->json(['message' => 'Vous devez être connecté pour effectuer cette action'], 401);
            }
    }
    }

