
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detail_pesanan', function (Blueprint $table) {
            $table->id('id_detail');
            $table->unsignedBigInteger('id_pesanan');
            $table->unsignedBigInteger('id_layanan');
            $table->decimal('harga_satuan', 12, 2);
            $table->decimal('subtotal', 12, 2);
            $table->decimal('kuantitas', 10, 2);
            $table->timestamps();

            $table->foreign('id_pesanan')
                ->references('id_pesanan')
                ->on('pesanan')
                ->onDelete('cascade');

            $table->foreign('id_layanan')
                ->references('id_layanan')
                ->on('layanan')
                ->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detail_pesanan');
    }
};
