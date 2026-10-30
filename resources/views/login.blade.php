@extends('layouts.auth')
@section('title', 'Login - NAT Laundry')

@section('content')
<div class="bg-white rounded-2xl shadow-lg p-8">
  <p class="text-sm text-slate-500">Selamat datang di</p>
  <h1 class="text-2xl font-semibold text-brand">NAT Laundry</h1>

  <form id="form" onsubmit="return false" class="mt-6 space-y-4" novalidate>
    <div>
      <label for="username" class="text-sm font-medium">Username</label>
      <input id="username" name="username" autocomplete="username" required
        class="mt-1 w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-light">
    </div>
    <div>
      <label for="password" class="text-sm font-medium">Password</label>
      <input id="password" name="password" type="password" autocomplete="current-password" required
        class="mt-1 w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-light">
    </div>
    <p id="error" class="hidden text-sm text-red-600 bg-red-50 rounded-lg px-3 py-2" role="alert"></p>
    <button id="btn" class="w-full bg-brand text-white text-sm py-2.5 rounded-lg hover:bg-brand-dark disabled:opacity-60">
      Masuk
    </button>
  </form>

  <p class="mt-5 text-sm text-center text-slate-500">
    Belum punya akun? <a href="/register" class="text-brand font-medium hover:underline">Daftar sekarang</a>
  </p>
</div>
@endsection

@push('scripts')
<script>
const tujuan = (u) => (u?.role === 'admin' ? '/admin' : '/pesanan');
if (Auth.isLoggedIn()) location.href = tujuan(Auth.getUser());

document.getElementById('form').addEventListener('submit', async (e) => {
  e.preventDefault();
  const btn = document.getElementById('btn');
  const err = document.getElementById('error');
  err.classList.add('hidden');

  const username = document.getElementById('username').value.trim();
  const password = document.getElementById('password').value;
  if (!username || !password) {
    err.textContent = 'Username dan password wajib diisi.';
    err.classList.remove('hidden');
    return;
  }

  btn.disabled = true;
  btn.textContent = 'Memproses...';

  try {
    const res = await api('/auth/login', { method: 'POST', body: { username, password } });
    const { pengguna, token } = res.data;
    Auth.setSession(token, pengguna);
    location.href = tujuan(pengguna);
  } catch (ex) {
    err.textContent = ex.errors ? Object.values(ex.errors).flat().join(' ') : ex.message;
    err.classList.remove('hidden');
    btn.disabled = false;
    btn.textContent = 'Masuk';
  }
});
</script>
@endpush