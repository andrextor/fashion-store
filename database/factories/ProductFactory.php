<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Product::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name,
            'image' => 'images/' . $this->faker->name . '.jpeg',
            'description' => $this->faker->text,
            'price' => $this->faker->randomNumber(4) ,
            'genre' => 'F',
        ];
    }
}
