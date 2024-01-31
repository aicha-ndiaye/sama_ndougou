<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthentificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_register_user()
    {
        $data = [
            'nom' => 'Wade',
            'prenom' => 'Mariam',
            'email' => 'mane@gmail.com',
            'password' => 'passer1234',
            'adresse' => 'keur Massar',
            'telephone' => '778009876',

        ];

        $response = $this->json('POST', '/api/inscriptionClient', $data);

        $response->assertStatus(200)
                 ->assertJson([
                     'status' => 200,
                     'message' => 'Utilisateur créer avec succes',
                 ]);
    }



    public function test_registerLivreur(){

        $admin = User::factory()->create(['type' => 'admin']);

        $data = [
            'nom' => 'LivreurTest',
            'prenom' => 'LivreurPrenom',
            'email' => 'livreur@gmail.com',
            'password' => 'passer1234',
            'genre' => 'homme',
            'statut'=> 'disponible',
            'adresse' => 'keur Massar'
        ];

        $response = $this->actingAs($admin)->json('POST', '/api/inscriptionlivreur', $data);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 200,
                'message' => 'lovreur créer avec succes',
            ]);
    }

    public function test_user_login_commercant(){
        $user = User::factory()->create(['type' => 'commercant']);
        $credentials = [
            'email' => $user->email,
            'password' => 'passer1234',
        ];

        $response = $this->json('POST', '/api/login', $credentials);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 200,
                'message' => 'Salut Commercant',
                'user' => $user->toArray(),
                'authorization' => [
                    'token' => $response['authorization']['token'],
                    'type' => 'bearer',
                ]
            ]);
    }

    public function test_user_login_client(){
        $user = User::factory()->create(['type' => 'client']);
        $credentials = [
            'email' => $user->email,
            'password' => 'passer1234',
        ];

        $response = $this->json('POST', '/api/login', $credentials);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 200,
                'message' => 'Salut Client',
                'user' => $user->toArray(),
                'authorization' => [
                    'token' => $response['authorization']['token'],
                    'type' => 'bearer',
                ]
            ]);
    }

    public function test_user_login_livreur(){
        $user = User::factory()->create(['type' => 'livreur']);
        $credentials = [
            'email' => $user->email,
            'password' => 'passer1234',
        ];

        $response = $this->json('POST', '/api/login', $credentials);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 200,
                'message' => 'Salut livreur',
                'user' => $user->toArray(),
                'authorization' => [
                    'token' => $response['authorization']['token'],
                    'type' => 'bearer',
                ]
            ]);
    }

    public function test_user_login_admin(){
        $user = User::factory()->create(['type' => 'admin']);
        $credentials = [
            'email' => $user->email,
            'password' => 'passer1234',
        ];

        $response = $this->json('POST', '/api/login', $credentials);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 200,
                'message' => 'Salut Admin',
                'user' => $user->toArray(),
                'authorization' => [
                    'token' => $response['authorization']['token'],
                    'type' => 'bearer',
                ]
            ]);
    }
}
