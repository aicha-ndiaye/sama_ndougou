<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    
    public function testresgisterUser(): void
    {
        $user = User::factory()->create();
        $unserinsert = $user->toArray();
        $this->assertDatabaseHas('Users', $unserinsert);
    }


    public function testLoginUser(): void
    {
        $user = User::factory()->create();
        $credentials = ['email' => $user->email, 'password' => $user->password];
        $response = $this->post('api/login', $credentials);
        $response->assertStatus(200);
    }

    public function testLoginAdmin(): void
    {
        $credentials = ['email' => 'aicha@gmail.com', 'password' => 'passer123'];
        $response = $this->post('api/loginadmin', $credentials);
        $response->assertStatus(200);
    }

    // public function testBloquerTemporairementUtilisateur(): void
    // {
    //     $user = User::factory()->create();
    //     $this->actingAs($user, 'api');
    //     $user_id=5;
    //     $response = $this->post('api/BlockerTemporairement/'.$user_id);
    //     $response->assertStatus(200);
    // }

    // public function testBloquerDefinitivementUtilisateur(): void
    // {
    //     $user = User::factory()->create();
    //     $this->actingAs($user, 'api');
    //     $user_id=6;
    //     $response = $this->post('api/BlockerDefinitivement/'.$user_id);
    //     $response->assertStatus(200);
    // }
    // public function testDebloquerUtilisateur(): void
    // {
    //     $user = User::factory()->create();
    //     $this->actingAs($user, 'api');
    //     $user_id=5;
    //     $response = $this->post('api/Debloquert/'.$user_id);
    //     $response->assertStatus(200);
    // }
}
