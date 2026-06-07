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
        if (Schema::hasColumn('branches', 'manager_name')) {
            Schema::table('branches', function (Blueprint $table) {
                $table->renameColumn('manager_name', 'pengelola_cabang');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('branches', 'pengelola_cabang')) {
            Schema::table('branches', function (Blueprint $table) {
                $table->renameColumn('pengelola_cabang', 'manager_name');
            });
        }
    }
};
