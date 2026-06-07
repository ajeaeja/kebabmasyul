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
        Schema::create('partner_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('partner_id')->constrained('partners')->onDelete('cascade');
            $table->date('order_date');
            $table->date('shipping_date')->nullable();
            $table->text('expedition_info')->nullable();
            $table->decimal('shipping_cost', 15, 2)->default(0.00);
            $table->enum('status', ['menunggu_dipacking', 'dipacking', 'dikirim', 'selesai'])->default('menunggu_dipacking');
            $table->enum('payment_status', ['lunas', 'belum_lunas', 'tempo'])->default('belum_lunas');
            $table->decimal('total_price', 15, 2)->default(0.00);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('partner_orders');
    }
};
