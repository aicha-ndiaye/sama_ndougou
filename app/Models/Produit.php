<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produit extends Model
{
    use HasFactory;

    protected $guarded = [];

        public function commande()
        {
            return $this->belongsToMany(Commande::class, 'detailCommande');
        }
        public function user()
        {
            return $this->belongsTo(user::class);
        }
        public function categorie()
        {
            return $this->belongsTo(categorieProduit::class);
        }
        public function panier()
        {
            return $this->belongsTo(panier::class);
        }
        


}
