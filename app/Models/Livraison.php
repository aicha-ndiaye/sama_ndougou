<?php

namespace App\Models;

use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Livraison extends Model
{
    use HasFactory;
    protected $guarded = [];
    public function livreur()
    {
        return $this->belongsTo(Livreur::class);
    }

    public function commande()
    {
        return $this->belongsTo(Commande::class);
    }



}
