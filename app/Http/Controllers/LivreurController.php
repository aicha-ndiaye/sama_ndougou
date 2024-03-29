<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Livreur;
use App\Models\Commande;
use App\Models\Livraison;
use Illuminate\Http\Request;
use App\Notifications\gererCommande;
use Illuminate\Support\Facades\Auth;
use App\Notifications\nouvelleCommande;
use App\Notifications\CommandeEnAttente;
use App\Http\Requests\changerStatutRequest;
use App\Models\detailProduit;
use App\Notifications\affecterClient;
use App\Notifications\CommandeEnCours;

class LivreurController extends Controller
{
    public function changerStatut(changerStatutRequest $request)

    {
        $livreur = Auth::guard('api')->user();

        $livreur = Livreur::where('user_id', Auth::guard('api')->user()->id)->first();
        if (!$livreur) {
            return response()->json(['message' => 'Livreur non trouvé'], 404);
        }

        // on verfie si le livreur a le rôle 3
        if ($livreur->role_id === 3) {
        }

        $request->validate([
            'statut' => 'required|in:disponible,occupe',
        ]);

        $livreur->update(['statut' => $request->statut]);

        return response()->json([
            'status' => 200,
            'status_message' => 'statut mise a jour avec succès',
            'livreur' => [
                'nom' => $livreur->nom,
                'prenom' => $livreur->prenom,
                'statut' => $livreur->statut,
            ],
        ]);
    }


    public function affecterLivreur(Commande $commande, Request $request)
    {
        // Vérifiez si la commande est déjà affectée à un livreur
        $exist = Livraison::where('commande_id', $commande->id)->first();
        if ($exist) {
            return response()->json([
                'status' => 400,
                'status_message' => 'La commande a déjà été affectée à un livreur.',
            ]);
        } else {
            // Ajout d'une vérification pour s'assurer qu'un livreur est disponible
            $livreurDisponible = Livreur::where('statut', 'disponible')->first();
            if (!$livreurDisponible) {
                return response()->json([
                    'status' => 404,
                    'status_message' => 'Aucun livreur disponible pour le moment.',
                ]);
            }
            $livraison = new Livraison();
            if ($livraison->livreur_id = $livreurDisponible->id) {
                $livreurDisponible->statut = 'occupe';
                $livreurDisponible->save();
                $livreurDisponible->user->notify(new nouvelleCommande());
            }
            if ($livraison->commande_id = $commande->id) {
                $commande->statut = "enCours";
                $commande->save();
                $user = User::find($commande->user_id);
                if ($user) {
                    $user->notify(new CommandeEnCours());
                }
            }
            $livraison->dateLivraison = now();
            $livraison->save();
            return response()->json([
                'status' => 200,
                'status_message' => 'Livreur affecté avec succès',
                'dat a' => $livraison,
            ]);
        }
    }


    public function listerLivreursDisponible()
    {
        try {
            $livreurs = Livreur::where('statut', 'disponible')
                ->join('users', 'livreurs.user_id', '=', 'users.id')
                ->select('livreurs.*', 'users.nom', 'users.prenom', 'users.telephone')
                ->get();

            return response()->json([
                'status' => 200,
                'status_message' => 'Liste des livreurs disponibles',
                'data' => $livreurs->map(function ($livreur) {
                    return [
                        'id' => $livreur->id,
                        'nom' => $livreur->nom,
                        'prenom' => $livreur->prenom,
                        'statut' => $livreur->statut,
                        'telephone' => $livreur->telephone,

                    ];
                }),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'status_message' => 'Erreur lors de la récupération des livreurs disponibles',
                'error' => $e->getMessage(),
            ]);
        }
    }


    public function listerLivreursOccupe()
    {
        try {
            $livreurs = Livreur::where('statut', 'occupe')
                ->join('users', 'livreurs.user_id', '=', 'users.id')
                ->select('livreurs.*', 'users.nom', 'users.prenom', 'users.telephone')
                ->get();

            return response()->json([
                'status' => 200,
                'status_message' => 'Liste des livreurs occupés',
                'data' => $livreurs->map(function ($livreur) {
                    return [
                        'id' => $livreur->id,
                        'nom' => $livreur->nom,
                        'prenom' => $livreur->prenom,
                        'statut' => $livreur->statut,
                        'telephone' => $livreur->telephone

                    ];
                }),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'status_message' => 'Erreur lors de la récupération des livreurs occupés',
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function CommandeTerminee(Request $request, $commandeId)
    {
        // $livreur = Livreur::find(auth()->user()->livreur->id);
        $livreur = Auth::guard('api')->user()->livreur;

        // ON RECUPERE  la livraison associée à la commande
        $livraisons = Livraison::where('livreur_id', $livreur->id)
            ->where('commande_id', $commandeId)
            ->get();
            // dd($livraisons);

        // ICI On verifie si la livraison existe
        if ($livraisons->all() == null) {
            return response()->json(['message' => 'Livraison non trouvée'], 404);
        }

        // onverifie si la cmmande n'est pas affecte a quelqu'un
        foreach ($livraisons as $livraison) {
            if ($livraison->livreur_id !== $livreur->id) {
                return response()->json(['message' => 'Vous n\'avez pas le droit de modifier cette livraison',
                    'statut'=>403
            ]);
            }

            //  on verifie si la commande est déjà terminée avant de le modifier
            if ($livraison->statut === 'terminee') {
                return response()->json(['message' => 'Désolé, la commande est déjà terminée. Vous ne pouvez pas la modifier.',
                'statut'=>403
            ]);

            }
            // dd($livraison);
            // Mettons à jour le statut de la livraison à "terminée" après la livraison
            $livraison->update(['statut' => 'terminée']);

            // Mettons à jour le statut du livreur à "disponible" après la livraison
            $livreur->update(['statut' => 'disponible']);

            // Mettons à jour le statut de la commande à "terminée"
            $commande = Commande::find($commandeId);
            $commande->update(['statut' => 'terminée']);
        }

        return response()->json(['message' => 'Commande livrée avec succès et marquée comme terminée'], 200);
    }

    public function ListerCommandeAffecter(){
        $user = Livreur::find(auth()->user()->livreur->id);
      // dd($user->id);
        $livraisonAffecter = Livraison::where('livreur_id', $user->id)->orderBy('created_at', 'desc')->get();
       // dd($livraisonAffecter);
        $ListecommandeAffecter = [];
        //dd($livraisonAffecter);
        foreach ($livraisonAffecter  as $livraison) {


            $ListecommandeAffecter[] = [
                'Id' => $livraison->id,
                'client'=>[
                    'Nom' => $livraison->commande->user->nom,
                    'Prenom' => $livraison->commande->user->prenom,
                    'Adresse' => $livraison->commande->adresse_de_livraison,
                ],
                'Date_commande' => $livraison->commande->created_at,
                'Etat' => $livraison->statut
            ];
        }
        return response()->json([
            'status' => 200,
            'status_message' => 'la liste des commandes',
            'data' => $ListecommandeAffecter
        ]);
    }

}
