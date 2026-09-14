<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('anggota', function (Blueprint $table) {
            $table->string('nis', 20)->primary();
            $table->string('nama_lengkap', 150);
            $table->string('kelas', 20);
            $table->string('password', 255);
            $table->string('fcm_token', 255)->nullable(); // FCM token untuk push notification Flutter
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('anggota');
    }
};
