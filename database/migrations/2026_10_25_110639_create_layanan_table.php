
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('layanan', function (Blueprint $table) {
            $table->id('id_layanan');
            $table->unsignedBigInteger('id_jenis');
            $table->string('nama_layanan');
            $table->string('tipe');
            $table->string('satuan');
            $table->decimal('harga', 12, 2);
            $table->timestamps();

            $table->foreign('id_jenis')
                ->references('id_jenis')
                ->on('jenis_layanan')
                ->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('layanan');
    }
};
