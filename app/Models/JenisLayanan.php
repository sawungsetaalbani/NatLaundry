<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JenisLayanan extends Model
{
    use HasFactory;

    protected $table = 'jenis_layanan';

    protected $primaryKey = 'id_jenis';

    protected $fillable = [
        'nama_jenis',
    ];

    public function layanan()
    {
        return $this->hasMany(
            Layanan::class,
            'id_jenis',
            'id_jenis'
        );
    }
}
