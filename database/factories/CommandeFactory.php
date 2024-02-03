<?php

namespace Database\Factories;

use App\Models\Commande;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Commande>
 */
class CommandeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'numeroCommande' => $this->generateNumeroCommande(),
            'dateCommande' => $this->faker->date(),
            'quantite' => $this->faker->numberBetween(10, 50),
            'adresse_de_livraison' => $this->faker->sentence(),
            'statut' => 'enAttente', 
            'plus_de_detail_pour_la_commande' => $this->faker->sentence()
        ];
    }

    private function generateNumeroCommande()
    {
        $prefixe = $this->faker->word();
        $suffixe = $this->faker->numberBetween(1000, 9999);
        return $prefixe . '-' . $suffixe;
    }



}
