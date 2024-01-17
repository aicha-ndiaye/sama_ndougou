<?php

use App\Models\Commande;
use App\Models\Livreur;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

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
            $table->enum('statut',['enAttente','enCours','terminee'])->default('enAtttente');
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
