<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategoriesTableSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'ファッション','家電','インテリア','レディース','メンズ','コスメ',
            '本','ゲーム','スポーツ','キッチン','ハンドメイド','アクセサリー',
            'おもちゃ','ベビー・キッズ','アウトドア','ホビー','食品','飲料',
            '楽器','カメラ','スマホ','パソコン','ペット用品','車・バイク',
            'DIY・工具','ヘルスケア','ビューティー','オフィス用品','チケット','その他',
        ];

        foreach ($categories as $name) {
            Category::firstOrCreate(['name' => $name]);
        }
    }
}
