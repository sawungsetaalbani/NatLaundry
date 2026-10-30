<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'beranda')->name('beranda');
Route::view('/layanan', 'layanan')->name('layanan');
Route::view('/login', 'login')->name('login');
Route::view('/register', 'register')->name('register');
Route::view('/pesanan', 'pesanan')->name('pesanan');
Route::view('/riwayat', 'riwayat')->name('riwayat');

Route::prefix('admin')->group(function () {
    Route::view('/', 'admin.dashboard');
    Route::view('/pesanan', 'admin.pesanan');
    Route::view('/pesanan/tambah', 'admin.pesanan-tambah');
    Route::view('/pesanan/{id}/ubah', 'admin.pesanan-ubah');
    Route::view('/pengguna', 'admin.pengguna');
    Route::view('/pengguna/tambah', 'admin.pengguna-form');
    Route::view('/pengguna/{id}/ubah', 'admin.pengguna-form');
    Route::view('/layanan', 'admin.layanan');
    Route::view('/layanan/tambah', 'admin.layanan-form');
    Route::view('/layanan/{id}/ubah', 'admin.layanan-form');
    Route::view('/jenis-layanan', 'admin.jenis-layanan');
    Route::view('/laporan', 'admin.laporan');
});