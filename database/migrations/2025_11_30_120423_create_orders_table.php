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
        Schema::create('orders', function (Blueprint $table) {
        $table->id();
        
        // Guest Token (Penting untuk user tanpa login)
        $table->string('guest_token')->index()->nullable();
        
        // Data Pembeli
        $table->string('customer_name');
        $table->string('customer_phone')->nullable();
        
        // Relasi (Nullable karena bisa Take Away atau belum dikonfirmasi kasir)
        $table->foreignId('table_id')->nullable()->constrained('tables')->nullOnDelete();
        $table->foreignId('cashier_id')->nullable()->constrained('users')->nullOnDelete();
        
        // Data Transaksi
        $table->decimal('total_amount', 12, 2)->default(0);
        $table->enum('order_type', ['dine_in', 'take_away', 'booking']);
        $table->enum('status', ['pending', 'confirmed', 'cooking', 'served', 'paid', 'cancelled'])->default('pending');
        $table->string('payment_method')->nullable(); // cash, qris
        
        // Khusus Booking
        $table->dateTime('booking_time')->nullable();
        
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
