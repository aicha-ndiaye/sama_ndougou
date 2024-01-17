<?php

use App\Models\Categorie;
use App\Models\Commande;
use App\Models\User;
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
        Schema::create('produits', function (Blueprint $table) {
            $table->id();
            $table->string('nomProduit');
            $table->float('prix');
            $table->integer('quantite');
            $table->text('description');
            $table->string('image');
            $table->foreignIdFor(Categorie::class)->nullable()->constrained()->onDelete('cascade');
            $table->foreignIdFor(Commande::class)->nullable()->constrained()->onDelete('cascade');
            $table->foreignIdFor(User::class)->nullable()->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produits');
    }
};
