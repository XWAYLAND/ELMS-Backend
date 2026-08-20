<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penulis', function (Blueprint $table) {
            $table->string('id_penulis')->primary();
            $table->string('nama_penulis');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penulis');
    }
};
