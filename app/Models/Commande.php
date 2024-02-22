<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
