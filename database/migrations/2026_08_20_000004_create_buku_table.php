<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('buku', function (Blueprint $table) {
            $table->string('isbn', 30)->primary();
            $table->string('judul', 255);
            $table->string('slug', 300)->unique()->nullable();
            $table->string('cover', 500)->nullable();
            $table->string('edisi', 100)->nullable();
            $table->string('deskripsi_fisik', 255)->nullable();
            $table->string('bahasa', 50)->nullable()->default('Indonesia');
            $table->boolean('tersedia')->default(true);
            $table->string('id_jenis', 10);
            $table->string('penulis', 255)->nullable();
            $table->string('penerbit', 255)->nullable();
            $table->timestamps();

            $table->foreign('id_jenis')->references('id_jenis')->on('jenis')->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('buku');
    }
};
