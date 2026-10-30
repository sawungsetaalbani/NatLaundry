<?php

namespace App\Http\Controllers;

use App\Models\Layanan;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\QueryException;
use Throwable;

class LayananController extends Controller
{
    public function index()
    {
        try {
            $layanan = Layanan::with('jenisLayanan')->get();

            return response()->json([
                'success' => true,
                'message' => 'Data layanan berhasil diambil.',
                'data' => $layanan,
            ], 200);
        } catch (Throwable $e) {
            return $this->serverError();
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'id_jenis' => 'required|exists:jenis_layanan,id_jenis',
                'nama_layanan' => 'required|string|max:255',
                'tipe' => 'required|string|max:255',
                'satuan' => 'required|string|max:50',
                'harga' => 'required|numeric|min:0',
            ], [
                'id_jenis.required' => 'Jenis layanan wajib dipilih.',
                'id_jenis.exists' => 'Jenis layanan tidak ditemukan.',
                'nama_layanan.required' => 'Nama layanan wajib diisi.',
                'tipe.required' => 'Tipe layanan wajib diisi.',
                'satuan.required' => 'Satuan layanan wajib diisi.',
                'harga.required' => 'Harga layanan wajib diisi.',
                'harga.numeric' => 'Harga layanan harus berupa angka.',
                'harga.min' => 'Harga layanan tidak boleh kurang dari nol.',
            ]);

            $layanan = Layanan::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Layanan berhasil ditambahkan.',
                'data' => $layanan->load('jenisLayanan'),
            ], 201);
        } catch (ValidationException $e) {
            return $this->validationError($e);
        } catch (QueryException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Layanan gagal disimpan karena terjadi masalah pada data.',
            ], 500);
        } catch (Throwable $e) {
            return $this->serverError();
        }
    }

    public function show(string $id)
    {
        try {
            $layanan = Layanan::with('jenisLayanan')->find($id);

            if (!$layanan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Layanan tidak ditemukan.',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Detail layanan berhasil diambil.',
                'data' => $layanan,
            ], 200);
        } catch (Throwable $e) {
            return $this->serverError();
        }
    }

    public function update(Request $request, string $id)
    {
        try {
            $layanan = Layanan::find($id);

            if (!$layanan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Layanan tidak ditemukan.',
                ], 404);
            }

            $validated = $request->validate([
                'id_jenis' => 'sometimes|required|exists:jenis_layanan,id_jenis',
                'nama_layanan' => 'sometimes|required|string|max:255',
                'tipe' => 'sometimes|required|string|max:255',
                'satuan' => 'sometimes|required|string|max:50',
                'harga' => 'sometimes|required|numeric|min:0',
            ], [
                'id_jenis.exists' => 'Jenis layanan tidak ditemukan.',
                'nama_layanan.required' => 'Nama layanan wajib diisi.',
                'tipe.required' => 'Tipe layanan wajib diisi.',
                'satuan.required' => 'Satuan layanan wajib diisi.',
                'harga.numeric' => 'Harga layanan harus berupa angka.',
                'harga.min' => 'Harga layanan tidak boleh kurang dari nol.',
            ]);

            $layanan->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Layanan berhasil diperbarui.',
                'data' => $layanan->fresh()->load('jenisLayanan'),
            ], 200);
        } catch (ValidationException $e) {
            return $this->validationError($e);
        } catch (QueryException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Layanan gagal diperbarui karena terjadi masalah pada data.',
            ], 500);
        } catch (Throwable $e) {
            return $this->serverError();
        }
    }

    public function destroy(string $id)
    {
        try {
            $layanan = Layanan::find($id);

            if (!$layanan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Layanan tidak ditemukan.',
                ], 404);
            }

            $layanan->delete();

            return response()->json([
                'success' => true,
                'message' => 'Layanan berhasil dihapus.',
            ], 200);
        } catch (QueryException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Layanan tidak dapat dihapus karena masih digunakan dalam detail pesanan.',
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
