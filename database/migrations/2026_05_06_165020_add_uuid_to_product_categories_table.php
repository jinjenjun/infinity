<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_categories', function (Blueprint $table) {
            $table->uuid('uuid')->unique()->after('id');
        });

        DB::table('product_categories')->orderBy('id')->whereNull('uuid')->each(function ($item) {
            DB::table('product_categories')->where('id', $item->id)->update(['uuid' => Str::uuid()]);
        });
    }

    public function down(): void
    {
        Schema::table('product_categories', function (Blueprint $table) {
            $table->dropColumn('uuid');
        });
    }
};
