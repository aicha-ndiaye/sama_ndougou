<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Panier;
use App\Models\Commande;
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

        // Vérifie si l'utilisateur est connecté et a le rôle approprié
        if ($user && $user->role_id == 2) {

            // Obtenez la date et l'heure actuelles
            $dateCommande = Carbon::now();

            // Obtenez l'identifiant de l'utilisateur connecté
            $userId = Auth::guard('api')->user()->id;

            // Obtenez l'ID du produit de la demande
            $produitId = $request->input('produit_id');
            $quantite = $request->input('quantite');

            // Obtenez le nom et le prénom de l'utilisateur connecté
            $usernom = Auth::guard('api')->user()->nom;
            $userprenom = Auth::guard('api')->user()->prenom;

            $panier = Panier::where('user_id', $user->id)->get();

            // Vérifiez s'il y a des produits dans le panier de l'utilisateur
            if ($panier->isEmpty()) {
                return response()->json(['error' => 'Votre panier est vide'], 400);
            }

            // Créez la commande
            $commande = Commande::create([
                'user_id' => $userId,
                'numeroCommande' => Commande::max('numeroCommande') + 1,
                'dateCommande' => $dateCommande,
                'plus_de_detail_pour_la_commande' => $request->plus_de_detail_pour_la_commande,
                'statut' => 'enAttente',
            ]);

            // Addttachez les produits à la commande
            $commande->produits()->attach($request->produitId, ['quantite' => $request->quantite]);
            $commande->save();

            // Notification après le retour de la commande
            $user->notify(new gererCommande());

            // Supprimez tous les produits du panier de l'utilisateur après la création de la commande
            Panier::where('user_id', $user->id)->delete();

            // Retournez la commande
            return response()->json([
                'message' => 'Commande enregistrée avec succès',
                'commande' => [
                    'user' => $userprenom,
                    'prenom' => $usernom,
                    'numeroCommande' => $commande->numeroCommande,
                    'dateCommande' => $dateCommande,
                    'plus_de_detail_pour_la_commande' => $request->plus_de_detail_pour_la_commande,
                    'statut' => $commande->statut,
                ]
            ], 201);
        }

        return response()->json(['message' => 'Non autorisé seul un client peut passer une commande'], 401);
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
