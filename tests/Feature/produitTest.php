<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Role;
use App\Models\User;
use App\Models\Produit;
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

        // Définissez les données du produit
        $produitData = [
            'nomProduit' => 'carotte',
            'prix' => '300',
            'quantiteTotale' => '100',
            'description' => 'ceci est un legume',
            'image' => UploadedFile::fake()->image('carotte.jpg'),
        ];

        // Effectuez une requête POST pour créer un produit
        $response = $this->post('api/createProduit', $produitData);

        // Assurez-vous que la requête a réussi
        $response->assertStatus(201);
    }




    public function testListeProduit()
    {
        // Assurez-vous qu'il y a au moins un produit dans la base de données
        Produit::factory()->create();

        // Effectuez une requête GET pour lister les produits
        $response = $this->get('api/index');

        // Assurez-vous que la requête a réussi
        $response->assertStatus(200);
    }

}



// public function testAnnulerReservation(): void
// {
//     $user = Utilisateur::factory()->create();
//     $this->actingAs($user,'apiut');
//     $reservationtr=Reservation::FindOrFail(1);
//     $response = $this->delete('api/DeleteReservation/'.$reservationtr->id);
//     $response->assertStatus(200);
// }

