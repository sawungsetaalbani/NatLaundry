@extends('layouts.admin')
@section('title', 'Form Pelanggan')

@php
  $edit = !request()->is('admin/pengguna/tambah');
  $in = 'w-full bg-[#EDF0F5] text-[#2F5B8C] placeholder-[#4F8DB8] rounded-lg px-4 py-2.5 text-xs focus:outline-none focus:ring-2 focus:ring-brand-light';
@endphp

@section('heading')
  <h1 class="text-3xl text-brand">
    @if ($edit)
      <span class="font-semibold">Ubah Data</span> <span class="font-light">Pelanggan</span>
    @else
      <span class="font-semibold">Tambah</span> <span class="font-light">Pelanggan</span>
    @endif
  </h1>
@endsection

@section('content')
<a href="/admin/pengguna" title="Kembali"
   class="w-7 h-7 rounded-full border border-brand-light text-brand flex items-center justify-center hover:bg-brand-soft">
  <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M15 6l-6 6 6 6"/></svg>
</a>

<form id="form" onsubmit="return false" novalidate class="mt-4 max-w-4xl">
  <div class="border border-brand-light/50 rounded-2xl p-6 md:p-8 grid md:grid-cols-2 gap-x-10 gap-y-5">

    <div>
      <label for="nama_pengguna" class="block text-xs font-semibold text-brand mb-2">Nama Pelanggan</label>
      <input id="nama_pengguna" autocomplete="off" placeholder="Masukkan nama pelanggan" class="{{ $in }}">
    </div>

    <div>
      <label for="no_telpon" class="block text-xs font-semibold text-brand mb-2">Nomor Telepon</label>
      <div class="flex items-center bg-[#EDF0F5] rounded-lg focus-within:ring-2 focus-within:ring-brand-light">
        <span class="px-3 text-xs font-semibold text-brand border-r border-slate-300">+62</span>
        <input id="no_telpon" type="tel" inputmode="numeric" autocomplete="off" placeholder="Nomor telepon"
          class="w-full bg-transparent text-[#2F5B8C] placeholder-[#4F8DB8] px-3 py-2.5 text-xs focus:outline-none">
      </div>
    </div>

    <div>
      <label for="username" class="block text-xs font-semibold text-brand mb-2">Username</label>
      <input id="username" autocomplete="off" placeholder="Masukkan username" class="{{ $in }}">
    </div>

    <div class="md:row-span-2">
      <label for="alamat" class="block text-xs font-semibold text-brand mb-2">Alamat</label>
      <textarea id="alamat" rows="5" placeholder="Masukkan alamat pelanggan" class="{{ $in }} h-[calc(100%-1.75rem)] min-h-[7rem] resize-none"></textarea>
    </div>

    <div>
      <label for="password" class="block text-xs font-semibold text-brand mb-2">Password</label>
      <div class="relative">
        <input id="password" type="password" autocomplete="new-password"
          placeholder="{{ $edit ? 'Kosongkan jika tidak diganti' : 'Masukkan password (min. 8 karakter)' }}"
          class="{{ $in }} pr-10">
        <button type="button" id="mata" title="Tampilkan password" class="absolute right-3 top-1/2 -translate-y-1/2 text-brand-light hover:text-brand">
          <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s4-7 10-7 10 7 10 7-4 7-10 7S2 12 2 12z"/><circle cx="12" cy="12" r="3"/></svg>
        </button>
      </div>
    </div>
  </div>

  <p id="error" class="hidden mt-4 text-sm text-red-600 bg-red-50 rounded-lg px-3 py-2" role="alert"></p>

  <div class="mt-5 flex justify-end">
    <button id="btn" class="bg-brand text-white text-xs px-6 py-2.5 rounded-lg hover:bg-brand-dark disabled:opacity-60">
      {{ $edit ? 'Ubah Data' : 'Simpan Data' }}
    </button>
  </div>
</form>
@endsection

@push('scripts')
<script>
const $ = (id) => document.getElementById(id);
const cocok = location.pathname.match(/\/pengguna\/(\d+)\/ubah/);
const editId = cocok ? cocok[1] : null;

// Tampilkan / sembunyikan password
$('mata').addEventListener('click', () => {
  $('password').type = $('password').type === 'password' ? 'text' : 'password';
});

// Nomor telepon: buang +62 / 62 / 0 di depan, sisakan angkanya saja
const bersihkanNomor = (s) => String(s || '').replace(/\D/g, '').replace(/^62/, '').replace(/^0+/, '');

(async () => {
  if (!editId) return;
  try {
    const x = await api('/admin/pengguna/' + editId);
    $('nama_pengguna').value = x.nama_pengguna;
    $('no_telpon').value = bersihkanNomor(x.no_telpon);
    $('username').value = x.username;
    $('alamat').value = x.alamat;
  } catch (e) {
    showError($('error'), e);
    $('btn').disabled = true;
  }
})();

$('form').addEventListener('submit', async (e) => {
  e.preventDefault();
  hideError($('error'));

  const nomor = bersihkanNomor($('no_telpon').value);
  const body = {
    nama_pengguna: $('nama_pengguna').value.trim(),
    username: $('username').value.trim(),
    no_telpon: nomor ? '+62' + nomor : '',
    alamat: $('alamat').value.trim(),
  };
  const password = $('password').value;

  if (!body.nama_pengguna || !body.username || !body.no_telpon || !body.alamat) {
    showError($('error'), new Error('Nama, nomor telepon, username, dan alamat wajib diisi.'));
    return;
  }
  if (!editId && !password) {
    showError($('error'), new Error('Password wajib diisi.'));
    return;
  }
  if (password) {
    if (password.length < 8) { showError($('error'), new Error('Password minimal 8 karakter.')); return; }
    body.password = password;
  }
  if (!editId) body.role = 'pelanggan';

  $('btn').disabled = true;
  try {
    await api(editId ? `/admin/pengguna/${editId}` : '/admin/pengguna', {
      method: editId ? 'PUT' : 'POST',
      body,
    });
    location.href = '/admin/pengguna';
  } catch (ex) {
    showError($('error'), ex);
    $('btn').disabled = false;
  }
});
</script>
@endpush