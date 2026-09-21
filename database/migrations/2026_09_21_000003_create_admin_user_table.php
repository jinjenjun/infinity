<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admin_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admin_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('invite_code_id')->nullable()->constrained('invite_codes')->onDelete('set null');
            $table->timestamps();
            $table->unique(['admin_id', 'user_id']);
        });

        DB::table('users')->whereNotNull('managed_by')->orderBy('id')->each(function ($user) {
            DB::table('admin_user')->insert([
                'admin_id' => $user->managed_by,
                'user_id' => $user->id,
                'invite_code_id' => $user->invite_code_id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['managed_by']);
            $table->dropColumn('managed_by');
            $table->dropForeign(['invite_code_id']);
            $table->dropColumn('invite_code_id');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('managed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('invite_code_id')->nullable()->constrained('invite_codes')->onDelete('set null');
        });

        DB::table('admin_user')->orderBy('id')->each(function ($row) {
            DB::table('users')->where('id', $row->user_id)->whereNull('managed_by')->update([
                'managed_by' => $row->admin_id,
                'invite_code_id' => $row->invite_code_id,
            ]);
        });

        Schema::dropIfExists('admin_user');
    }
};
