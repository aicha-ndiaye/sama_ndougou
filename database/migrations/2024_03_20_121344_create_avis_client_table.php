<?php

use App\Models\AvisClient;
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
    Schema::create('avis_clients', function (Blueprint $table) {
        $table->id();
        $table->string('contenu');
        $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
        $table->foreignId('produit_id')->constrained('produits')->onDelete('cascade');
        $table->timestamps();
    });
}



    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('avis_client');
    }
};
