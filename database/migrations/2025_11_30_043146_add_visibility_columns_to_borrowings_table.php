<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('borrowings', function (Blueprint $table) {
            $table->boolean('hidden_by_admin')->default(false)->after('approved_at');
            $table->boolean('hidden_by_student')->default(false)->after('hidden_by_admin');
        });
    }

    public function down(): void
    {
        Schema::table('borrowings', function (Blueprint $table) {
            $table->dropColumn(['hidden_by_admin', 'hidden_by_student']);
        });
    }
};