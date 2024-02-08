<?php

namespace App\Http\Controllers;

use App\Http\Requests\InscriptionClientRequest;
use App\Http\Requests\InscriptionLivreurRequest;
use App\Models\Livreur;
use App\Models\Role;
use App\Models\User;
use App\Notifications\motDePasseOublie;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Auth;

class userController extends Controller
{

    public function ajouterRole(Request $request)
    {
        $request->validate([
            'nomRole' => 'required|string|unique:roles',
        ]);

        $role = Role::create([
            'nomRole' => $request->nomRole,
        ]);

        return response()->json(['message' => 'Rôle ajouté avec succès', 'role' => $role], 201);
    }

    public function inscriptionClient(InscriptionClientRequest $request)
    {
        $roleClient = Role::where('nomRole', 'client')->first();
        // $user = User::create([
            $user = new user();
            $user->nom = $request->nom;
            $user->prenom= $request->prenom;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
            $user->adresse = $request->adresse;
            $user->telephone =$request->telephone;
            $user->role_id = $roleClient->id;

          $this->saveImage($request, 'image', 'images', $user, 'image');
          $user->save();

        // ]);

        return response()->json(['message' => 'client ajouté avec succès',$user], 201);
    }

    private function saveImage($request, $fileKey, $path, $produit, $fieldName)
    {
        if ($request->file($fileKey)) {
            $file = $request->file($fileKey);
            $filename = date('YmdHi') . $file->getClientOriginalName();
            $file->move(public_path($path), $filename);
            $produit->$fieldName = $filename;
        }
    }

    public function inscriptionlivreur(InscriptionLivreurRequest $request)
{
    $imagePath = null;

    if ($request->hasFile('image')) {
        $image = $request->file('image');
        $imageName = time() . '.' . $image->getClientOriginalExtension();
        $imagePath = $image->storeAs('images', $imageName, 'public');
    }

    $roleLivreur = Role::where('nomRole', 'livreur')->first();

    $user = User::create([
        'nom' => $request->nom,
        'prenom' => $request->prenom,
        'email' => $request->email,
        'password' => Hash::make($request->password),
        'adresse' => $request->adresse,
        'role_id' => $roleLivreur->id,
        'telephone' => $request->telephone,
        'image' => $imagePath,
    ]);

    $livreur = $user->livreur()->create([
        'statut' => $request->statut ?? 'occupé',
    ]);

    return response()->json(['message' => 'Livreur ajouté avec succès', 'user' => $livreur], 201);
}

    public function login(Request $request)
    {
        // data validation
        $validator = Validator::make($request->all(), [
            "email" => "required|email",
            "password" => Rules\Password::defaults()
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // JWTAuth
        $token = JWTAuth::attempt([
            "email" => $request->email,
            "password" => $request->password
        ]);

        if (!empty($token)) {

            return response()->json([
                "status" => true,
                "message" => "utilisateur connecter avec succe",
                "token" => $token,
                "user"=>auth()->user()
            ]);
        }

        return response()->json([
            "status" => false,
            "message" => "details invalide"
        ]);
    }

    public function deconnect()
    {
        //J'utilise la façade Auth pour faire la deconnexion
        Auth::logout();
        session()->invalidate();

        session()->regenerateToken();

        return response()->json(
            [
                'status' => true,
                'message' => 'Déconnecté avec succès',
            ],
            200
        );
    }

    public function verifMail(Request $request)
    {
        $user = User::where('email', $request->email)->first();
        if ($user) {
            return response()->json([
                'status_code' => 200,
                'status_message' => 'Utilisateur trouvé',
                'user' => $user,
            ]);
        } else {
            return response()->json([
                'message' => "Cet e-mail n'existe pas dans notre base de données."
            ], 404);
        }
    }


    public function modifieProfileAdmin(Request $request)
    {
        $admin = auth()->user();

        $validator = Validator::make($request->all(), [
            'nom' => 'required|string',
            'prenom' => 'required|string',
            'email' => 'required|email|unique:users,email,' . $admin->id,
            'password' => 'required|min:6',
            'adresse' => 'required|string',
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],

        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $imagePath = $admin->image;

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $imagePath = $image->storeAs('images', $imageName, 'public');
        }

        $admin->update([
            'nom' => $request->nom,
            'prenom' => $request->prenom,
            'email' => $request->email,
            'password' => $request->password ? Hash::make($request->password) : $admin->password,
            'adresse' => $request->adresse,
            'image' => $imagePath,

        ]);

        return response()->json(['message' => 'Profil mis à jour avec succès'], 200);
    }

    public function modifierMotDePasse(Request $request)
{
    $validator = Validator::make($request->all(), [
        'nouveau_password' => Rules\Password::defaults(),
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }

    // Vérifiez si l'utilisateur est authentifié
    $user = Auth::guard('api')->user();

    if (!$user) {
        return response()->json(['message' => 'Utilisateur non authentifié'], 401);
    }
    // $userId = $user->id;

    // Vérifiez que l'ancien mot de passe est correct
    if (!Hash::check($request->ancien_password, $user->password)) {
        return response()->json(['message' => 'Mot de passe actuel incorrect'], 401);
    }

    // Mettez à jour le mot de passe avec le nouveau
    $user->password = Hash::make($request->nouveau_password);
    $user->notify(new motDePasseOublie());

    $user->save();

    return response()->json(['message' => 'Mot de passe modifié avec succès'], 200);
}




public function resetPassword(Request $request, User $user)
{
    $validator = Validator::make($request->all(), [
        'password' => ['required', Password::defaults()],
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }

    // Assurez-vous que l'utilisateur est le même que celui qui est authentifié
    if (auth()->user()->id !== $user->id) {
        return response()->json(['message' => 'Vous n\'avez pas les autorisations nécessaires'], 403);
    }

    // Mettez à jour le mot de passe avec le nouveau
    $user->password = bcrypt($request->password);
    $user->save();

    return response()->json([
        'status_code' => 200,
        'status_message' => 'Votre mot de passe a été modifié',
        'user' => $user,
    ]);
}



public function listerClients()
{
    // Utilisez Eloquent pour obtenir les utilisateurs avec le rôle spécifié (role_id == 1 pour le rôle "client")
    $users = User::where('role_id', 2)->get();

    // Retournez la liste des utilisateurs
    return $users;
}

public function listerLivreur()
{
    // Utilisez Eloquent pour obtenir les utilisateurs avec le rôle spécifié (role_id == 1 pour le rôle "client")
    $users = User::where('role_id', 3)->get();

    // Retournez la liste des utilisateurs
    return $users;
}

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
