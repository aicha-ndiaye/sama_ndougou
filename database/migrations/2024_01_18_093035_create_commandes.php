<?php

use App\Models\Panier;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('commandes', function (Blueprint $table) {
            $table->id();
            $table->string('numeroCommande');
            $table->string('dateCommande');
            $table->string('plus_de_detail_pour_la_commande')->nullable();
            $table->string('adresse_de_livraison');
            $table->foreignIdFor(User::class)->constrained()->onDelete('cascade');
            $table->enum('statut',['enAttente','enCours','terminee'])->default('enAttente');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('commandes');
    }
};
