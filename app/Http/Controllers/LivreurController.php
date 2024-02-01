<?php

namespace App\Http\Controllers;

use App\Models\Livreur;
use App\Models\Commande;
use App\Models\Livraison;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LivreurController extends Controller
{
    public function changerStatut(Request $request)
    {
        // Vérifiez si l'utilisateur est connecté
        if (!Auth::guard('api')->check()) {
            return response()->json(['message' => 'Vous devez être connecté pour accéder à cette ressource'], 401);
        }

        // Obtenez le livreur à partir de l'ID de l'utilisateur connecté
        $livreur = Livreur::where('user_id', Auth::guard('api')->user()->id)->first();
        if (!$livreur) {
            return response()->json(['message' => 'Livreur non trouvé'], 404);
        }

        // Vérifiez si le livreur a le rôle 3
        if ($livreur->role_id === 3) {
        }

        // Vérifiez si la demande contient un champ 'statut' valide
        $request->validate([
            'statut' => 'required|in:disponible,occupe',
        ]);

        // Mettez à jour le statut du livreur
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

        if (!$user || $user->role_id !== 1) {
            return response()->json([
                'status' => 403,
                'status_message' => 'Vous n\'avez pas les droits pour accéder à cette ressource',
            ]);
        }

        // Vérifiez si la commande est déjà affectée à un livreur
        if ($commande->livreur_id == null) {
            return response()->json([
                'status' => 400,
                'status_message' => 'La commande a déjà été affectée à un livreur.',
            ]);
        }

        // Obtenez le livreur à partir de l'ID fourni dans la requête
        $livreur = Livreur::where("statut", "disponible")->first();

        if (!$livreur) {
            return response()->json([
                'status' => 404,
                'status_message' => 'Livreur non trouvé',
            ]);
        }

        // Mettez à jour le statut du livreur et sauvegardez
        $livreur->statut = 'occupe';
        $livreur->save();

        // Mettez à jour la commande avec l'ID du livreur
        $commande->update([
            'livreur_id' => $livreur->id,
            'statut' => 'enCours',
        ]);

        // Créez une nouvelle livraison
        $livraison = new Livraison([
            'livreur_id' => $livreur->id,
            'commande_id' => $commande->id,
            'dateLivraison' => now(),
        ]);

        // Sauvegardez la livraison
        $livraison->save();

        return response()->json([
            'status' => 200,
            'status_message' => 'Livreur affecté',
            'data' => $commande,
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
        } catch (Exception $e) {
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
        } catch (Exception $e) {
            return response()->json([
                'status' => 500,
                'status_message' => 'Erreur lors de la récupération des livreurs occupés',
                'error' => $e->getMessage(),
            ]);
        }
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
