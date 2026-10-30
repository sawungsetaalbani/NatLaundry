<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pesanan', function (Blueprint $table) {
            $table->id('id_pesanan');

            $table->unsignedBigInteger('id_pengguna');

            $table->date('tgl_pesanan');
            $table->enum('status_pesanan', ['Diproses', 'Selesai'])
                ->default('Diproses');
            $table->decimal('total_harga', 12, 2)->default(0);
            $table->enum('status_pembayaran', ['Belum Lunas', 'Lunas'])
                ->default('Belum Lunas');

            $table->timestamps();

            $table->foreign('id_pengguna')
                ->references('id_pengguna')
                ->on('pengguna')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pesanan');
    }
};