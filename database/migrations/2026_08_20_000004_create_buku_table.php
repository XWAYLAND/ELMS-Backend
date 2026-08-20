<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('buku', function (Blueprint $table) {
            $table->string('isbn')->primary();
            $table->string('judul');
            $table->string('edisi')->nullable();
            $table->text('deskripsi_fisik')->nullable();
            $table->string('bahasa')->default('Indonesia');
            $table->string('cover')->nullable(); // URL gambar cover
            $table->string('id_jenis');
            $table->string('id_penulis');
            $table->string('id_penerbit');
            $table->timestamps();

            $table->foreign('id_jenis')->references('id_jenis')->on('jenis')->onDelete('restrict');
            $table->foreign('id_penulis')->references('id_penulis')->on('penulis')->onDelete('restrict');
            $table->foreign('id_penerbit')->references('id_penerbit')->on('penerbit')->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('buku');
    }
};
