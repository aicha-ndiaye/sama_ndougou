<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Role;
use App\Models\Produit;
use Illuminate\Foundation\Auth\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class panierTest extends TestCase
{

    use RefreshDatabase;

    public function testAjoutPanier()
    {
        // Créez un rôle (si nécessaire) ou utilisez un rôle existant
        $role = Role::firstOrCreate(['nomRole' => 'client']);

        // Créez un utilisateur avec le rôle
        $user = User::factory()->create(['role_id' => $role->id]);
        $this->actingAs($user, 'api');

        // Créez un produit
        $produit = Produit::factory()->create();

        // Définissez les données du panier
        $panierData = [
            'quantite' => '10',
            'produit_id' => $produit->id,
        ];

        // Effectuez une requête POST pour créer un panier
        $response = $this->post('api/createPanier', $panierData);

        // Assurez-vous que la requête a réussi
        $response->assertStatus(201);

        // Vérifiez que le panier a été créé
        $this->assertDatabaseHas('paniers', [
            'quantite' => $panierData['quantite'],
            'produit_id' => $panierData['produit_id'],
            'user_id' => $user->id,
        ]);
    }
}
