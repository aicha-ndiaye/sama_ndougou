<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Role;
use App\Models\User;
use App\Models\Produit;
use Faker\Factory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;


class ProduitTest extends TestCase
{
    use RefreshDatabase;

    public function testAjoutProduit()
    {
        // Créez un rôle (si nécessaire) ou utilisez un rôle existant
        $role = Role::firstOrCreate(['nomRole' => 'admin']);

        // Créez un utilisateur avec le rôle
        $user = User::factory()->create(['role_id' => $role->id]);
        $this->actingAs($user, 'api');

        // Créez un produit
        $produit = Produit::factory()->create();

        // Assurez-vous que l'utilisateur est enregistré dans la base de données
        $this->assertDatabaseHas('users', $user->toArray());

        // Assurez-vous que le produit est enregistré dans la base de données
        $this->assertDatabaseHas('produits', $produit->toArray());
    }


    public function testUpdateProduit()
    {
        // Créez un rôle (si nécessaire) ou utilisez un rôle existant
        $role = Role::firstOrCreate(['nomRole' => 'admin']);

        // Créez un utilisateur avec le rôle
        $user = User::factory()->create(['role_id' => $role->id]);
        $this->actingAs($user, 'api');

        // Créez un produit existant dans la base de données
        $produit = Produit::factory()->create();

        // Nouvelles données pour la mise à jour
        $faker = Factory::create();
        $nouvellesDonnees = [
            'nomProduit' => 'Nouveau Nom de Produit',
            'description' => 'Nouvelle Description',
            'prix' => 20.99,
            'quantiteTotale' => 2,
            'image' => UploadedFile::fake()->image('structure.jpg'),
        ];

        // Effectuez une requête PUT pour mettre à jour le produit
        $response = $this->putJson("api/updateProduit/{$produit->id}", $nouvellesDonnees);

        // Assurez-vous que la requête a réussi
        $response->assertStatus(200);

        // Assurez-vous que le produit dans la base de données a été mis à jour
        $this->assertDatabaseHas('produits', array_merge(['id' => $produit->id]));
    }



    public function testListeProduit()
    {
        // Assurez-vous qu'il y a au moins un produit dans la base de données
        Produit::factory()->create();

        // Effectuez une requête GET pour lister les produits
        $response = $this->get('api/indexProduit');

        // Assurez-vous que la requête a réussi
        $response->assertStatus(200);
    }
    public function testDeleteProduit()
    {
        // Créez un rôle (si nécessaire) ou utilisez un rôle existant
        $role = Role::firstOrCreate(['nomRole' => 'admin']);

        // Créez un utilisateur avec le rôle
        $user = User::factory()->create(['role_id' => $role->id]);
        $this->actingAs($user, 'api');

        // Assurez-vous qu'il y a au moins un produit dans la base de données
        $produit = Produit::factory()->create();

        // Effectuez une requête DELETE pour supprimer le produit
        $response = $this->delete("api/deleteProduit/{$produit->id}");

        // Assurez-vous que la requête a réussi
        $response->assertStatus(200);

        // Assurez-vous que le produit a été supprimé de la base de données
        $this->assertDatabaseMissing('produits', ['id' => $produit->id]);
    }


}



