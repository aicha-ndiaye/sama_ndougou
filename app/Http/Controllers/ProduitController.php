<?php

namespace App\Http\Controllers;

use App\Models\Produit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProduitController extends Controller
{


    public function createProduit(Request $request)
    {
        $user = auth()->user();

        // Vérifie si l'utilisateur est connecté
        if ($user) {
            if($user->role_id == 1) {
                $validator = Validator::make($request->all(), [
                    'nomProduit' => 'required|string',
                    'prix' => 'required|numeric',
                    'quantiteTotale' => 'required|integer',
                    'description' => 'required|string',
                    'image' => ['image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
                ]);

                if ($validator->fails()) {
                    return response()->json(['errors' => $validator->errors()], 422);
                }

                $imagePath = null;

                if ($request->hasFile('image')) {
                    $image = $request->file('image');
                    $imageName = time().'.'.$image->getClientOriginalExtension();
                    $imagePath = $image->storeAs('images', $imageName, 'public');
                }

                // Vérifie si le produit existe déjà
                $produitExistant = Produit::where('nomProduit', $request->nomProduit)->first();

                if ($produitExistant) {
                    // Incrémente la quantité totale du produit
                    $produitExistant->quantiteTotale += $request->quantiteTotale;
                    $produitExistant->save();

                    return response()->json(['message' => 'produit ajouté avec succée', 'produit' => $produitExistant], 201);
                } else {
                    // Ajoute le produit
                    $produit = Produit::create([
                        'nomProduit' => $request->nomProduit,
                        'prix' => $request->prix,
                        'quantiteTotale' => $request->quantiteTotale,
                        'image' => $imagePath,
                        'description' => $request->description,
                    ]);

                    return response()->json(['message' => 'produit ajouté avec succée', 'produit' => $produit], 201);
                }
            }

            // Si l'utilisateur n'est pas admin, renvoie une réponse non autorisée
            return response()->json(['message' => 'Non autorisé seul ldadmin peut ajouter un produit'], 401);
        }
    }

    public function updateProduit(Request $request, $id)
    {
        $user = auth()->user();

        if ($user) {
            if ($user->role_id == 1) {
                $validator = Validator::make($request->all(), [
                    'nomProduit' => 'required|string',
                    'prix' => 'required|numeric',
                    'quantiteTotale' => 'required|integer',
                    'description' => 'required|string',
                    'categorie_produit'=>'required|numeric',
                    'image' => ['image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
                ]);

                if ($validator->fails()) {
                    return response()->json(['errors' => $validator->errors()], 422);
                }

                $produit = Produit::find($id);

                if (!$produit) {
                    return response()->json(['message' => 'produit non trouvée'], 404);
                }

                $imagePath = null;
                if ($request->hasFile('image')) {
                    $image = $request->file('image');
                    $imageName = time() . '.' . $image->getClientOriginalExtension();
                    $imagePath = $image->storeAs('images', $imageName, 'public');
                }

                // Calculer la différence de quantité
                $quantiteDifference = $request->quantiteTotale - $produit->quantiteTotale;

                // Mettre à jour la quantité
                $produit->quantiteTotale = $request->quantiteTotale;

                // Mettre à jour les autres champs
                $produit->nomProduit = $request->nomProduit;
                $produit->prix = $request->prix;
                $produit->image = $imagePath;
                $produit->description = $request->description;

                $produit->save();

                return response()->json(['message' => 'produit modifiée avec succès'], 200);
            }
        }

        return response()->json(['message' => 'Non autorisé'], 401);
    }

    public function deleteProduit($id)
    {
        $user = auth()->user();

        if ($user) {
            if ($user->role_id == 1) {
                $produit = Produit::find($id);

                if (!$produit) {
                    return response()->json(['message' => 'produit non trouvée'], 404);
                }

                $produit->delete();

                return response()->json(['message' => 'produit supprimé avec succès'], 200);
            } else {
                return response()->json(['message' => 'Vous n\'êtes pas autorisé à effectuer cette action'], 401);
            }
        } else {
            return response()->json(['message' => 'Vous devez être connecté pour effectuer cette action'], 401);
        }
    }


    public function index()
    {
        $produit = Produit::all();
        return response()->json([
            "La listes de tous les produit "=>$produit
        ], 200);
    }


    public function rechercheProduit(Request $request)
    {
        $produit = Produit::findOrFail($request->id);
        return response()->json([
            "message"=>"Voici le produit que vous cherchez",
            "produit"=>$produit
            ], 200);
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

}
