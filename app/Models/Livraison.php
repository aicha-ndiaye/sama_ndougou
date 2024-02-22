<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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


    public function user()
    {
        return $this->belongsTo(User::class);
    }


}
