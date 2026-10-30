@extends('layouts.admin')
@section('title', 'Data Pelanggan')

@section('heading')
  <h1 class="text-3xl text-brand"><span class="font-semibold">Data</span> <span class="font-light">Pelanggan</span></h1>
@endsection

@section('content')
<div class="flex flex-wrap items-center justify-between gap-3">
  <a href="/admin/pengguna/tambah" class="flex items-center gap-2 bg-brand text-white text-xs px-5 py-2.5 rounded-lg hover:bg-brand-dark">
    <span class="text-base leading-none">+</span> Tambah Pelanggan
  </a>

  <div class="flex items-center gap-3 border border-brand-light rounded-full px-5 py-2 w-full sm:w-80">
    <svg class="w-4 h-4 text-brand-light shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="11" cy="11" r="6"/><path d="M20 20l-4-4"/></svg>
    <input id="cari" type="search" placeholder="Cari data pelanggan disini"
      class="w-full text-xs text-brand placeholder-brand-light bg-transparent focus:outline-none">
  </div>
</div>

<div class="mt-5 rounded-t-2xl overflow-x-auto">
  <table class="w-full min-w-[760px] text-xs text-brand">
    <thead class="bg-[#4F8DB8] text-white">
      <tr>
        <th class="py-3 px-3 font-medium w-12">No.</th>
        <th class="py-3 px-3 font-medium w-24">Aksi</th>
        <th class="py-3 px-3 font-medium">Nama Pelanggan</th>
        <th class="py-3 px-3 font-medium">Alamat</th>
        <th class="py-3 px-3 font-medium">No. Telp</th>
        <th class="py-3 px-3 font-medium">Username</th>
      </tr>
    </thead>
    <tbody id="tbody" class="text-center"></tbody>
  </table>
</div>
@endsection

@push('scripts')
<script>
const $ = (id) => document.getElementById(id);
let daftar = [];

const IKON_EDIT = '<svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 20h4L19 9l-4-4L4 16z"/></svg>';
const IKON_HAPUS = '<svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 7h16M10 11v6M14 11v6M6 7l1 13h10l1-13M9 7V4h6v3"/></svg>';

function gambar() {
  const kata = $('cari').value.trim().toLowerCase();
  const hasil = daftar.filter(x =>
    [x.nama_pengguna, x.alamat, x.no_telpon, x.username].join(' ').toLowerCase().includes(kata));

  $('tbody').innerHTML = hasil.map((x, i) => `
    <tr class="border-b border-slate-100">
      <td class="py-3.5 px-3">${i + 1}.</td>
      <td class="py-3.5 px-3">
        <div class="flex items-center justify-center gap-2">
          <a href="/admin/pengguna/${x.id_pengguna}/ubah" title="Ubah" class="w-7 h-7 rounded-full bg-brand text-white flex items-center justify-center hover:bg-brand-dark">${IKON_EDIT}</a>
          <button onclick="hapus(${x.id_pengguna})" title="Hapus" class="w-7 h-7 rounded-full bg-brand text-white flex items-center justify-center hover:bg-brand-dark">${IKON_HAPUS}</button>
        </div>
      </td>
      <td class="py-3.5 px-3">${esc(x.nama_pengguna)}</td>
      <td class="py-3.5 px-3 max-w-[16rem]">${esc(x.alamat)}</td>
      <td class="py-3.5 px-3 whitespace-nowrap">${esc(x.no_telpon)}</td>
      <td class="py-3.5 px-3">${esc(x.username)}</td>
    </tr>`).join('') || rowsKosong(6, 'Belum ada pelanggan.');
}

async function muat() {
  try {
    const semua = await api('/admin/pengguna');
    daftar = semua.filter(u => u.role === 'pelanggan');
    gambar();
  } catch (e) { $('tbody').innerHTML = rowsKosong(6, esc(e.message)); }
}

async function hapus(id) {
  if (!confirm('Hapus pelanggan ini?')) return;
  try { await api(`/admin/pengguna/${id}`, { method: 'DELETE' }); muat(); }
  catch (e) { alert(e.message); }
}

$('cari').addEventListener('input', gambar);
muat();
</script>
@endpush