<?php

namespace App\Http\Controllers;

use App\Models\DetailPesanan;
use App\Models\Layanan;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Throwable;

class DetailPesananController extends Controller
{
    public function index()
    {
        try {
            $detail = DetailPesanan::with([
                'pesanan',
                'layanan'
            ])->get();

            return response()->json([
                'success' => true,
                'message' => 'Data detail pesanan berhasil diambil.',
                'data' => $detail,
            ], 200);
        } catch (Throwable $e) {
            return $this->serverError();
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'id_pesanan' => 'required|exists:pesanan,id_pesanan',
                'id_layanan' => 'required|exists:layanan,id_layanan',
                'kuantitas' => 'required|numeric|gt:0',
            ], [
                'id_pesanan.required' => 'Pesanan wajib dipilih.',
                'id_pesanan.exists' => 'Pesanan tidak ditemukan.',
                'id_layanan.required' => 'Layanan wajib dipilih.',
                'id_layanan.exists' => 'Layanan tidak ditemukan.',
                'kuantitas.required' => 'Kuantitas wajib diisi.',
                'kuantitas.numeric' => 'Kuantitas harus berupa angka.',
                'kuantitas.gt' => 'Kuantitas harus lebih dari nol.',
            ]);

            $detail = DB::transaction(function () use ($validated) {
                $layanan = Layanan::findOrFail($validated['id_layanan']);
                $kuantitas = (float) $validated['kuantitas'];
                $harga = (float) $layanan->harga;

                $detail = DetailPesanan::create([
                    'id_pesanan' => $validated['id_pesanan'],
                    'id_layanan' => $validated['id_layanan'],
                    'kuantitas' => $kuantitas,
                    'harga_satuan' => $harga,
                    'subtotal' => round($kuantitas * $harga, 2),
                ]);

                $this->hitungTotalPesanan($detail->id_pesanan);

                return $detail;
            });

            return response()->json([
                'success' => true,
                'message' => 'Detail pesanan berhasil ditambahkan.',
                'data' => $detail->load(['pesanan', 'layanan']),
            ], 201);
        } catch (ValidationException $e) {
            return $this->validationError($e);
        } catch (QueryException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Detail pesanan gagal disimpan karena terjadi masalah pada data.',
            ], 500);
        } catch (Throwable $e) {
            return $this->serverError();
        }
    }

    public function show(string $id)
    {
        try {
            $detail = DetailPesanan::with([
                'pesanan',
                'layanan'
            ])->find($id);

            if (!$detail) {
                return response()->json([
                    'success' => false,
                    'message' => 'Detail pesanan tidak ditemukan.',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Detail pesanan berhasil diambil.',
                'data' => $detail,
            ], 200);
        } catch (Throwable $e) {
            return $this->serverError();
        }
    }

    public function update(Request $request, string $id)
    {
        try {
            $detail = DetailPesanan::find($id);

            if (!$detail) {
                return response()->json([
                    'success' => false,
                    'message' => 'Detail pesanan tidak ditemukan.',
                ], 404);
            }

            $validated = $request->validate([
                'id_pesanan' => 'sometimes|required|exists:pesanan,id_pesanan',
                'id_layanan' => 'sometimes|required|exists:layanan,id_layanan',
                'kuantitas' => 'sometimes|required|numeric|gt:0',
            ], [
                'id_pesanan.exists' => 'Pesanan tidak ditemukan.',
                'id_layanan.exists' => 'Layanan tidak ditemukan.',
                'kuantitas.numeric' => 'Kuantitas harus berupa angka.',
                'kuantitas.gt' => 'Kuantitas harus lebih dari nol.',
            ]);

            $detail = DB::transaction(function () use ($detail, $validated) {
                $pesananLama = $detail->id_pesanan;

                $idPesananBaru = $validated['id_pesanan']
                    ?? $detail->id_pesanan;

                $idLayanan = $validated['id_layanan']
                    ?? $detail->id_layanan;

                $kuantitas = (float) (
                    $validated['kuantitas'] ?? $detail->kuantitas
                );

                $layanan = Layanan::findOrFail($idLayanan);
                $harga = (float) $layanan->harga;

                $detail->update([
                    'id_pesanan' => $idPesananBaru,
                    'id_layanan' => $idLayanan,
                    'kuantitas' => $kuantitas,
                    'harga_satuan' => $harga,
                    'subtotal' => round($kuantitas * $harga, 2),
                ]);

                $this->hitungTotalPesanan($pesananLama);

                if ($pesananLama != $idPesananBaru) {
                    $this->hitungTotalPesanan($idPesananBaru);
                }

                return $detail->fresh();
            });

            return response()->json([
                'success' => true,
                'message' => 'Detail pesanan berhasil diperbarui.',
                'data' => $detail->load(['pesanan', 'layanan']),
            ], 200);
        } catch (ValidationException $e) {
            return $this->validationError($e);
        } catch (QueryException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Detail pesanan gagal diperbarui karena terjadi masalah pada data.',
            ], 500);
        } catch (Throwable $e) {
            return $this->serverError();
        }
    }

    public function destroy(string $id)
    {
        try {
            $detail = DetailPesanan::find($id);

            if (!$detail) {
                return response()->json([
                    'success' => false,
                    'message' => 'Detail pesanan tidak ditemukan.',
                ], 404);
            }

            DB::transaction(function () use ($detail) {
                $idPesanan = $detail->id_pesanan;

                $detail->delete();

                $this->hitungTotalPesanan($idPesanan);
            });

            return response()->json([
                'success' => true,
                'message' => 'Detail pesanan berhasil dihapus.',
            ], 200);
        } catch (QueryException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Detail pesanan tidak dapat dihapus karena masih terkait dengan data lain.',
            ], 409);
        } catch (Throwable $e) {
            return $this->serverError();
        }
    }

    private function hitungTotalPesanan($idPesanan)
    {
        $total = DetailPesanan::where('id_pesanan', $idPesanan)
            ->sum('subtotal');

        DB::table('pesanan')
            ->where('id_pesanan', $idPesanan)
            ->update([
                'total_harga' => $total
            ]);
    }

    private function validationError(ValidationException $e)
    {
        $errors = $e->errors();

        $message = collect($errors)->flatten()->first()
            ?? 'Data yang dikirim tidak valid.';

        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors,
        ], 422);
    }

    private function serverError()
    {
        return response()->json([
            'success' => false,
            'message' => 'Terjadi kesalahan pada server.',
        ], 500);
    }
}
