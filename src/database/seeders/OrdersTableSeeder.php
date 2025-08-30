<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;

class OrdersTableSeeder extends Seeder
{
    public function run(): void
    {
        Order::create([
            'user_id' => 1,
            'item_id' => 1,
            'payment_method' => 'クレジットカード',
            'status' => '購入完了',
            'shipping_address' => '東京都テスト区1-2-3',
            'total_amount' => 1000,
        ]);
    }
}
