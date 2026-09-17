<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('favorit', function (Blueprint $table) {
            $table->string('nis', 20);
            $table->string('isbn', 30);
            $table->timestamp('created_at')->useCurrent();

            $table->primary(['nis', 'isbn']);
            $table->foreign('nis')->references('nis')->on('anggota')->cascadeOnDelete();
            $table->foreign('isbn')->references('isbn')->on('buku')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('favorit');
    }
};
