<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\AvisClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class avis_clienttController extends Controller
{


    public function createAvis(Request $request)
{
    $user = Auth::guard('api')->user();

    if ($user && $user->role_id == 2) {
        $validator = $request->validate([
            'contenu' => 'required|string|min:3',
            'produit_id' => 'required|exists:produits,id', 
        ]);

        $avis = AvisClient::create([
            'contenu' => $request->contenu,
            'user_id' => $user->id,
            'produit_id' => $request->produit_id,
        ]);

        return response()->json([
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
    }

