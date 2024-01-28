<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Produit;


class ProduitFactory extends Factory
{
    protected $model = Produit::class;

    public function definition()
    {
        return [
            'nomProduit' => $this->faker->word(),
            'prix' => $this->faker->randomFloat(2, 10, 100),
            'quantiteTotale' => $this->faker->numberBetween(10, 50),
            'description' => $this->faker->sentence(),
            'image' => 'default.jpg',
        ];
    }
}


