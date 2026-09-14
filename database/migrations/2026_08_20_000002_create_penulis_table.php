<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penulis', function (Blueprint $table) {
            $table->string('id_penulis', 10)->primary();
            $table->string('nama_penulis', 150)->unique();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penulis');
    }
};
