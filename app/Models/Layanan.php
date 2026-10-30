<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Layanan extends Model
{
    use HasFactory;

    protected $table = 'layanan';

    protected $primaryKey = 'id_layanan';

    protected $fillable = [
        'id_jenis',
        'nama_layanan',
        'tipe',
        'satuan',
        'harga',
    ];

    protected function casts(): array
    {
        return [
            'harga' => 'decimal:2',
        ];
    }

    public function jenisLayanan()
    {
        return $this->belongsTo(
            JenisLayanan::class,
            'id_jenis',
            'id_jenis'
        );
    }

    public function detailPesanan()
    {
        return $this->hasMany(
            DetailPesanan::class,
            'id_layanan',
            'id_layanan'
        );
    }
}
