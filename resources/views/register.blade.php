@extends('layouts.auth')
@section('title', 'Daftar - NAT Laundry')

@section('content')
<div class="bg-white rounded-2xl shadow-lg p-8">
  <p class="text-sm text-slate-500">Buat akun</p>
  <h1 class="text-2xl font-semibold text-brand">NAT Laundry</h1>

  <form id="form" onsubmit="return false" class="mt-6 space-y-4" novalidate>
    @php
      $fields = [
        ['nama_pengguna', 'Nama Lengkap', 'text', 'name'],
        ['no_telpon', 'Nomor Telepon', 'tel', 'tel'],
        ['username', 'Username', 'text', 'username'],
        ['alamat', 'Alamat', 'text', 'street-address'],
        ['password', 'Password (minimal 8 karakter)', 'password', 'new-password'],
        ['password_confirmation', 'Konfirmasi Password', 'password', 'new-password'],
      ];
    @endphp

    @foreach ($fields as [$id, $label, $type, $auto])
      <div>
        <label for="{{ $id }}" class="text-sm font-medium">{{ $label }}</label>
        <input id="{{ $id }}" name="{{ $id }}" type="{{ $type }}" autocomplete="{{ $auto }}" required
          class="mt-1 w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-light">
      </div>
    @endforeach

    <p id="error" class="hidden text-sm text-red-600 bg-red-50 rounded-lg px-3 py-2" role="alert"></p>
    <button id="btn" class="w-full bg-brand text-white text-sm py-2.5 rounded-lg hover:bg-brand-dark disabled:opacity-60">
      Daftar
    </button>
  </form>

  <p class="mt-5 text-sm text-center text-slate-500">
    Sudah punya akun? <a href="/login" class="text-brand font-medium hover:underline">Masuk</a>
  </p>
</div>
@endsection

@push('scripts')
<script>
const tujuan = (u) => (u?.role === 'admin' ? '/admin' : '/pesanan');
if (Auth.isLoggedIn()) location.href = tujuan(Auth.getUser());

const FIELD = ['nama_pengguna', 'no_telpon', 'username', 'alamat', 'password', 'password_confirmation'];

document.getElementById('form').addEventListener('submit', async (e) => {
  e.preventDefault();
  const btn = document.getElementById('btn');
  const err = document.getElementById('error');
  err.classList.add('hidden');

  const body = {};
  FIELD.forEach(f => body[f] = document.getElementById(f).value);

  if (FIELD.some(f => !String(body[f]).trim())) {
    err.textContent = 'Semua kolom wajib diisi.';
    err.classList.remove('hidden');
    return;
  }

  btn.disabled = true;
  btn.textContent = 'Memproses...';

  try {
    const res = await api('/auth/register', { method: 'POST', body });
    Auth.setSession(res.data.token, res.data.pengguna);
    location.href = tujuan(res.data.pengguna);
  } catch (ex) {
    err.textContent = ex.errors ? Object.values(ex.errors).flat().join(' ') : ex.message;
    err.classList.remove('hidden');
    btn.disabled = false;
    btn.textContent = 'Daftar';
  }
});
</script>
@endpush