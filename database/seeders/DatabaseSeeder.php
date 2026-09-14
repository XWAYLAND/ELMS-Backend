<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     * Urutan sesuai dependency (parent table terlebih dahulu).
     */
    public function run(): void
    {
        $this->call([
            JenisSeeder::class,
            PenulisSeeder::class,
            PenerbitSeeder::class,
            BukuSeeder::class,
            AnggotaSeeder::class,
            PegawaiSeeder::class,
            PeminjamanSeeder::class,
        ]);
    }
}
