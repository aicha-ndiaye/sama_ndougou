<?php

use App\Http\Controllers\avis_clienttController;
use App\Http\Controllers\categorieBlogController;
use App\Http\Controllers\CategorieController;
use App\Http\Controllers\CommandeController;
use App\Http\Controllers\detailpoduitController;
use App\Http\Controllers\LivreurController;
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

//se connecter
Route::post('login', [UserController::class, 'login']);
//se deconnecter
Route::post('deconnect', [UserController::class, 'deconnect']);
//verifier si un email existe
Route::post('verifMail', [UserController::class, 'verifMail']);
//lister tous les produits
Route::get('/indexProduit', [ProduitController::class, 'indexProduit']);
// lister panier
Route::get('/indexPanier', [PanierController::class, 'indexPanier']);
//ajouter role
Route::post('ajouterRole', [UserController::class, 'ajouterRole']);
// lister les post
Route::get('/indexPost', [PostController::class, 'indexPost']);
//cree une commande
Route::post('/createCommande', [CommandeController::class, 'createCommande']);


Route::get('/commandeEnCours/{id}', [CommandeController::class, 'commandeEnCours']);
Route::get('/commandeTerminee/{id}', [CommandeController::class, 'commandeTerminee']);
Route::get('/listeCommandeEnAttente', [CommandeController::class, 'listeCommandeEnAttente']);
Route::get('/listeCommandeEnCours', [CommandeController::class, 'listeCommandeEnCours']);
Route::get('/ListecommandeTerminee', [CommandeController::class, 'ListecommandeTerminee']);



Route::post('/ajouterAuPanier', [PanierController::class, 'ajouterAuPanier']);

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
Route::delete('/deleteCommande/{id}', [CommandeController::class, 'deleteCommande']);
Route::post('/changerStatut', [LivreurController::class, 'changerStatut']);

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
    Route::post('/AffecterLivreur/{commande}', [LivreurController::class, 'AffecterLivreur']);
    Route::get('/listerLivreursDisponible', [LivreurController::class, 'listerLivreursDisponible']);
    Route::get('/listerLivreursOccupe', [LivreurController::class, 'listerLivreursOccupe']);



});

