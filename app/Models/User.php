<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Models\Role;
use App\Models\Livreur;
use Illuminate\Console\Command;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    // protected $fillable = [
    //     'name',
    //     'email',
    //     'password',
    // ];
    protected $guarded = [];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function getJWTIdentifier()
    {
    return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
    return [];
    }

    public function produis(){
        return $this->hasMany(Produit::class);
    }
    public function commande(){
        return $this->hasMany(Commande::class);
    }
    public function post(){
        return $this->hasMany(Post::class);
    }

    public function roles()
    {
        return $this->belongsTo(Role::class);
    }

    public function livreur(){
        return $this->hasOne(Livreur::class);
    }
    public function panier()
{
    return $this->hasOne(Panier::class);
}

}
