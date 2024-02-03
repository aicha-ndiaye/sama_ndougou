<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Commande extends Model
{
    use HasFactory;
    protected $guarded = [];


    // protected static function boot()
    // {
    //     parent::boot();

    //     static::creating(function ($commande) {
    //         $latestCommande = static::latest()->first();

    //         if ($latestCommande) {
    //             $commande->numeroCommande = $latestCommande->numeroCommande + 1;
    //         } else {
    //             $commande->numeroCommande = 1;
    //         }
    //     });
    // }

    public function livreur()
    {
        return $this->belongsTo(livreur::class);
    }


    public function produits()
    {
        return $this->belongsToMany(Produit::class, 'detail_produits')
            ->withPivot('quantite');
    }
    public function panier()
    {
        return $this->belongsTo(Panier::class);
    }
}
