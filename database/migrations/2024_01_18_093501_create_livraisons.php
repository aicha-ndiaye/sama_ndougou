<?php

use App\Models\Livreur;
use App\Models\Commande;
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
        Schema::create('livraisons', function (Blueprint $table) {
            $table->id();
            $table->string('avisClient');
            $table->enum('statut',['enAttente','enCours','terminee'])->default('enAttente');
            $table->string('dateLivraison');
            $table->foreignIdFor(Commande::class)->constrained()->onDelete('cascade');
            $table->foreignIdFor(Livreur::class)->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('livraisons');
    }
};
