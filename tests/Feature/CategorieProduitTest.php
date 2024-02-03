<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Role;
use App\Models\User;
use Database\Factories\CategorieProduitFactory;

class CategorieProduitTest extends TestCase
{
    public function testCreerCategorie()
{
    // Créez un rôle (si nécessaire) ou utilisez un rôle existant
    $role = Role::firstOrCreate(['nomRole' => 'admin']);

    // Utilisez une adresse e-mail unique à chaque exécution du test
    $email = 'aicha' . uniqid() . '@gmail.com';

    // Créez un utilisateur avec le rôle et l'adresse e-mail unique
    $user = User::factory()->create(['role_id' => $role->id, 'email' => $email]);

    $this->actingAs($user, 'api');

    // Générez dynamiquement les données de la catégorie de produit en utilisant la factory
    $categorieData = CategorieProduitFactory::factory()->make()->toArray();
    

    // Effectuez une requête POST pour créer une catégorie de produit
    $response = $this->postJson('api/createCategorieProduits', $categorieData);

    // Assurez-vous que la requête a réussi
    $response->assertStatus(201);
}



}
