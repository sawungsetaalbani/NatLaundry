<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pesanan extends Model
{
    use HasFactory;

    protected $table = 'pesanan';

    protected $primaryKey = 'id_pesanan';

    protected $fillable = [
        'id_pengguna',
        'tgl_pesanan',
        'status_pesanan',
        'total_harga',
        'status_pembayaran',
    ];

    protected function casts(): array
    {
        return [
            'tgl_pesanan' => 'date:Y-m-d',
            'total_harga' => 'decimal:2',
        ];
    }

    public function pengguna()
    {
        return $this->belongsTo(
            Pengguna::class,
            'id_pengguna',
            'id_pengguna'
        );
    }

    public function detailPesanan()
    {
        return $this->hasMany(
            DetailPesanan::class,
            'id_pesanan',
            'id_pesanan'
        );
    }
}