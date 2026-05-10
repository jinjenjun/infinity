<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invite_codes', function (Blueprint $table) {
            $table->dropForeign(['used_by']);
            $table->dropColumn(['is_used', 'used_by', 'used_at']);
        });
    }

    public function down(): void
    {
        Schema::table('invite_codes', function (Blueprint $table) {
            $table->boolean('is_used')->default(false);
            $table->foreignId('used_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('used_at')->nullable();
        });
    }
};