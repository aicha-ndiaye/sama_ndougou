<?php

namespace App\Http\Controllers;

use App\Models\Panier;
use App\Models\Produit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;

class PanierController extends Controller
{
    public function ajouterAuPanier(Request $request)
    {
        $user = Auth::guard('api')->user();

        if (!$user) {
            return response()->json(['message' => 'Non autorisé'], 401);
        }

        if ($user->id == 2) {
            return response()->json(['message' => 'Accès refusé seul un client peut ajouter au panier'], 403);
        }

        $request->validate([
            'produit_id' => 'required|exists:produits,id',
            'quantite' => 'required|integer|min:1',
        ]);

        $produit = Produit::find($request->produit_id);

        if (!$produit || $produit->quantiteTotale <= 0) {
            return response()->json(['message' => 'Produit non disponible'], 400);
        }

        // Vérifiez si le panier existe déjà pour l'utilisateur et le produit spécifiés
        $panier = Panier::where('user_id', $user->id)
            ->where('produit_id', $request->produit_id)
            ->first();

        if (!$panier) {
            // Si le panier n'existe pas, créez-en un nouveau
            $panier = Panier::create([
                'user_id' => $user->id,
                'produit_id' => $request->produit_id,
                'quantite' => $request->quantite,
            ]);
        } else {
            // Si le panier existe déjà, mettez à jour la quantité du produit
            $panier->quantite += $request->quantite;
            $panier->save();
        }

        // Réduisez la quantité du produit dans le stock
        $produit->quantiteTotale -= $panier->quantite;
        $produit->save();

        return response()->json([
            'message' => 'Produit ajouté au panier avec succès',
            'commande' => [
                'user' => $user->prenom,
                'prenom' => $user->nom,
                'produit' => $produit->nomProduit,
                'quantite' => $request->quantite,
            ]
        ], 201);
    }



    public function afficherProduitsPanier()
{
    // Obtenez l'utilisateur connecté
    $user = Auth::guard('api')->user();

    // Vérifiez si l'utilisateur est authentifié
    if ($user) {
        // Obtenez les produits du panier de l'utilisateur
        $produitsPanier = Panier::where('user_id', $user->id)
            ->with('produit')
            ->get();

        // Retournez une réponse JSON avec les produits du panier
        return response()->json(['produitsPanier' => $produitsPanier]);
    } else {
        // Utilisateur non authentifié
        return response()->json(['message' => 'Non autorisé'], 401);
    }
}




public function ajouterAuPanierVisiteur(Request $request)
{
    // Récupérez le panier du visiteur à partir de la session
    $panier = Session::get('panier', []);

    // Récupérez le produit_id de la requête
    $produitId = $request->produit_id;

    // Vérifiez si le produit existe déjà dans le panier
    $produit = $panier[$produitId] ?? null;

    if ($produit) {
        // Si le produit existe déjà, incrémentez la quantité
        $produit['quantite'] += $request->quantite;
    } else {
        // Si le produit n'existe pas, créez un nouvel élément
        $produit = [
            'produit_id' => $produitId,
            'quantite' => $request->quantite,
        ];
    }

    // Ajoutez le produit au panier
    $panier[$produitId] = $produit;

    // Sauvegardez le panier dans la session
    Session::put('panier', $panier);

    // Retournez une réponse JSON
    return response()->json(['message' => 'Produit ajouté au panier visiteur avec succès'], 200);
}


// public function afficherPanierVisiteur()
// {
//     $panier = Session::get('panier', []);

//     return response()->json(['panier' => $panier]);
// }

public function afficherPanierVisiteur(Request $request)
{
    // Récupérez le panier du visiteur à partir de la session
    $panier = Session::get('panier', []);

    // Si le panier est vide, renvoyez une erreur
    if (count($panier) === 0) {
        return response()->json(['error' => 'Le panier est vide'], 404);
    }

    // Retournez la réponse JSON avec le panier
    return response()->json(['panier' => $panier], 200);
}









    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
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

    public function deletePanier($id)
    {
        $user = Auth::guard('api')->user();

        if ($user) {
            if ($user->role_id == 2) {
                $panier = Panier::find($id);

                if (!$panier) {
                    return response()->json(['message' => 'panier non trouvée'], 404);
                }

                $panier->delete();

                return response()->json(['message' => 'panier supprimé avec succès'], 200);
            } else {
                return response()->json(['message' => 'Vous n\'êtes pas autorisé à effectuer cette action'], 401);
            }
        } else {
            return response()->json(['message' => 'Vous devez être connecté pour effectuer cette action'], 401);
        }
    }

}
