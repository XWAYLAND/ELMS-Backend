<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('peminjaman', function (Blueprint $table) {
            $table->string('id_transaksi')->primary();
            $table->date('waktu_pinjam');
            $table->date('batas_kembali');
            $table->enum('status', ['pending', 'dipinjam', 'dikembalikan', 'terlambat'])->default('pending');
            $table->string('nis');
            $table->string('isbn');
            $table->string('id_pegawai');
            $table->timestamps();

            $table->foreign('nis')->references('nis')->on('anggota')->onDelete('restrict');
            $table->foreign('isbn')->references('isbn')->on('buku')->onDelete('restrict');
            $table->foreign('id_pegawai')->references('id_pegawai')->on('pegawai')->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('peminjaman');
    }
};
