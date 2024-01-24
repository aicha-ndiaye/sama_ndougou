<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AvisClient extends Model
{
    use HasFactory;

    protected $guarded = [];

    // Définir la relation avec la table 'produits'
    public function produit()
    {
        return $this->belongsTo(Produit::class, 'produit_id');
    }

    // Définir la relation avec la table 'users' (utilisateur qui a donné l'avis)
    public function utilisateur()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
