<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penerbit', function (Blueprint $table) {
            $table->string('id_penerbit', 10)->primary();
            $table->string('nama_penerbit', 150)->unique();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penerbit');
    }
};
