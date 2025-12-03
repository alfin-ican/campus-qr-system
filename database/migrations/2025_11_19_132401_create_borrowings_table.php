<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('borrowings', function (Blueprint $table) {
            $table->id();
            $table->string('borrowing_code')->unique();

            // Relasi ke tabel students
            $table->foreignId('student_id')
                  ->constrained('students')
                  ->onDelete('cascade');

            // Relasi ke items
            $table->foreignId('item_id')
                  ->constrained('items')
                  ->onDelete('cascade');

            // Admin yang approve
            $table->foreignId('approved_by')
                  ->nullable()
                  ->constrained('admins')
                  ->nullOnDelete();

            // Tanggal
            $table->date('borrow_date');
            $table->date('planned_return_date');
            $table->date('return_date')->nullable();

            // Status
            $table->enum('status', [
                'pending', 'approved', 'rejected', 'returned', 'late'
            ])->default('pending');

            // Detail peminjaman
            $table->text('purpose')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamp('approved_at')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('borrowings');
    }
};