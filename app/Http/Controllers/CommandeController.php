<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Panier;
use App\Models\Commande;
use App\Models\detailProduit;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Notifications\gererCommande;
use Illuminate\Support\Facades\Auth;

class CommandeController extends Controller
{
    /**
     * Display a listing of the resource.
     */


    // ...

    public function indexCommande()
    {
        // Récupérez l'utilisateur connecté
        $user = Auth::guard('api')->user();

        // Vérifiez si l'utilisateur est connecté
        if ($user) {
            // Obtenez la liste des commandes de l'utilisateur connecté
            $commandes = Commande::where('user_id', $user->id)->get();

            return response()->json([
                'Liste de vos commandes' => $commandes,
            ], 200);
        }

        // Retournez une réponse si l'utilisateur n'est pas connecté
        return response()->json(['message' => 'Non autorisé'], 401);
    }


    public function createCommande(Request $request)
    {
        $user = Auth::guard('api')->user();
        $panier = Panier::where('user_id', $user->id)->first();

        if (!$panier) {
            return response()->json(['status' => 404, 'status_message' => 'Le panier est vide ou n\'existe pas.']);
        }

        $commande = Commande::create([
            'dateCommande' => now(),
            'user_id' => $user->id,
            'numeroCommande' => Commande::max('numeroCommande') + 1,
            'adresse_de_livraison' => $request->adresse_de_livraison,
        ]);

        $montantTotal = 0;
        $quantiteTotal = 0;

        $produitsPanier = Panier::where('user_id', $user->id)->with('produit')->get();

        foreach ($produitsPanier as $produit) {
            $montantTotal += $produit->quantite * $produit->produit->prix;
            $quantiteTotal += $produit->quantite;

            DetailProduit::create([
                'commande_id' => $commande->id,
                'produit_id' => $produit->produit->id,
                'montant' => $produit->quantite * $produit->produit->prix,
                'nombre_produit' => $produit->quantite,
            ]);
        }

        // Détachez les produits après les avoir ajoutés à la commande
        $panier->delete();

        return response()->json([
            'status' => 200,
            'status_message' => 'Commande créée avec succès',
            'user' => [
                'prenom' => $user->prenom,
                'nom' => $user->nom,
            ],
            'commande' => [
                'numeroCommande' => $commande->numeroCommande,
                'dateCommande' => $commande->dateCommande,
                'adresse_de_livraison' => $commande->adresse_de_livraison,
                'statut' => $commande->enAttente,
                'produit_id' => $produit->produit->nomProduit,
            ],
            'produits' => [
                'montantTotal' => $montantTotal,
                'quantiteTotal' => $quantiteTotal,
            ],
        ]);
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
