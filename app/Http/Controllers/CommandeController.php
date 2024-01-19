<?php

namespace App\Http\Controllers;

use App\Models\Commande;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class CommandeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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

            // Créez la commande
            $commande = Commande::create([
                'user_id' => $userId,
                'numeroCommande' => Commande::count() + 1,
                'dateCommande' => $dateCommande,
                'statut' => 'enAttente',
            ]);

            // Attachez les produits à la commande
            $commande->produits()->attach($request->produitId, ['quantite' => $request->quantite]);
             // Retournez la commande
            return response()->json(['message' => 'Commande enregistrée avec succès', 'commande' =>[
                'user' => $userprenom,
                'prenom' => $usernom,
                'numeroCommande' => Commande::count(),
                'dateCommande' => $dateCommande,
                'statut' => 'enAttente',
            ]], 201);
        }
        return response()->json(['message' => 'Non autorisé seul un client peut passer une commande'], 401);
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
