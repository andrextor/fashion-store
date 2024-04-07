<?php

namespace Database\Seeders;

use Faker\Factory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $faker = Factory::create();

        for ($i = 1; $i <= 10; $i++) {

            $genre = ($i <= 5) ? 'M' : 'F';

            DB::table('products')->insert([
                'name' => $faker->name,
                'image' => 'sr' . $i . '.jpeg',
                'description' => $faker->text,
                'price' => $faker->randomNumber(2, true),
                'genre' => $genre,
            ]);
        }
    }
}
