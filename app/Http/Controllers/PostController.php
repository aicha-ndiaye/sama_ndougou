<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Produit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{

    public function indexPost()
    {
        $post = Post::all();
        return response()->json([
            "La listes de tous les publications "=>$post
        ], 200);
    }
    /**
     * Show the form for creating a new resource.
     */
    public function createPost(Request $request)
    {
        $user = auth()->user();

        if ($user) {
            if ($user->role_id == 1) {
                $post = new Post();
                $post->nomPost = $request->nomPost;
                $post->titrePost = $request->titrePost;
                $post->description = $request->description;
                $post->image = $request->image;
                $post->datePost = $request->datePost;
                $post->categorie_blog_id = $request->categorie_blog_id;

                $post->save();

                return response()->json(['message' => 'post ajouté avec succès', 'post' => $post], 200);
            } else {
                return response()->json(['message' => 'Vous n\'êtes pas autorisé à effectuer cette action'], 401);
            }
        } else {
            return response()->json(['message' => 'Vous devez être connecté pour effectuer cette action'], 401);
        }
    }


    public function updatePost(Request $request, string $id)
    {
        $user = auth()->user();
        if ($user) {
            if ($user->role_id == 1) { $validator=Validator::make($request->all(),[
            'nomPost' => 'required',
            'titrePost' => 'required',
            'description' => 'required',
            'image' => 'required',
            'datePost' => 'required',
            'categorie_blog_id' => 'required|exists:categorie_blogs,id',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $post = Post::find($id);
        $post->nomPost = $request->nomPost;
        $post->titrePost = $request->titrePost;
        $post->description = $request->description;
        $post->image = $request->image;
        $post->datePost = $request->datePost;
        $post->categorie_blog_id = $request->categorie_blog_id;
        $post->save();

        return response()->json(['message' => 'post modifié avec succès', 'post' => $post], 200);
    }
        }}



        public function deletePost($id)
        {
            $user = auth()->user();

            if ($user) {
                if ($user->role_id == 1) {
                    $post = Post::find($id);

                    if (!$post) {
                        return response()->json(['message' => 'post non trouvée'], 404);
                    }

                    $post->delete();

                    return response()->json(['message' => 'postsupprimé avec succès'], 200);
                } else {
                    return response()->json(['message' => 'Vous n\'êtes pas autorisé à effectuer cette action'], 401);
                }
            } else {
                return response()->json(['message' => 'Vous devez être connecté pour effectuer cette action'], 401);
            }
        }

        public function recherchePost(Request $request)
        {
            $post= Post::findOrFail($request->id);
            return response()->json([
                "message"=>"Voici le post que vous cherchez",
                "produit"=>$post
                ], 200);
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



}
