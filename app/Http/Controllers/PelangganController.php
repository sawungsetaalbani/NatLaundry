<?php

namespace App\Http\Controllers;

use App\Models\Pesanan;
use Illuminate\Http\Request;
use Throwable;
use Barryvdh\DomPDF\Facade\Pdf;

class PelangganController extends Controller
{
    public function index(Request $request)
    {
        try {
            $pesanan = Pesanan::with([
                'detailPesanan.layanan'
            ])
                ->where('id_pengguna', $request->user()->id_pengguna)
                ->orderBy('tgl_pesanan', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Daftar pesanan berhasil ditemukan.',
                'data' => $pesanan,
            ], 200);
        } catch (Throwable $e) {
            return $this->serverError();
        }
    }

    public function show(Request $request, string $id)
    {
        try {
            $pesanan = Pesanan::with([
                'detailPesanan.layanan'
            ])
                ->where('id_pengguna', $request->user()->id_pengguna)
                ->find($id);

            if (!$pesanan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pesanan tidak ditemukan.',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Detail pesanan ditemukan.',
                'data' => $pesanan,
            ], 200);
        } catch (Throwable $e) {
            return $this->serverError();
        }
    }

    public function riwayat(Request $request)
    {
        try {
            $pesanan = Pesanan::with([
                'detailPesanan.layanan'
            ])
                ->where('id_pengguna', $request->user()->id_pengguna)
                ->where('status_pesanan', 'Selesai')
                ->orderBy('tgl_pesanan', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Riwayat pesanan berhasil ditemukan.',
                'data' => $pesanan,
            ], 200);
        } catch (Throwable $e) {
            return $this->serverError();
        }
    }

    private function serverError()
    {
        return response()->json([
            'success' => false,
            'message' => 'Terjadi kesalahan pada server.',
        ], 500);
    }

    public function exportPdf($id)
    {
        $pengguna = auth()->user();

        $pesanan = \App\Models\Pesanan::with(
            'pengguna',
            'detailPesanan.layanan'
        )
            ->where('id_pengguna', $pengguna->id_pengguna)
            ->where('id_pesanan', $id)
            ->firstOrFail();

        $pdf = Pdf::loadView('pdf.pesanan-pelanggan', [
            'pesanan' => $pesanan,
        ]);

        return $pdf->download(
            'pesanan-' . $pesanan->id_pesanan . '.pdf'
        );
    }
    public function exportRiwayatPdf(Request $request)
    {
        $request->validate([
            'bulan' => ['required', 'integer', 'between:1,12'],
            'tahun' => ['required', 'integer', 'between:2000,2100'],
        ]);

        $pengguna = auth()->user();

        $pesanan = Pesanan::with('detailPesanan.layanan')
            ->where('id_pengguna', $pengguna->id_pengguna)
            ->where('status_pesanan', 'Selesai')
            ->whereYear('tgl_pesanan', $request->tahun)
            ->whereMonth('tgl_pesanan', $request->bulan)
            ->orderByDesc('tgl_pesanan')
            ->orderByDesc('id_pesanan')
            ->get();

        $pdf = Pdf::loadView('pdf.riwayat-pelanggan', [
            'pengguna' => $pengguna,
            'pesanan' => $pesanan,
            'bulan' => (int) $request->bulan,
            'tahun' => (int) $request->tahun,
        ])->setPaper('a4', 'portrait');

        return $pdf->download(
            'riwayat-transaksi-' .
            $request->tahun . '-' .
            str_pad($request->bulan, 2, '0', STR_PAD_LEFT) .
            '.pdf'
        );
    }
}