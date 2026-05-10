<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_categories', function (Blueprint $table) {
            $table->foreignId('admin_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->foreignId('parent_id')->nullable()->constrained('product_categories')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('product_categories', function (Blueprint $table) {
            $table->dropForeign(['admin_id']);
            $table->dropColumn('admin_id');
            $table->dropForeign(['parent_id']);
            $table->dropColumn('parent_id');
        });
    }
};