<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Convert any existing 'tempo' payment_status values to 'belum_lunas' first
        DB::table('partner_orders')->where('payment_status', 'tempo')->update(['payment_status' => 'belum_lunas']);

        // 1. Drop payment_proof column
        if (Schema::hasColumn('partner_orders', 'payment_proof')) {
            Schema::table('partner_orders', function (Blueprint $table) {
                $table->dropColumn('payment_proof');
            });
        }

        // 2. Add payment_method column
        Schema::table('partner_orders', function (Blueprint $table) {
            $table->enum('payment_method', ['transfer', 'qris', 'cash'])->nullable()->after('payment_status');
        });

        // 3. Update payment_status enum to drop 'tempo'
        $driver = DB::getDriverName();
        if ($driver === 'pgsql') {
            DB::statement("ALTER TABLE partner_orders DROP CONSTRAINT IF EXISTS partner_orders_payment_status_check");
            DB::statement("ALTER TABLE partner_orders ADD CONSTRAINT partner_orders_payment_status_check CHECK (payment_status IN ('lunas', 'belum_lunas'))");
            DB::statement("ALTER TABLE partner_orders ALTER COLUMN payment_status SET DEFAULT 'belum_lunas'");
        } elseif ($driver === 'mysql') {
            DB::statement("ALTER TABLE partner_orders MODIFY COLUMN payment_status ENUM('lunas', 'belum_lunas') DEFAULT 'belum_lunas'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('partner_orders', function (Blueprint $table) {
            $table->dropColumn('payment_method');
            $table->string('payment_proof')->nullable()->after('payment_status');
        });

        $driver = DB::getDriverName();
        if ($driver === 'pgsql') {
            DB::statement("ALTER TABLE partner_orders DROP CONSTRAINT IF EXISTS partner_orders_payment_status_check");
            DB::statement("ALTER TABLE partner_orders ADD CONSTRAINT partner_orders_payment_status_check CHECK (payment_status IN ('lunas', 'belum_lunas', 'tempo'))");
            DB::statement("ALTER TABLE partner_orders ALTER COLUMN payment_status SET DEFAULT 'belum_lunas'");
        } elseif ($driver === 'mysql') {
            DB::statement("ALTER TABLE partner_orders MODIFY COLUMN payment_status ENUM('lunas', 'belum_lunas', 'tempo') DEFAULT 'belum_lunas'");
        }
    }
};
