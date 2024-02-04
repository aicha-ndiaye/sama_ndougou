<?php

namespace Tests\Feature;

use App\Http\Controllers\categorieBlogController;
use App\Models\CategorieBlog;
use Tests\TestCase;
use App\Models\Role;
use App\Models\User;
use Database\Factories\CategorieBlogFactory;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CategorieBlogTest extends TestCase
{
    use RefreshDatabase;

    public function testCreerCategorieBlog()
    {
        $role = Role::firstOrCreate(['nomRole' => 'admin']);
        $user = User::factory()->create(['role_id' => $role->id]);
        $this->actingAs($user, 'api');

        // Effectuez une requête POST pour créer une catégorie de blog
        $response = $this->postJson('api/createCategorieBlog', [
            'nomCategorie' => 'Nom de la catégorie'
        ]);
        $response->assertStatus(201);
        $this->assertDatabaseHas('categorie_blogs', [
            'nomCategorie' => 'Nom de la catégorie',
        ]);
    }

    }



