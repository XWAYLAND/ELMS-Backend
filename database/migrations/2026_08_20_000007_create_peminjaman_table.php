<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('peminjaman', function (Blueprint $table) {
            $table->string('id_transaksi', 20)->primary();
            $table->string('nis', 20);
            $table->string('isbn', 30);
            $table->string('id_pegawai', 20)->nullable();
            $table->string('kode_unik', 10)->unique();
            $table->dateTime('kode_unik_expires_at')->nullable();
            $table->dateTime('waktu_pengajuan');
            $table->dateTime('batas_waktu')->nullable();
            $table->integer('durasi_hari')->default(7);
            $table->dateTime('tanggal_kembali')->nullable();
            $table->enum('status', ['menunggu', 'aktif', 'menunggu_kembali', 'terlambat', 'dikembalikan', 'ditolak'])->default('menunggu');
            $table->timestamps();

            $table->foreign('nis')->references('nis')->on('anggota')->onDelete('restrict');
            $table->foreign('isbn')->references('isbn')->on('buku')->onDelete('restrict');
            $table->foreign('id_pegawai')->references('id_pegawai')->on('pegawai')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('peminjaman');
    }
};
