<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invite_codes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admin_id')->constrained('users')->onDelete('cascade');
            $table->string('code')->unique();
            $table->boolean('is_used')->default(false);
            $table->foreignId('used_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('used_at')->nullable();
            $table->timestamp('expires_at')->nullable(); // 可選：設定期限
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invite_codes');
    }
};