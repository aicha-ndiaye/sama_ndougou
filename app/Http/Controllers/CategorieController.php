<?php

namespace App\Http\Controllers;

use App\Models\categorieProduit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class CategorieController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function createCategorieProduits(Request $request)
    {
        $user = auth()->user();

        // Vérifie si l'utilisateur est connecté
        if ($user) {
            // Vérifie si son rôle est égal à 1 (admin)
            if ($user->role_id == 1){
                $validator = Validator::make($request->all(), [
                    'nomCategorie' => 'required|string',
                ]);

                if ($validator->fails()) {
                    return response()->json(['errors' => $validator->errors()], 422);
                }

                $categorieProduits = categorieProduit::create([
                    'nomCategorie' => $request->nomCategorie,
                ]);

                return response()->json(['message' => 'categorie ajoutée avec succès', 'categorieProduits' => $categorieProduits], 201);
            }

            // Si l'utilisateur n'est pas admin, renvoie une réponse non autorisée
            return response()->json(['message' => 'Non autorisé'], 401);
        }
    }

    public function supprimerCategorieProduit($id)
    {

        if (!auth()->check() || auth()->user()->role_id !==1 ) {
            return response()->json(['message' => 'Non autorisé'], 401);
        }


        $categorieProduits = categorieProduit::find($id);

        if (!$categorieProduits) {
            return response()->json(['message' => 'categorie non trouvé'], 404);
        }

        $categorieProduits->delete();

        return response()->json(['message' => 'cateorie supprimer avec succé'], 200);
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


}
