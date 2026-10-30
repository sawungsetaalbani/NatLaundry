<?php

namespace App\Http\Controllers;

use App\Models\Pengguna;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\QueryException;
use Throwable;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        try {
            $validated = $request->validate([
                'nama_pengguna' => 'required|string|max:255',
                'no_telpon' => 'required|string|max:20',
                'username' => 'required|string|max:50|unique:pengguna,username',
                'alamat' => 'required|string|max:255',
                'password' => ['required', 'confirmed', Password::min(8)],
            ], [
                'nama_pengguna.required' => 'Nama pengguna wajib diisi.',
                'no_telpon.required' => 'Nomor telepon wajib diisi.',
                'username.required' => 'Username wajib diisi.',
                'username.unique' => 'Username sudah digunakan.',
                'alamat.required' => 'Alamat wajib diisi.',
                'password.required' => 'Password wajib diisi.',
                'password.confirmed' => 'Konfirmasi password tidak sesuai.',
                'password.min' => 'Password minimal 8 karakter.',
            ]);

            $pengguna = Pengguna::create([
                'nama_pengguna' => $validated['nama_pengguna'],
                'no_telpon' => $validated['no_telpon'],
                'username' => $validated['username'],
                'alamat' => $validated['alamat'],
                'password' => $validated['password'],
                'role' => 'pelanggan',
            ]);

            $token = $pengguna->createToken('auth_token')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Registrasi berhasil.',
                'data' => [
                    'pengguna' => $pengguna,
                    'token' => $token,
                ],
            ], 201);
        } catch (ValidationException $e) {
            return $this->validationError($e);
        } catch (QueryException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Registrasi gagal karena terjadi masalah pada data.',
            ], 500);
        } catch (Throwable $e) {
            return $this->serverError();
        }
    }

    public function login(Request $request)
    {
        try {
            $validated = $request->validate([
                'username' => 'required|string',
                'password' => 'required|string',
            ], [
                'username.required' => 'Username wajib diisi.',
                'password.required' => 'Password wajib diisi.',
            ]);

            $pengguna = Pengguna::where(
                'username',
                $validated['username']
            )->first();

            if (
                !$pengguna ||
                !Hash::check($validated['password'], $pengguna->password)
            ) {
                return response()->json([
                    'success' => false,
                    'message' => 'Username atau password salah.',
                ], 401);
            }

            $token = $pengguna->createToken('auth_token')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Login berhasil.',
                'data' => [
                    'pengguna' => $pengguna,
                    'token' => $token,
                ],
            ], 200);
        } catch (ValidationException $e) {
            return $this->validationError($e);
        } catch (Throwable $e) {
            return $this->serverError();
        }
    }

    public function me(Request $request)
    {
        return response()->json([
            'success' => true,
            'message' => 'Data pengguna berhasil diambil.',
            'data' => [
                'pengguna' => $request->user(),
            ],
        ], 200);
    }

    public function logout(Request $request)
    {
        $token = $request->user()->currentAccessToken();

        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => 'Token akses tidak ditemukan.',
            ], 401);
        }

        $token->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logout berhasil.',
        ], 200);
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
