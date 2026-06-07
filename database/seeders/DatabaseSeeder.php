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

        // 3. Seed Partners
        $partner1Id = DB::table('partners')->insertGetId([
            'name' => 'Mitra Kebab Margonda',
            'owner_name' => 'Budi Sudarsono',
            'phone' => '081234567890',
            'address' => 'Jl. Margonda Raya No. 12, Depok',
            'jenis_paket' => 'Paket Premium',
            'join_date' => '2025-01-15',
            'mou_end_date' => '2027-01-15',
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $partner2Id = DB::table('partners')->insertGetId([
            'name' => 'Mitra Kebab Depok II',
            'owner_name' => 'Rian Apriyadi',
            'phone' => '087712345678',
            'address' => 'Jl. Sentosa Raya No. 45, Depok Dua',
            'jenis_paket' => 'Paket Basic',
            'join_date' => '2025-03-20',
            'mou_end_date' => '2026-03-20',
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $partner3Id = DB::table('partners')->insertGetId([
            'name' => 'Mitra Kebab Salemba',
            'owner_name' => 'Susi Susanti',
            'phone' => '081398765432',
            'address' => 'Jl. Salemba Raya No. 8, Jakarta Pusat',
            'jenis_paket' => 'Paket Super Premium',
            'join_date' => '2024-06-01',
            'mou_end_date' => '2026-06-01',
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $partner4Id = DB::table('partners')->insertGetId([
            'name' => 'Mitra Kebab Bekasi',
            'owner_name' => 'Joko Widodo',
            'phone' => '085612345678',
            'address' => 'Jl. Ahmad Yani No. 3, Bekasi',
            'jenis_paket' => 'Paket Basic',
            'join_date' => '2024-02-10',
            'mou_end_date' => '2025-02-10',
            'status' => 'inactive',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 4. Seed Branches
        $branch1Id = DB::table('branches')->insertGetId([
            'name' => 'Cabang Depok Sleman (Pusat)',
            'address' => 'Jl. Margonda Raya No. 12, Depok',
            'pengelola_cabang' => 'Budi Sudarsono',
            'opened_date' => '2025-01-15',
            'notes' => 'Pusat wilayah Depok Utara.',
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $branch2Id = DB::table('branches')->insertGetId([
            'name' => 'Cabang Bekasi Timur (Pusat)',
            'address' => 'Jl. Sentosa Raya No. 45, Bekasi',
            'pengelola_cabang' => 'Rian Apriyadi',
            'opened_date' => '2025-03-20',
            'notes' => 'Pusat wilayah Bekasi.',
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $branch3Id = DB::table('branches')->insertGetId([
            'name' => 'Cabang Bogor Raya (Pusat)',
            'address' => 'Jl. Salemba Raya No. 8, Bogor',
            'pengelola_cabang' => 'Susi Susanti',
            'opened_date' => '2024-06-01',
            'notes' => 'Pusat wilayah Bogor.',
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $branch4Id = DB::table('branches')->insertGetId([
            'name' => 'Cabang Utama Margonda (Pusat)',
            'address' => 'Jl. Margonda Raya No. 1, Depok (Pusat)',
            'pengelola_cabang' => 'Agus Kebab',
            'opened_date' => '2023-01-01',
            'notes' => 'Pusat operasional cabang internal.',
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $branch5Id = DB::table('branches')->insertGetId([
            'name' => 'Cabang Salemba Tengah (Pusat)',
            'address' => 'Jl. Salemba Tengah No. 22, Jakarta Pusat',
            'pengelola_cabang' => 'Toni Hermawan',
            'opened_date' => '2023-11-10',
            'notes' => 'Outlet internal kedua di Jakarta.',
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 5. Seed Raw Materials
        $raw1Id = DB::table('raw_materials')->insertGetId([
            'sku' => 'BB001',
            'name' => 'Tortilla Kebab Besar',
            'stock' => 150.00,
            'unit' => 'pack',
            'safety_stock' => 30.00,
            'price' => 25000.00,
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $raw2Id = DB::table('raw_materials')->insertGetId([
            'sku' => 'BB002',
            'name' => 'Daging Sapi Kebab',
            'stock' => 12.00,
            'unit' => 'tiang',
            'safety_stock' => 5.00,
            'price' => 300000.00,
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $raw3Id = DB::table('raw_materials')->insertGetId([
            'sku' => 'BB003',
            'name' => 'Saus Sambal',
            'stock' => 45.00,
            'unit' => 'dirigen',
            'safety_stock' => 10.00,
            'price' => 45000.00,
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Trigger Alert Stock (safety stock is 10, current is 8)
        $raw4Id = DB::table('raw_materials')->insertGetId([
            'sku' => 'BB004',
            'name' => 'Saus Tomat',
            'stock' => 8.00,
            'unit' => 'dirigen',
            'safety_stock' => 10.00,
            'price' => 40000.00,
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $raw5Id = DB::table('raw_materials')->insertGetId([
            'sku' => 'BB005',
            'name' => 'Mayones',
            'stock' => 22.00,
            'unit' => 'pack',
            'safety_stock' => 8.00,
            'price' => 32000.00,
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $raw6Id = DB::table('raw_materials')->insertGetId([
            'sku' => 'BB006',
            'name' => 'Kertas Pembungkus Kebab',
            'stock' => 250.00,
            'unit' => 'pcs',
            'safety_stock' => 50.00,
            'price' => 500.00,
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Trigger Alert Stock (safety stock is 15, current is 4)
        $raw7Id = DB::table('raw_materials')->insertGetId([
            'sku' => 'BB007',
            'name' => 'Keju Slice',
            'stock' => 4.00,
            'unit' => 'pack',
            'safety_stock' => 15.00,
            'price' => 65000.00,
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 6. Seed Incoming Stocks
        DB::table('incoming_stocks')->insert([
            [
                'raw_material_id' => $raw1Id,
                'quantity' => 100.00,
                'incoming_date' => '2026-05-10',
                'notes' => 'Pengadaan rutin awal bulan supplier Tortilla Jaya',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'raw_material_id' => $raw2Id,
                'quantity' => 10.00,
                'incoming_date' => '2026-05-12',
                'notes' => 'Pengadaan daging sapi dari Rumah Potong Rapi',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'raw_material_id' => $raw3Id,
                'quantity' => 20.00,
                'incoming_date' => '2026-05-20',
                'notes' => 'Restock saus sambal',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // 7. Seed Partner Orders
        // Order 1: Margonda - Completed (Selesai)
        $order1Id = DB::table('partner_orders')->insertGetId([
            'partner_id' => $partner1Id,
            'order_date' => '2026-05-15',
            'shipping_date' => '2026-05-16',
            'expedition_info' => 'Bus Prima Jasa No Pol B 1234 XY',
            'shipping_cost' => 50000.00,
            'status' => 'selesai',
            'payment_status' => 'lunas',
            'payment_method' => 'transfer',
            'total_price' => 850000.00,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('partner_order_items')->insert([
            [
                'partner_order_id' => $order1Id,
                'raw_material_id' => $raw1Id,
                'quantity' => 10.00,
                'price' => 25000.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'partner_order_id' => $order1Id,
                'raw_material_id' => $raw2Id,
                'quantity' => 2.00,
                'price' => 300000.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Order 2: Depok II - Completed (Selesai)
        $order2Id = DB::table('partner_orders')->insertGetId([
            'partner_id' => $partner2Id,
            'order_date' => '2026-05-20',
            'shipping_date' => '2026-05-21',
            'expedition_info' => 'Travel Baraya Resi #BYA9988',
            'shipping_cost' => 75000.00,
            'status' => 'selesai',
            'payment_status' => 'lunas',
            'payment_method' => 'qris',
            'total_price' => 1200000.00,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('partner_order_items')->insert([
            [
                'partner_order_id' => $order2Id,
                'raw_material_id' => $raw2Id,
                'quantity' => 4.00,
                'price' => 300000.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Order 3: Salemba - Processing (Dipacking)
        $order3Id = DB::table('partner_orders')->insertGetId([
            'partner_id' => $partner3Id,
            'order_date' => '2026-06-01',
            'shipping_date' => '2026-06-03',
            'expedition_info' => 'Kurir internal Gudang (Resi #JKT02)',
            'shipping_cost' => 30000.00,
            'status' => 'dipacking',
            'payment_status' => 'belum_lunas',
            'total_price' => 680000.00,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('partner_order_items')->insert([
            [
                'partner_order_id' => $order3Id,
                'raw_material_id' => $raw1Id,
                'quantity' => 8.00,
                'price' => 25000.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'partner_order_id' => $order3Id,
                'raw_material_id' => $raw3Id,
                'quantity' => 4.00,
                'price' => 45000.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'partner_order_id' => $order3Id,
                'raw_material_id' => $raw2Id,
                'quantity' => 1.00,
                'price' => 300000.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Order 4: Margonda - Pending (Menunggu Dipacking)
        $order4Id = DB::table('partner_orders')->insertGetId([
            'partner_id' => $partner1Id,
            'order_date' => '2026-06-03',
            'shipping_date' => '2026-06-05',
            'expedition_info' => 'Travel Lintas Go',
            'shipping_cost' => 45000.00,
            'status' => 'menunggu_dipacking',
            'payment_status' => 'belum_lunas',
            'total_price' => 370000.00,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('partner_order_items')->insert([
            [
                'partner_order_id' => $order4Id,
                'raw_material_id' => $raw1Id,
                'quantity' => 4.00,
                'price' => 25000.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'partner_order_id' => $order4Id,
                'raw_material_id' => $raw3Id,
                'quantity' => 6.00,
                'price' => 45000.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // 8. Seed Branch Reports (Weekly Revenue for last 7 days)
        // 2026-05-28 to 2026-06-03
        $dates = [
            '2026-05-28',
            '2026-05-29',
            '2026-05-30',
            '2026-05-31',
            '2026-06-01',
            '2026-06-02',
            '2026-06-03'
        ];

        // Seed randomized but realistic daily revenues for branches
        $branchRevenues = [
            $branch1Id => [1200000, 1400000, 1800000, 2000000, 1300000, 1100000, 1500000],
            $branch2Id => [800000, 950000, 1500000, 1600000, 900000, 850000, 1100000],
            $branch3Id => [1100000, 1300000, 1700000, 1900000, 1200000, 1000000, 1400000],
            $branch4Id => [2500000, 2800000, 3500000, 3800000, 2600000, 2400000, 2900000],
            $branch5Id => [2000000, 2200000, 2900000, 3100000, 2100000, 1900000, 2400000],
        ];

        foreach ($branchRevenues as $bId => $revs) {
            foreach ($dates as $idx => $date) {
                $total = $revs[$idx];
                $qris = round($total * 0.6); // 60% QRIS
                $cash = $total - $qris;      // 40% Cash
                $portions = round($total / 25000); // Average porsi 25k

                DB::table('branch_reports')->insert([
                    'branch_id' => $bId,
                    'report_date' => $date,
                    'cash_setoran' => $cash,
                    'qris_setoran' => $qris,
                    'omset' => $total,
                    'portions_sold' => $portions,
                    'notes' => 'Laporan omset disalin dari WhatsApp pusat.',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // 9. Seed Edit Requests
        // Request 1: Pending (Salemba Branch Report - edit omset)
        // Get one report ID to reference
        $reportToEdit = DB::table('branch_reports')
            ->where('branch_id', $branch3Id)
            ->where('report_date', '2026-06-02')
            ->first();

        $adminId = DB::table('users')->where('role', 'admin')->first()->id;
        $ownerId = DB::table('users')->where('role', 'owner')->first()->id;

        DB::table('edit_requests')->insert([
            [
                'user_id' => $adminId,
                'model_type' => 'App\\Models\\BranchReport',
                'model_id' => $reportToEdit->id,
                'original_data' => json_encode(['omset' => $reportToEdit->omset, 'notes' => $reportToEdit->notes]),
                'requested_data' => json_encode(['omset' => 1500000.00, 'notes' => 'Typo entry, direvisi dari WA.']),
                'reason' => 'Admin salah mengetik nominal omset dari WA Mitra Salemba.',
                'status' => 'pending',
                'reviewer_id' => null,
                'reviewed_at' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Request 2: Approved (Partner Margonda Phone Number edit)
            [
                'user_id' => $adminId,
                'model_type' => 'App\\Models\\Partner',
                'model_id' => $partner1Id,
                'original_data' => json_encode(['phone' => '081234567890']),
                'requested_data' => json_encode(['phone' => '081299999999']),
                'reason' => 'Mitra Margonda ganti nomor WhatsApp bisnis.',
                'status' => 'approved',
                'reviewer_id' => $ownerId,
                'reviewed_at' => now()->subDay(),
                'created_at' => now()->subDay(),
                'updated_at' => now()->subDay(),
            ]
        ]);
    }
}
