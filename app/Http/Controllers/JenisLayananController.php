<?php

namespace App\Http\Controllers;

use App\Models\JenisLayanan;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\QueryException;
use Throwable;

class JenisLayananController extends Controller
{
    public function index()
    {
        try {
            $jenis = JenisLayanan::with('layanan')->get();

            return response()->json([
                'success' => true,
                'message' => 'Data jenis layanan berhasil diambil.',
                'data' => $jenis,
            ], 200);
        } catch (Throwable $e) {
            return $this->serverError();
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'nama_jenis' => 'required|string|max:255',
            ], [
                'nama_jenis.required' => 'Nama jenis layanan wajib diisi.',
                'nama_jenis.string' => 'Nama jenis layanan harus berupa teks.',
                'nama_jenis.max' => 'Nama jenis layanan maksimal 255 karakter.',
            ]);

            $jenis = JenisLayanan::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Jenis layanan berhasil ditambahkan.',
                'data' => $jenis,
            ], 201);
        } catch (ValidationException $e) {
            return $this->validationError($e);
        } catch (QueryException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Jenis layanan gagal disimpan karena terjadi masalah pada data.',
            ], 500);
        } catch (Throwable $e) {
            return $this->serverError();
        }
    }

    public function show(string $id)
    {
        try {
            $jenis = JenisLayanan::with('layanan')->find($id);

            if (!$jenis) {
                return response()->json([
                    'success' => false,
                    'message' => 'Jenis layanan tidak ditemukan.',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Detail jenis layanan berhasil diambil.',
                'data' => $jenis,
            ], 200);
        } catch (Throwable $e) {
            return $this->serverError();
        }
    }

    public function update(Request $request, string $id)
    {
        try {
            $jenis = JenisLayanan::find($id);

            if (!$jenis) {
                return response()->json([
                    'success' => false,
                    'message' => 'Jenis layanan tidak ditemukan.',
                ], 404);
            }

            $validated = $request->validate([
                'nama_jenis' => 'required|string|max:255',
            ], [
                'nama_jenis.required' => 'Nama jenis layanan wajib diisi.',
                'nama_jenis.string' => 'Nama jenis layanan harus berupa teks.',
                'nama_jenis.max' => 'Nama jenis layanan maksimal 255 karakter.',
            ]);

            $jenis->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Jenis layanan berhasil diperbarui.',
                'data' => $jenis->fresh()->load('layanan'),
            ], 200);
        } catch (ValidationException $e) {
            return $this->validationError($e);
        } catch (QueryException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Jenis layanan gagal diperbarui karena terjadi masalah pada data.',
            ], 500);
        } catch (Throwable $e) {
            return $this->serverError();
        }
    }

    public function destroy(string $id)
    {
        try {
            $jenis = JenisLayanan::find($id);

            if (!$jenis) {
                return response()->json([
                    'success' => false,
                    'message' => 'Jenis layanan tidak ditemukan.',
                ], 404);
            }

            $jenis->delete();

            return response()->json([
                'success' => true,
                'message' => 'Jenis layanan berhasil dihapus.',
            ], 200);
        } catch (QueryException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Jenis layanan tidak dapat dihapus karena masih digunakan oleh layanan lain.',
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
