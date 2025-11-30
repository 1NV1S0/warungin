<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('menus', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->string('slug')->unique(); // URL friendly name
        $table->text('description')->nullable();
        $table->decimal('price', 10, 2); // Harga (misal: 15000.00)
        $table->integer('stock')->default(0); // Stok porsi
        $table->enum('category', ['makanan', 'minuman', 'snack']);
        $table->string('image_path')->nullable();
        $table->boolean('is_available')->default(true); // Switch on/off manual
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menus');
    }
};
