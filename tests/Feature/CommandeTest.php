<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Role;
use App\Models\User;
use App\Models\Commande;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CommandeTest extends TestCase
{
    public function testCreerCommande()
    {
        // Créez un rôle (si nécessaire) ou utilisez un rôle existant
        $role = Role::firstOrCreate(['nomRole' => 'client']);

        // Créez un utilisateur avec le rôle
        $user = User::factory()->create(['role_id' => $role->id]);
        $this->actingAs($user, 'api');

        // Générez dynamiquement les données de la commande en utilisant la factory
        $commandeData = Commande::factory()->make()->toArray();

        // Effectuez une requête POST pour créer une commande
        $response = $this->postJson('api/createCommande', $commandeData);

        // Assurez-vous que la requête a réussi
        $response->assertStatus(201);
    }


}
