<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('orders', function (Blueprint $t) {
        $t->string('stripe_payment_intent_id')->nullable()->index();
      $t->string('payment_status')->default('pending'); // pending|succeeded|canceled|requires_action など
    });
    }
    public function down(): void {
    Schema::table('orders', function (Blueprint $t) {
        $t->dropColumn(['stripe_payment_intent_id','payment_status']);
    });
    }
};

