<?php

namespace App\Http\Controllers;

use App\Models\Produit;
use App\Models\Commande;
use Illuminate\Http\Request;
use App\Models\detailProduit;
use Illuminate\Support\Facades\Auth;

class detailpoduitController extends Controller
{


    public function detailCommande(Request $request , $id)
    {
      $user = Auth::guard('api')->user();
      $userId = $user->id;

      // Récupérez l'ID de la commande à partir de la requête
      $commandeId = $request->input('commande_id');

      // Vérifiez si la commande existe
      $commande = Commande::find($id)->first();
        // dd($commande);
      if (!$commande) {
        return response()->json(['error' => 'La commande spécifiée n\'existe pas'], 404);
      }

      // Récupérez les détails de la commande
     $commandeDetails = Commande::with('produits')->find($commandeId);
        dd($commandeDetails);
      // Calculez le montant total de la commande
      $montantTotal = 0;
      foreach ($commandeDetails->produits as $produit) {
        $montantTotal += $produit->montant;
      }

      // Retournez les détails de la commande
      return response()->json([
        'message' => 'detail commande',
        'user_id' => $userId,
        'commande_id' => $commandeId,
        'montant_total' => $montantTotal,
        'produits' => $commandeDetails->produits,
      ], 200);
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
