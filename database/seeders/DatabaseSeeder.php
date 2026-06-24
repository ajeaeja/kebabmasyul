<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Clear tables to avoid duplicates
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('edit_requests')->truncate();
        DB::table('branch_reports')->truncate();
        DB::table('partner_order_items')->truncate();
        DB::table('partner_orders')->truncate();
        DB::table('incoming_stocks')->truncate();
        DB::table('raw_materials')->truncate();
        DB::table('branches')->truncate();
        DB::table('partners')->truncate();
        DB::table('users')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 2. Seed Users
        DB::table('users')->insert([
            [
                'name' => 'Masyul Owner',
                'email' => 'owner@masyulkebab.com',
                'password' => Hash::make('password'),
                'role' => 'owner',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Masyul Admin',
                'email' => 'admin@masyulkebab.com',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Masyul Warehouse',
                'email' => 'gudang@masyulkebab.com',
                'password' => Hash::make('password'),
                'role' => 'gudang',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}
