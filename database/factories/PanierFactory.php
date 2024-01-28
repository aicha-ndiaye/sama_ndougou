<?php

namespace Database\Factories;

use App\Models\Panier;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Panier>
 */
class PanierFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'quantite' => $this->faker->numberBetween(10, 50),
            'produit_id' => $this->faker->numberBetween(1, 10),  
        ];
    }
}
