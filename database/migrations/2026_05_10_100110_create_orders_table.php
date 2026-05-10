<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('order_number')->unique(); // 訂單編號
            $table->enum('status', [
                'pending',    // 待付款
                'paid',       // 已付款
                'failed',     // 付款失敗
                'cancelled',  // 已取消
            ])->default('pending');
            $table->decimal('total', 10, 2);
            $table->string('recipient_name');
            $table->string('recipient_phone');
            $table->string('recipient_address');
            $table->string('payment_method')->default('credit_card');
            $table->string('ecpay_trade_no')->nullable(); // 綠界交易編號
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};