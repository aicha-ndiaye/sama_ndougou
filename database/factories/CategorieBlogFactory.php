<?php
// CategorieBlogFactory.php

namespace Database\Factories;

use App\Models\CategorieBlog;
use Illuminate\Database\Eloquent\Factories\Factory;

class CategorieBlogFactory extends Factory
{
    protected $model = CategorieBlog::class;

    public function definition()
    {
        return [
            'nomCategorie' => $this->faker->word(),
            // Autres attributs de votre modèle ici
        ];
    }
}
