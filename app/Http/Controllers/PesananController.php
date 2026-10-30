<?php

namespace App\Http\Controllers;

use App\Models\Pesanan;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\QueryException;
use Throwable;

class PesananController extends Controller
{
    public function index()
    {
        try {
            $pesanan = Pesanan::with([
                'pengguna',
                'detailPesanan.layanan'
            ])->get();

            return response()->json([
                'success' => true,
                'message' => 'Data pesanan berhasil ditemukan.',
                'data' => $pesanan,
            ], 200);
        } catch (Throwable $e) {
            return $this->serverError();
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'id_pengguna' => 'required|exists:pengguna,id_pengguna',
                'tgl_pesanan' => 'required|date',
                'status_pesanan' => 'sometimes|required|in:Diproses,Selesai',
                'total_harga' => 'sometimes|required|numeric|min:0',
                'status_pembayaran' => 'sometimes|required|in:Belum Lunas,Lunas',
            ], [
                'id_pengguna.required' => 'Pelanggan wajib dipilih.',
                'id_pengguna.exists' => 'Pelanggan tidak ditemukan.',
                'tgl_pesanan.required' => 'Tanggal pesanan wajib diisi.',
                'tgl_pesanan.date' => 'Format tanggal pesanan tidak valid.',
                'status_pesanan.required' => 'Status pesanan harus Diproses atau Selesai.',
                'total_harga.numeric' => 'Total harga harus berupa angka.',
                'total_harga.min' => 'Total harga tidak boleh kurang dari nol.',
                'status_pembayaran.in' => 'Status pembayaran harus Belum Lunas atau Lunas.',
            ]);

            $pesanan = Pesanan::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Pesanan berhasil ditambahkan.',
                'data' => $pesanan->load([
                    'pengguna',
                    'detailPesanan.layanan'
                ]),
            ], 201);
        } catch (ValidationException $e) {
            return $this->validationError($e);
        } catch (QueryException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Pesanan gagal disimpan karena terjadi masalah pada data.',
            ], 500);
        } catch (Throwable $e) {
            return $this->serverError();
        }
    }

    public function show(string $id)
    {
        try {
            $pesanan = Pesanan::with([
                'pengguna',
                'detailPesanan.layanan'
            ])->find($id);

            if (!$pesanan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pesanan tidak ditemukan.',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Detail pesanan berhasil ditemukan.',
                'data' => $pesanan,
            ], 200);
        } catch (Throwable $e) {
            return $this->serverError();
        }
    }

    public function update(Request $request, string $id)
    {
        try {
            $pesanan = Pesanan::find($id);

            if (!$pesanan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pesanan tidak ditemukan.',
                ], 404);
            }

            $validated = $request->validate([
                'id_pengguna' => 'sometimes|required|exists:pengguna,id_pengguna',
                'tgl_pesanan' => 'sometimes|required|date',
                'status_pesanan' => 'sometimes|required|string|max:255',
                'total_harga' => 'sometimes|required|numeric|min:0',
                'status_pembayaran' => 'sometimes|required|in:Belum Lunas,Lunas',
            ], [
                'id_pengguna.exists' => 'Pelanggan tidak ditemukan.',
                'tgl_pesanan.date' => 'Format tanggal pesanan tidak valid.',
                'total_harga.numeric' => 'Total harga harus berupa angka.',
                'total_harga.min' => 'Total harga tidak boleh kurang dari nol.',
                'status_pembayaran.in' => 'Status pembayaran harus Belum Lunas atau Lunas.',
            ]);

            $pesanan->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Pesanan berhasil diperbarui.',
                'data' => $pesanan->fresh()->load([
                    'pengguna',
                    'detailPesanan.layanan'
                ]),
            ], 200);
        } catch (ValidationException $e) {
            return $this->validationError($e);
        } catch (QueryException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Pesanan gagal diperbarui karena terjadi masalah pada data.',
            ], 500);
        } catch (Throwable $e) {
            return $this->serverError();
        }
    }

    public function destroy(string $id)
    {
        try {
            $pesanan = Pesanan::find($id);

            if (!$pesanan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pesanan tidak ditemukan.',
                ], 404);
            }

            $pesanan->delete();

            return response()->json([
                'success' => true,
                'message' => 'Pesanan berhasil dihapus.',
            ], 200);
        } catch (QueryException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Pesanan tidak dapat dihapus karena masih memiliki detail atau data terkait.',
            ], 409);
        } catch (Throwable $e) {
            return $this->serverError();
        }
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