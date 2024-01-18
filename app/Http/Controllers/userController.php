<?php

namespace App\Http\Controllers;

use App\Models\Livreur;
use App\Models\Role;
use App\Models\User;
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


    public function inscriptionClient(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nom' => ['required', 'string', 'min:4', 'regex:/^[a-zA-Z]+$/'],
            'prenom' => ['required', 'string', 'min:4', 'regex:/^[a-zA-Z ]+$/'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => Password::defaults(),
            'adresse' => ['required', 'string', 'regex:/^[a-zA-Z0-9 ]+$/'],
            'telephone' => ['required', 'regex:/^7\d{8}$/'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $imagePath = null;
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $imagePath = $image->storeAs('images', $imageName, 'public');
        }
        $roleClient = Role::where('nomRole', 'client')->first();
        $user = User::create([
            'nom' => $request->nom,
            'prenom' => $request->prenom,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'adresse' => $request->adresse,
            'telephone'=>$request->telephone,
            'image' => $imagePath,
            'role_id' => $roleClient->id,
        ]);
        // $user->roles()->attach($roleClient);

        return response()->json(['message' => 'client ajouté avec succès'], 201);
    }


    public function inscriptionlivreur(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nom' => 'required|string|min:2|regex:/^[a-zA-Z]+$/',
            'prenom' => 'required|string|min:4|regex:/^[a-zA-Z ]+$/',
            'email' => 'required|email|unique:users,email',
            'password' => Rules\Password::defaults(),
            'adresse' => 'required|string|regex:/^[a-zA-Z0-9 ]+$/',
            'statut' => 'required|string',
            'telephone' => ['required', 'regex:/^7\d{8}$/'],
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $imagePath = null;

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $imagePath = $image->storeAs('images', $imageName, 'public');
        }
        $rolelivreur = Role::where('nomRole', 'livreur')->first();

        $user = User::create([
            'nom' => $request->nom,
            'prenom' => $request->prenom,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'adresse' => $request->adresse,
            'role_id' => $rolelivreur->id,
            'telephone'=>$request->telephone,
            'image' => $imagePath,
        ]);

        $livreur=$user->livreur()->create([
            'statut' => $request->statut,
        ]);

        // $user->roles()->attach($rolelivreur);
        return response()->json(['message' => 'livreur ajouté avec succès','user'=>$livreur], 201);
    }

    public function inscriptionAdmin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nom' => ['required', 'string', 'min:4', 'regex:/^[a-zA-Z]+$/'],
            'prenom' => ['required', 'string', 'min:4', 'regex:/^[a-zA-Z ]+$/'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => Password::defaults(),
            'adresse' => ['required', 'string', 'regex:/^[a-zA-Z0-9 ]+$/'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $imagePath = null;

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $imagePath = $image->storeAs('images', $imageName, 'public');
        }

        $roleAdmin = Role::where('nomRole', 'admin')->first();

        $user = User::create([
            'nom' => $request->nom,
            'prenom' => $request->prenom,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'adresse' => $request->adresse,
            'image' => $imagePath,
            'role_id' => $roleAdmin->id,
        ]);

        // $user->roles()->attach($roleAdmin);

        return response()->json(['message' => 'Admin ajouté avec succès'], 201);
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



    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
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
