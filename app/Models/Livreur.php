<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Livreur extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $fillables=[
        'statut'
    ];

    public function commande()
    {
        return $this->belongsTo(Commande::class);
    }
    public function livraison()
    {
        return $this->belongsTo(livraison::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
