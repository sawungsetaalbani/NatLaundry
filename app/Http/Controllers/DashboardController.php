<?php

namespace App\Http\Controllers;

use App\Models\Pesanan;
use App\Models\Pengguna;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $tahun = (int) $request->query('year', now()->year);
        $bulan = (int) $request->query('month', now()->month);

        if (
            $tahun < 2000 ||
            $tahun > 2100 ||
            $bulan < 1 ||
            $bulan > 12
        ) {
            return response()->json([
                'message' => 'Bulan atau tahun tidak valid.'
            ], 422);
        }

        $pesananLunas = Pesanan::where(
            'status_pembayaran',
            'Lunas'
        );

        $totalPendapatan = (clone $pesananLunas)
            ->sum('total_harga');

        $pendapatanBulanan = (clone $pesananLunas)
            ->whereYear('tgl_pesanan', $tahun)
            ->whereMonth('tgl_pesanan', $bulan)
            ->sum('total_harga');

        return response()->json([
            'pesanan_diproses' => Pesanan::where(
                'status_pesanan',
                'Diproses'
            )->count(),

            'pelanggan_aktif' => Pengguna::where(
                'role',
                'pelanggan'
            )->count(),

            'total_pendapatan' => (float) $totalPendapatan,

            'pendapatan_bulanan' => [
                'bulan' => $bulan,
                'tahun' => $tahun,
                'total' => (float) $pendapatanBulanan,
            ],
        ]);
    }

    public function grafikPenjualan(Request $request)
    {
        $tahun = (int) $request->query('year', now()->year);

        if ($tahun < 2000 || $tahun > 2100) {
            return response()->json([
                'message' => 'Tahun tidak valid.'
            ], 422);
        }

        $data = Pesanan::selectRaw(
            'MONTH(tgl_pesanan) as bulan, SUM(total_harga) as total'
        )
            ->where('status_pembayaran', 'Lunas')
            ->whereYear('tgl_pesanan', $tahun)
            ->groupByRaw('MONTH(tgl_pesanan)')
            ->get()
            ->keyBy('bulan');

        $grafik = [];

        for ($bulan = 1; $bulan <= 12; $bulan++) {
            $grafik[] = [
                'bulan' => $bulan,
                'total' => (float) ($data->get($bulan)?->total ?? 0),
            ];
        }

        return response()->json([
            'tahun' => $tahun,
            'data' => $grafik,
        ]);
    }

    public function pesananDiproses()
    {
        $pesanan = Pesanan::with('pengguna:id_pengguna,nama_pengguna')
            ->select([
                'id_pesanan',
                'id_pengguna',
                'tgl_pesanan',
                'status_pesanan',
                'total_harga',
                'status_pembayaran',
            ])
            ->where('status_pesanan', 'Diproses')
            ->orderByDesc('tgl_pesanan')
            ->orderByDesc('id_pesanan')
            ->paginate(10);

        return response()->json($pesanan);
    }
}
