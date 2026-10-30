<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Pesanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $validated = $request->validate([
            'tanggal_mulai' => ['nullable', 'date'],
            'tanggal_akhir' => [
                'nullable',
                'date',
                'after_or_equal:tanggal_mulai',
            ],
            'status_pembayaran' => [
                'nullable',
                'in:Lunas,Belum Lunas',
            ],
            'page' => ['nullable', 'integer', 'min:1'],
        ]);

        $query = Pesanan::with([
            'pengguna:id_pengguna,nama_pengguna',
        ]);

        if (!empty($validated['tanggal_mulai'])) {
            $query->whereDate(
                'tgl_pesanan',
                '>=',
                $validated['tanggal_mulai']
            );
        }

        if (!empty($validated['tanggal_akhir'])) {
            $query->whereDate(
                'tgl_pesanan',
                '<=',
                $validated['tanggal_akhir']
            );
        }

        if (!empty($validated['status_pembayaran'])) {
            $query->where(
                'status_pembayaran',
                $validated['status_pembayaran']
            );
        }

        $ringkasan = (clone $query)
            ->selectRaw('COUNT(*) as jumlah_pesanan')
            ->selectRaw(
                "SUM(CASE WHEN status_pembayaran = 'Lunas'
                AND status_pesanan != 'Dibatalkan'
                THEN total_harga ELSE 0 END) as total_pendapatan"
            )
            ->selectRaw(
                "SUM(CASE WHEN status_pembayaran = 'Lunas'
                THEN 1 ELSE 0 END) as jumlah_lunas"
            )
            ->selectRaw(
                "SUM(CASE WHEN status_pembayaran = 'Belum Lunas'
                THEN 1 ELSE 0 END) as jumlah_belum_lunas"
            )
            ->first();

        $pesanan = $query
            ->orderByDesc('tgl_pesanan')
            ->orderByDesc('id_pesanan')
            ->paginate(10)
            ->withQueryString();

        return response()->json([
            'message' => 'Laporan transaksi berhasil diambil.',
            'filter' => [
                'tanggal_mulai' => $validated['tanggal_mulai'] ?? null,
                'tanggal_akhir' => $validated['tanggal_akhir'] ?? null,
                'status_pembayaran' =>
                    $validated['status_pembayaran'] ?? null,
            ],
            'ringkasan' => [
                'jumlah_pesanan' => (int) $ringkasan->jumlah_pesanan,
                'total_pendapatan' =>
                    (float) ($ringkasan->total_pendapatan ?? 0),
                'jumlah_lunas' => (int) $ringkasan->jumlah_lunas,
                'jumlah_belum_lunas' =>
                    (int) $ringkasan->jumlah_belum_lunas,
            ],
            'data' => $pesanan,
        ]);
    }
    
    public function exportPdf(Request $request)
    {
        $validated = $request->validate([
            'tanggal_mulai' => ['nullable', 'date'],
            'tanggal_akhir' => [
                'nullable',
                'date',
                'after_or_equal:tanggal_mulai',
            ],
            'status_pembayaran' => [
                'nullable',
                'in:Lunas,Belum Lunas',
            ],
        ]);

        $query = Pesanan::with('pengguna:id_pengguna,nama_pengguna');

        if (!empty($validated['tanggal_mulai'])) {
            $query->whereDate(
                'tgl_pesanan',
                '>=',
                $validated['tanggal_mulai']
            );
        }

        if (!empty($validated['tanggal_akhir'])) {
            $query->whereDate(
                'tgl_pesanan',
                '<=',
                $validated['tanggal_akhir']
            );
        }

        if (!empty($validated['status_pembayaran'])) {
            $query->where(
                'status_pembayaran',
                $validated['status_pembayaran']
            );
        }

        $pesanan = $query
            ->orderBy('tgl_pesanan')
            ->orderBy('id_pesanan')
            ->get();

        $totalPendapatan = $pesanan
            ->filter(fn ($item) =>
                $item->status_pembayaran === 'Lunas'
                && $item->status_pesanan !== 'Dibatalkan'
            )
            ->sum('total_harga');

        $pdf = Pdf::loadView('pdf.laporan-transaksi', [
            'pesanan' => $pesanan,
            'tanggalMulai' => $validated['tanggal_mulai'] ?? null,
            'tanggalAkhir' => $validated['tanggal_akhir'] ?? null,
            'statusPembayaran' => $validated['status_pembayaran'] ?? null,
            'totalPendapatan' => $totalPendapatan,
        ])->setPaper('a4', 'landscape');

        return $pdf->download('laporan-transaksi.pdf');
    }
}
