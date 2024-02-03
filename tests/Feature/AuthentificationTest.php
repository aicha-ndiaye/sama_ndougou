<?php

namespace Tests\Feature;

use App\Models\Livreur;
use Tests\TestCase;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthentificationTest extends TestCase
{
    use RefreshDatabase;


    public function testRole(): void
    {
        $user = Role::factory()->create();
        $unserinsert = $user->toArray();
        $this->assertDatabaseHas('roles', $unserinsert);

    }


    public function testresgisterclient(): void
{
    $role = Role::factory()->create(['nomRole' => 'client']);
    $user = User::factory()->create(['role_id' => $role->id]);
    $this->assertDatabaseHas('users', $user->toArray());
}

    public function testLoginclient(): void
    {
        $credentials = ['email' => 'bichasen@gmail.com', 'password' => 'passer123'];
        $response = $this->post('api/login', $credentials);
        $response->assertStatus(200);
     }

     public function testLoginLivreur(): void
     {
         $credentials = ['email' => 'magid@gmail.com', 'password' => 'passer123'];
         $response = $this->post('api/login', $credentials);
         $response->assertStatus(200);
      }


    public function testLoginAdmin(): void
    {
        $credentials = ['email' => 'aicha8420@gmail.com', 'password' => 'passer123'];
        $response = $this->post('api/login', $credentials);
        $response->assertStatus(200);
     }

}
