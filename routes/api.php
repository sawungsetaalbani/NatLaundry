
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LayananController;
use App\Http\Controllers\JenisLayananController;
use App\Http\Controllers\PenggunaController;
use App\Http\Controllers\PesananController;
use App\Http\Controllers\PelangganController;
use App\Http\Controllers\DetailPesananController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LaporanController;


Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/me', [AuthController::class, 'me']);
        Route::post('/logout', [AuthController::class, 'logout']);
    });
});

Route::get('/layanan', [LayananController::class, 'index']);
Route::get('/layanan/{id}', [LayananController::class, 'show']);
Route::get('/jenis-layanan', [JenisLayananController::class, 'index']);

Route::middleware(['auth:sanctum', 'role:pelanggan'])
    ->prefix('pelanggan')
    ->group(function () {
        Route::get('/pesanan', [PelangganController::class, 'index']);
        Route::get('/pesanan/{id}', [PelangganController::class, 'show']);
        Route::get('/riwayat', [PelangganController::class, 'riwayat']);
        Route::get('/pesanan/{id}/export-pdf', [PelangganController::class, 'exportPdf']);
        Route::get('/riwayat/export-pdf', [PelangganController::class, 'exportRiwayatPdf']);
    });

Route::middleware(['auth:sanctum', 'role:admin'])
    ->prefix('admin')
    ->group(function () {
        Route::apiResource('pengguna', PenggunaController::class)
            ->except(['create', 'edit']);

        Route::apiResource('jenis-layanan', JenisLayananController::class)
            ->except(['create', 'edit']);

        Route::apiResource('layanan', LayananController::class)
            ->except(['create', 'edit']);

        Route::apiResource('pesanan', PesananController::class)
            ->except(['create', 'edit']);

        Route::apiResource('detail-pesanan', DetailPesananController::class)
            ->except(['create', 'edit']);
        
        Route::get('/dashboard', [DashboardController::class, 'index']);
        Route::get('/dashboard/grafik-penjualan', [DashboardController::class, 'grafikPenjualan']);
        Route::get('/dashboard/pesanan', [DashboardController::class, 'pesananDiproses']);

        Route::get('/laporan', [LaporanController::class, 'index']);       
        Route::get('/laporan/export-pdf', [LaporanController::class, 'exportPdf']);
});