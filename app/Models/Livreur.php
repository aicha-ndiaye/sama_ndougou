<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;

class Livreur extends Model
{
    use HasFactory,Notifiable;

    protected $guarded = [];
    protected $fillable = ['statut']; // Correction ici

    public function commande()
    {
        return $this->belongsTo(Commande::class);
    }

    public function livraison()
    {
        return $this->belongsTo(Livraison::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
