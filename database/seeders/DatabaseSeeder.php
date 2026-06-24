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
        // 1. Clear tables to avoid duplicates (Driver-aware for MySQL, PostgreSQL, SQLite)
        $driver = DB::connection()->getDriverName();
        if ($driver === 'pgsql') {
            // PostgreSQL does not require disabling FK checks if we truncate all tables with CASCADE
            DB::statement('TRUNCATE edit_requests, branch_reports, partner_order_items, partner_orders, incoming_stocks, raw_materials, branches, partners, users CASCADE;');
        } else {
            if ($driver === 'mysql') {
                DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            } elseif ($driver === 'sqlite') {
                DB::statement('PRAGMA foreign_keys = OFF;');
            }

            DB::table('edit_requests')->truncate();
            DB::table('branch_reports')->truncate();
            DB::table('partner_order_items')->truncate();
            DB::table('partner_orders')->truncate();
            DB::table('incoming_stocks')->truncate();
            DB::table('raw_materials')->truncate();
            DB::table('branches')->truncate();
            DB::table('partners')->truncate();
            DB::table('users')->truncate();

            if ($driver === 'mysql') {
                DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            } elseif ($driver === 'sqlite') {
                DB::statement('PRAGMA foreign_keys = ON;');
            }
        }

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
