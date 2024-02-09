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
        // Vérifiez si l'utilisateur est connecté et a le rôle d'administrateur
        $user = Auth::guard('api')->user();
        if (!$user || $user->role_id != 1) {
            return response()->json([
                'status' => 403,
                'status_message' => 'Vous n\'avez pas les droits pour affecter un livreur à cette commande',
            ]);
        }

        // Vérifiez si la commande est déjà affectée à un livreur
        $exist = Livraison::where('commande_id', $commande->id)->first();
        if ($exist) {
            return response()->json([
                'status' => 400,
                'status_message' => 'La commande a déjà été affectée à un livreur.',
            ]);
        }

        $livreur= Livreur::where('statut','disponible')->first();
        //  dd($livreur);
        $livreur->notify(new affecterClient());

        $user= User::where('id',$commande->user_id)->first();
        $user->notify(new CommandeEnCours());

        // **Ajout d'une vérification pour s'assurer qu'un livreur est disponible**
        $livreurDisponible = Livreur::where('statut', 'disponible')->first();
        if (!$livreurDisponible) {
            return response()->json([
                'status' => 404,
                'status_message' => 'Aucun livreur disponible pour le moment.',
            ]);
        }

        // Mettez à jour le statut du livreur et sauvegardez
        $livreurDisponible->statut = 'occupe';
        $livreurDisponible->save();

        // Créez une nouvelle livraison
        $livraison = new Livraison([
            'livreur_id' => $livreurDisponible->id,
            'commande_id' => $commande->id,
            'dateLivraison' => now(),
        ]);
 // Notifiez le livreur de la nouvelle commande
        $livreurDisponible->user->notify(new nouvelleCommande());

        // Sauvegardez la livraison
        $livraison->save();

        // Mettez à jour la commande avec l'ID du livreur
        $commande->update([
            'user_id' => $livreurDisponible->user->id,
            'statut' => 'enCours',
        ]);

        return response()->json([
            'status' => 200,
            'status_message' => 'Livreur affecté avec succès',
            'data' => $livraison,
        ]);
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

        $livreur = Livreur::find(auth()->user()->livreur->id);

        // ON RECUPERE  la livraison associée à la commande
        $livraisons = Livraison::where('livreur_id', $livreur->id)
            ->where('commande_id', $commandeId)
            ->get();
        //     $user=User::where('id',$commande->user_id)->first();
        // $user->notify(new CommandeEnAttente());

        // ICI On verifie si la livraison existe
        if ($livraisons->all() == null) {
            return response()->json(['message' => 'Livraison non trouvée'], 404);
        }

        // onverifie si la cmmande n'est pas affecte a quelqu'un
        foreach ($livraisons as $livraison) {
            if ($livraison->livreur_id !== $livreur->id) {
                return response()->json(['message' => 'Vous n\'avez pas le droit de modifier cette livraison'], 403);
            }

            //  on verifie si la commande est déjà terminée avant de le modifier
            if ($livraison->statut === 'terminee') {
                return response()->json(['message' => 'Désolé, la commande est déjà terminée. Vous ne pouvez pas la modifier.'], 403);
            }

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
}
