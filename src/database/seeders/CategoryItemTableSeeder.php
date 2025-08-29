<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Item;
use App\Models\Category;

class CategoryItemTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categoryIds = Category::pluck('id')->toArray();

        Item::all()->each(function ($item) use ($categoryIds) {
            $randomIds = collect($categoryIds)->random(rand(1, 3))->toArray();
            $item->categories()->attach($randomIds);
        });
    }
}
