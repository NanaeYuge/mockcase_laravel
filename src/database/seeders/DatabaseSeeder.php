<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run(): void
    {
        $this->call([
            UsersTableSeeder::class,
            CategoriesTableSeeder::class,
            ItemsTableSeeder::class,
            CategoryItemTableSeeder::class,
            OrdersTableSeeder::class,
        ]);

        User::updateOrCreate(
        ['email' => 'demo@example.com'],
        [
            'name' => 'Demo User',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]
    );
    }
}
