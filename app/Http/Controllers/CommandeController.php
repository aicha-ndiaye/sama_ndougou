<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Panier;
use App\Models\Livreur;
use App\Models\Produit;
use App\Models\Commande;
use App\Models\Livraison;
use Illuminate\Http\Request;
use App\Models\detailProduit;
use Illuminate\Support\Carbon;
use App\Notifications\gererCommande;
use Illuminate\Support\Facades\Auth;
use App\Notifications\CommandeEnCours;
use App\Notifications\CommandeEnAttente;
use App\Http\Requests\createCommandeRequest;

class CommandeController extends Controller
{

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


    public function createCommande(createCommandeRequest $request)
{
    $user = Auth::guard('api')->user();
    // $panier = Panier::where('user_id', $user->id)->get();

    // if (count($panier) == 0) {
    //     return response()->json(['status' => 404, 'status_message' => 'Le panier est vide ou n\'existe pas.']);
    // }

    try {
    $commande = Commande::create([
        'dateCommande' => Carbon::now(),
        'user_id' => $user->id,
        'numeroCommande' => Commande::count() + 1,
        'adresse_de_livraison' => $request->adresse_de_livraison,
    ]);

    $montantTotal = 0;
    // $quantiteTotal = 0;

    // $produitsPanier = Panier::where('user_id', $user->id)->with('produit')->get();

    // foreach ($produitsPanier as $produit) {
    //     $montantTotal += $produit->quantite * $produit->produit->prix;
    //     $quantiteTotal += $produit->quantite;

    //     DetailProduit::create([
    //         'commande_id' => $commande->id,
    //         'produit_id' => $produit->produit->id,
    //         'montant' => $produit->quantite * $produit->produit->prix,
    //         'nombre_produit' => $produit->quantite,
    //     ]);
      $user=User::where('id',$commande->user_id)->first();
        $user->notify(new CommandeEnAttente());

        // $produit->delete();
        foreach ($request->input('panier') as $produit) {
            DetailProduit::create([
                'commande_id' => $commande->id,
                'produit_id' => $produit['produit_id'],
                'nombre_produit' => $produit['nombre_produit'],
                'montant' => $produit['montant'],
            ]);

            Produit::where('id', $produit['produit_id'])->decrement('quantiteTotale', $produit['nombre_produit']);

            // Ajouter le montant du produit au montant total
            $montantTotal += $produit['montant'];
        }
         $montantTotal+=$montantTotal;


    return response()->json([
        'status' => 200,
        'status_message' => 'Commande créée avec succès',
        'user' => [
            'prenom' => $user->prenom,
            'nom' => $user->nom,
        ],
        'commande' => [
            'numeroCommande' => $commande->numeroCommande,
            'adresse_de_livraison' => $commande->adresse_de_livraison,
            'details' => DetailProduit::where('commande_id', $commande->id)->get(),
        ],
        'produits' => [
            'montantTotal' => $montantTotal,
            // 'quantiteTotal' => $quantiteTotal,
        ],
    ]);
} catch (\Exception $e) {
    // Gestion des exceptions
    return response()->json(['status' => 500, 'status_message' => 'Une erreur est survenue lors de la création de la commande.']);
}
}


    public function commandeEnCours(Request $request, $id)
    {

        if (auth()->check()) {
            return response()->json(['message' => 'Non autorisé, vous devez vous connecter'], 401);
        }

        $user = Auth::guard('api')->user();

        if ($user->role_id !== 1) {
            return response()->json(['message' => 'Non autorisé. Seuls les administrateurs peuvent effectuer cette action.'], 403);
        }

        $commande = Commande::find($id);

        if (!$commande) {
            return response()->json(['message' => 'Commande non trouvée'], 404);
        }

        $commande->update(['statut' => 'enCours']);

        $commande->$user->notify(new CommandeEnCours());

        return response()->json(['message' => 'Votre commande est en cours de livraison', 'commande' => $commande], 200);
    }

    public function listeCommandeEnAttente()
    {
        if (auth()->check()) {
            return response()->json(['message' => 'Non autorisé, vous devez vous connecter'], 401);
        }

        $user = Auth::guard('api')->user();

        if ($user->role_id != 1 && $user->role_id != 2) {
            return response()->json(['message' => 'Non autorisé. Seuls les admins et les clients peuvent faire cette action.'], 403);
        }

        $commandesEnAttente = Commande::where('statut', 'EnAttente')->get();

        if ($commandesEnAttente->isEmpty()) {
            return response()->json(['message' => 'Aucune commande en attente trouvée'], 404);
        }

        return response()->json($commandesEnAttente, 200);
    }

    public function listeCommandeEnCours()
    {
        if (auth()->check()) {
            return response()->json(['message' => 'Non autorisé, vous devez vous connecter'], 401);
        }

        $user = Auth::guard('api')->user();

        if ($user->role_id != 1 && $user->role_id != 2) {
            return response()->json(['message' => 'Non autorisé. Seuls les admins et les clients peuvent faire cette action.'], 403);
        }

        $commandesEnCours = Commande::where('statut', 'EnCours')->get();

        if ($commandesEnCours->isEmpty()) {
            return response()->json(['message' => 'Aucune commande en attente trouvée'], 404);
        }

        return response()->json($commandesEnCours, 200);
    }


    public function ListecommandeTerminee()
    {
        if (auth()->check()) {
            return response()->json(['message' => 'Non autorisé, vous devez vous connecter'], 401);
        }

        $user = Auth::guard('api')->user();

        // On autorise à la fois les utilisateurs et les administrateurs
        if ($user->role_id != 1 && $user->role_id != 2) {
            return response()->json(['message' => 'Non autorisé. Seuls les admins et les clients peuvent faire cette action.'], 403);
        }

        $commandesEnCours = Commande::where('statut', 'EnCours')->get();

        if ($commandesEnCours->isEmpty()) {
            return response()->json(['message' => 'Aucune commande en attente trouvée'], 404);
        }

        return response()->json($commandesEnCours, 200);
    }


    public function deleteCommande($id)
    {
        $user = Auth::guard('api')->user();

        if ($user) {
            if ($user->role_id != 2 && $user->role_id != 2) {
                $commande = commande::find($id);

                if (!$commande) {
                    return response()->json(['message' => 'commande non trouvée'], 404);
                }

                $commande->delete();

                return response()->json(['message' => 'commande supprimé avec succès'], 200);
            } else {
                return response()->json(['message' => 'Vous n\'êtes pas autorisé à effectuer cette action'], 401);
            }
        } else {
            return response()->json(['message' => 'Vous devez être connecté pour effectuer cette action'], 401);
        }
    }
     public function detailCommande(Commande $commande){
        $user = Auth::guard('api')->user();
        if ($user->id==$commande->user_id){
            return response()->json($commande);
        }else{
            return response()->json([
                'message'=>'Commande introuvable',
                'StatusCode'=>400
            ]);
        }

     }
}
