<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Role;

use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_User()
    {

        $role = Role::factory()->create([
            'nomRole' => 'admin',
        ]);
        $user = User::factory()->create([
            'role_id' => $role->id,
        ]);

        $this->assertDatabaseHas('users', $user->toArray());
    }

    public function testInscriptionClient()
{
    $role = Role::factory()->create(['nomRole' => 'client']);

    $userData = [
        'nom' => 'ndiaye',
        'prenom' => 'aicha',
        'email' => 'aicha@gmail.com',
        'password' => 'passer123',
        'adresse' => 'liberte6',
        'telephone' => '77340049',
        // Ajoutez d'autres champs requis pour l'inscription
    ];

    $response = $this->postJson('/api/inscriptionClient', $userData);

    $response->assertStatus(201); // Assurez-vous que la requête a réussi

    // Assurez-vous que la réponse contient le message attendu ou tout autre indicateur de réussite
    $response->assertJson(['message' => 'client ajouté avec succès']);

    // Assurez-vous que l'utilisateur est présent dans la base de données
    $this->assertDatabaseHas('users', ['email' => $userData['email']]);
}








    // public function testInscriptionClient()
    // {

    //  $role = Role::factory()->create(['nomRole' => 'client']); // Ou tout autre rôle approprié
    // $user = User::factory()->create(['role_id' => $role->id]);
    // $response = $this->actingAs($user)->post('/api/inscriptionClient');
    // }

    public function testLogin()
    {
        $user= User::factory()->create();
        $credential= ['email'=>$user->email,'password'=>'password'];
        $response = $this->post('/api/login',$credential);
        $response->assertStatus(200)
        ->assertSuccessful([
            'email' => 'une adresse  email doit etre fournie',
            'password' => 'Le mot de passe est requis et doit avoir au minimum 7 caractères',
        ]);
    }
    public function    testDeconnect()
    {
        $user = User::factory()->create([
            'password' => bcrypt('password'),
        ]);
        $token = JWTAuth::fromUser($user);
        $response = $this->withHeader('Authorization', 'Bearer' . $token)
                         ->post('/api/deconnect');
        $response->assertStatus(200);
    }
     public function testCreateProduit()
     {
        $user = User::factory()->create([
            'email'=>'aicha@gmail.com',
            'password'=>bcrypt('passer123'),
            'role'=>'admin'
        ]);
        $token = JWTAuth::fromUser($user);

        $response = $this->withHeader('Authorization', 'Bearer' . $token)
                         ->get('api/createProduit');
        $response->assertStatus(200);
     }

    //    public function test_user_can_vienAny()
    //    {
    //     $user = User::factory()->create([
    //         'email'=>'admin@example.fr',
    //         'password'=>bcrypt('password'),
    //         'role'=>'admin'
    //     ]);
    //     $token = JWTAuth::fromUser($user);
    //     $response = $this->withHeader('Authorization', 'Bearer ' . $token)
    //                      ->get('api/listClients');
    //     $response->assertStatus(200);
    //    }

    // $user = User::factory()->create();
        // $response = $this->actingAs($user)->post('/api/inscriptionClient');
        // $response->assertStatus(200);

       public function testModifieProfileAdmin()
       {
        $user = User::factory()->create([
            'email'=>'aichagmail.com',
            'password'=>bcrypt('passer123')
        ]);
        $token = JWTAuth::fromUser($user);
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                         ->post('api/modifieProfileAdmin');
        $response->assertStatus(403);
       }
}

