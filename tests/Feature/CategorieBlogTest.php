<?php

namespace Tests\Feature;

use App\Http\Controllers\categorieBlogController;
use Tests\TestCase;
use App\Models\Role;
use App\Models\User;
use Database\Factories\CategorieBlogFactory;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CategorieBlogTest extends TestCase
{
    public function testCreerCategorieBlog()
    {
        $role = Role::firstOrCreate(['nomRole' => 'admin']);

        // Créez un utilisateur avec le rôle
        $user = User::factory()->create(['role_id' => $role->id]);
        $this->actingAs($user);

        $categorieData = [
            'nomCategorie' => 'astuce'
        ];

        // Effectuez une requête POST pour créer une catégorie de blog
        $response = $this->post('api/createCategorieBlog', $categorieData);

        // Assurez-vous que la requête a réussi
        $response->assertStatus(201);
    }

    }



