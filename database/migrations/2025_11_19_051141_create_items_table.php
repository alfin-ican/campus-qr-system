<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->string('item_code')->unique(); // Kode barang otomatis
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('category');
            $table->integer('quantity')->default(0);
            $table->enum('status', ['tersedia', 'dipinjam', 'rusak', 'maintenance'])->default('tersedia');
            $table->string('location')->nullable();
            $table->string('photo')->nullable();
            $table->string('qr_code')->nullable(); // Path ke file QR Code
            $table->foreignId('created_by')->constrained('admins')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};