<?php

namespace App\Http\Controllers;

use App\Models\Panier;
use App\Models\Produit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PanierController extends Controller
{


    public function ajouterAuPanier(Request $request)
    {
        // Obtenez l'utilisateur connecté
        $user = Auth::guard('api')->user();

        // Vérifiez si l'utilisateur est authentifié
        if ($user) {
            // Obtenez le nom et le prénom de l'utilisateur connecté
            $usernom = $user->nom;
            $userprenom = $user->prenom;

            // Créez un nouvel objet Panier
            $panier = new Panier([
                'user_id' => $user->id,
                'produit_id' => $request->produit_id,
                'quantite' => $request->quantite,
            ]);

            // Enregistrez le panier
            $panier->save();

            // Obtenez les détails du produit ajouté
            $nomProduit = $panier->produit->nomProduit;

            // Retournez une réponse JSON avec les détails de la commande
            return response()->json([
                'message' => 'Produit ajouté au panier avec succès',
                'commande' => [
                    'user' => $userprenom,
                    'prenom' => $usernom,
                    'produit' => $nomProduit,
                    'quantite' => $request->quantite,
                ]
            ], 201);
        } else {
            // Utilisateur non authentifié
            return response()->json(['message' => 'Non autorisé'], 401);
        }
    }




public function indexPanier()
{
    $user = auth()->user();
    $panier = Panier::where('user_id', $user->id)->with('produit')->get();

    return response()->json(['panier' => $panier]);
}


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
