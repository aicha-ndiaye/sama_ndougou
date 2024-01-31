<?php

use App\Models\Commande;
use App\Models\Produit;
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
        Schema::create('detail_produits', function (Blueprint $table) {
            $table->id();
            $table->integer('nombre_produit');
            $table->integer('montant');
            $table->timestamps();
            $table->foreignIdFor(Commande::class)->constrained()->onDelete('cascade');
            $table->foreignIdFor(Produit::class)->constrained()->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_produits');
    }
};
