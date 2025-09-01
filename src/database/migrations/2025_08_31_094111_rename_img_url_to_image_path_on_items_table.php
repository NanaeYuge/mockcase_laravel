<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {

        if (Schema::hasColumn('items', 'img_url') && !Schema::hasColumn('items', 'image_path')) {
            Schema::table('items', function (Blueprint $table) {
                $table->renameColumn('img_url', 'image_path');
            });
            return;
        }

        if (Schema::hasColumn('items', 'img_url') && Schema::hasColumn('items', 'image_path')) {
            DB::table('items')
                ->whereNull('image_path')
                ->whereNotNull('img_url')
                ->update(['image_path' => DB::raw('img_url')]);

            Schema::table('items', function (Blueprint $table) {
                $table->dropColumn('img_url');
            });
            return;
        }
    }

    public function down(): void
    {
        if (!Schema::hasColumn('items', 'img_url') && Schema::hasColumn('items', 'image_path')) {
            Schema::table('items', function (Blueprint $table) {
                $table->renameColumn('image_path', 'img_url');
            });
        }
    }
};
