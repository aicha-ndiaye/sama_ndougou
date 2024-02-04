<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Role;
use App\Models\User;
use Database\Factories\CategorieProduitFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CategorieProduitTest extends TestCase
{
    use RefreshDatabase;

    public function testCreerCategorieProduit()
    {
        $role = Role::firstOrCreate(['nomRole' => 'admin']);
        $user = User::factory()->create(['role_id' => $role->id]);
        $this->actingAs($user, 'api');

        // Effectuez une requête POST pour créer une catégorie de blog
        $response = $this->postJson('api/createCategorieProduits', [
            'nomCategorie' => 'Nom de la catégorie'
        ]);
        $response->assertStatus(201);
        $this->assertDatabaseHas('categorie_produits', [
            'nomCategorie' => 'Nom de la catégorie',
        ]);
    }



}
