<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Commande extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function livreur()
    {
        return $this->belongsTo(livreur::class);
    }


    public function produits()
    {
        return $this->belongsToMany(Produit::class, 'detail_produits')
            ->withPivot('quantite');
    }
}
