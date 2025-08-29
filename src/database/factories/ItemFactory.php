<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Item>
 */
class ItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id'     => \App\Models\User::factory(),
            'name'        => $this->faker->words(2, true),
            'brand'       => $this->faker->word(),
            'description' => $this->faker->sentence(),
            'price'       => $this->faker->numberBetween(500, 50000),
            'condition'   => $this->faker->randomElement(['新品', '未使用に近い', '目立った傷や汚れなし', 'やや傷や汚れあり']),
            'image_path'  => null,
            'img_url'     => null,
            'is_sold'    => false,
        ];
    }
}
