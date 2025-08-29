<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'test@example.com'],
            [
                'name'        => 'テストユーザー',
                'password'    => Hash::make('password'),
                'postal_code' => '123-4567',
                'address'     => '東京都渋谷区道玄坂1-2-3',
                'building'    => 'テストビル101',
            ]
        );
    }
}

