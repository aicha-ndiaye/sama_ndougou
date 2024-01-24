<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Panier extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function commandes()
{
    return $this->hasMany(Commande::class, 'panier_id');
}



    public function produit()
    {
        return $this->belongsTo(Produit::class);
    }
}



