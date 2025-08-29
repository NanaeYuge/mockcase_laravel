<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        $db = DB::getDatabaseName();
        $exists = DB::table('information_schema.statistics')
            ->where('table_schema', $db)
            ->where('table_name', 'category_item')
            ->where('index_name', 'category_item_unique')
            ->exists();

        if (!$exists) {
            Schema::table('category_item', function (Blueprint $table) {
                $table->unique(['item_id', 'category_id'], 'category_item_unique');
            });
        }
    }

    public function down(): void
    {
        // 既に無ければスキップ
        try {
            DB::statement('ALTER TABLE category_item DROP INDEX category_item_unique');
        } catch (\Throwable $e) {
            // 何もしない（インデックスが無い等）
        }
    }
};
