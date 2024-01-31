<?php

use App\Http\Controllers\avis_clienttController;
use App\Http\Controllers\categorieBlogController;
use App\Http\Controllers\CategorieController;
use App\Http\Controllers\CommandeController;
use App\Http\Controllers\detailpoduitController;
use App\Http\Controllers\PanierController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProduitController;
use App\Http\Controllers\userController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
//ajouter un utilisateur 'client'
Route::post('/inscriptionClient', [userController::class, 'inscriptionClient']);
//ajouter un utilisateur 'livreur'

// //ajouter un utilisateur 'admin'
// Route::post('/inscriptionAdmin', [UserController::class, 'inscriptionAdmin']);
//se connecter
Route::post('login', [UserController::class, 'login']);
Route::post('deconnect', [UserController::class, 'deconnect']);
//verifier si un email existe
Route::post('verifMail', [UserController::class, 'verifMail']);

Route::get('/index', [ProduitController::class, 'index']);
Route::get('/indexPanier', [PanierController::class, 'indexPanier']);
Route::post('ajouterRole', [UserController::class, 'ajouterRole']);
Route::get('/indexPost', [PostController::class, 'indexPost']);
Route::post('/createCommande', [CommandeController::class, 'createCommande']);
Route::post('/ProduitsCommande/{id}', [detailpoduitController::class, 'ProduitsCommande']);

Route::post('/ajoutProduitPanier', [PanierController::class, 'ajouterAuPanier']);

Route::get('/afficherProduitsPanier', [PanierController::class, 'afficherProduitsPanier']);

Route::post('/createAvis', [avis_clienttController::class, 'createAvis']);
Route::get('/indexAvis', [avis_clienttController::class, 'indexAvis']);
Route::post('/updateAvis/{id}', [avis_clienttController::class, 'updateAvis']);
Route::get('/rechercheProduit/{id}', [ProduitController::class, 'rechercheProduit']);
Route::get('/recherchePost/{id}', [PostController::class, 'recherchePost']);
Route::delete('/deletePanier/{id}', [PanierController::class, 'deletePanier']);
Route::post('/modifierMotDePasse', [userController::class, 'modifierMotDePasse']);
Route::post('/resetPassword', [userController::class, 'resetPassword']);
Route::get('/indexCommande', [CommandeController::class, 'indexCommande']);
Route::get('/detailCommande/{id}', [detailpoduitController::class, 'detailCommande']);

Route::middleware(['auth:api', 'admin'])->group(function () {

    Route::post('/inscriptionlivreur', [UserController::class, 'inscriptionlivreur']);
    Route::post('/createCategorieProduits', [CategorieController::class, 'createCategorieProduits']);
    Route::post('/createProduit', [ProduitController::class, 'createProduit']);
    Route::post('/updateProduit/{id}', [ProduitController::class, 'updateProduit']);
    Route::delete('/deleteProduit/{id}', [ProduitController::class, 'deleteProduit']);
    Route::post('/createCategorieBlog', [categorieBlogController::class, 'createCategorieBlog']);
    Route::post('/createPost', [PostController::class, 'createPost']);
    Route::post('/updatePost/{id}', [PostController::class, 'updatePost']);
    Route::delete('/deletePost/{id}', [PostController::class, 'deletePost']);
    Route::post('/modifieProfileAdmin', [userController::class, 'modifieProfileAdmin']);
    Route::delete('/deleteAvis/{id}', [avis_clienttController::class, 'deleteAvis']);



});

