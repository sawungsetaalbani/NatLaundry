<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengguna', function (Blueprint $table) {
            $table->id('id_pengguna');
            $table->string('nama_pengguna');
            $table->string('username')->unique();
            $table->string('password');
            $table->string('no_telpon', 20);
            $table->string('alamat');
            $table->enum('role', ['admin', 'pelanggan'])
                ->default('pelanggan');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengguna');
    }
};

