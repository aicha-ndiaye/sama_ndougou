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
use App\Models\CategorieBlog;
use App\Models\categorieProduit;
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
Route::post('deconnect', [UserController::class, 'deconnect'])->middleware('auth:api');
//verifier si un email existe
Route::post('verifMail', [UserController::class, 'verifMail']);
//lister tous les produits
Route::get('/indexProduit', [ProduitController::class, 'indexProduit']);
//ajouter role
Route::post('ajouterRole', [UserController::class, 'ajouterRole']);
// lister les post
Route::get('/indexPost', [PostController::class, 'indexPost']);

Route::get('/commandeEnCours/{id}', [CommandeController::class, 'commandeEnCours']);
Route::get('/commandeTerminee/{id}', [CommandeController::class, 'commandeTerminee']);
Route::get('/listeCommandeEnAttente', [CommandeController::class, 'listeCommandeEnAttente']);
Route::get('/listeCommandeEnCours', [CommandeController::class, 'listeCommandeEnCours']);
Route::get('/ListecommandeTerminee', [CommandeController::class, 'ListecommandeTerminee']);

Route::get('/indexAvis', [avis_clienttController::class, 'indexAvis']);
Route::get('/rechercheProduit/{id}', [ProduitController::class, 'rechercheProduit']);
Route::get('/recherchePost/{id}', [PostController::class, 'recherchePost']);
Route::post('/modifierMotDePasse', [userController::class, 'modifierMotDePasse'])->middleware('auth:api');
Route::post('/resetPassword', [userController::class, 'resetPassword'])->middleware('auth:api');
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
    Route::delete('/supprimerCategorieProduit/{id}', [CategorieController::class, 'supprimerCategorieProduit']);
    Route::delete('/supprimerCategorieBlog{id}', [categorieBlogController::class, 'supprimerCategorieBlog']);
    Route::get('/indexCategorieBlog', [categorieBlogController::class, 'indexCategorieBlog']);

});


Route::middleware(['auth:api', 'client'])->group(function () {
// lister panier
Route::get('/indexPanier', [PanierController::class, 'indexPanier']);
//cree une commande
Route::post('/createCommande', [CommandeController::class, 'createCommande']);
Route::post('/ajouterAuPanier', [PanierController::class, 'ajouterAuPanier']);
Route::get('/afficherProduitsPanier', [PanierController::class, 'afficherProduitsPanier']);
Route::post('/createAvis', [avis_clienttController::class, 'createAvis']);
Route::post('/updateAvis/{id}', [avis_clienttController::class, 'updateAvis']);
Route::delete('/deletePanier/{id}', [PanierController::class, 'deletePanier']);
Route::get('/indexCommande', [CommandeController::class, 'indexCommande']);
Route::get('/detailCommande/{id}', [detailpoduitController::class, 'detailCommande']);
Route::delete('/deleteCommande/{id}', [CommandeController::class, 'deleteCommande']);

});
Route::middleware(['auth:api', 'livreur'])->group(function () {
Route::post('/changerStatut', [LivreurController::class, 'changerStatut']);
Route::post('/CommandeTerminee/{commande}', [LivreurController::class, 'CommandeTerminee']);
});



