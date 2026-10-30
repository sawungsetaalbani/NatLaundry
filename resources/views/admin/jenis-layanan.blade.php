@extends('layouts.admin')
@section('title', 'Jenis Layanan')

@section('heading')
  <h1 class="text-3xl text-brand"><span class="font-semibold">Jenis</span> <span class="font-light">Layanan</span></h1>
@endsection

@section('content')
<div class="grid lg:grid-cols-[minmax(0,26rem)_minmax(0,1fr)] gap-6 items-start">

  {{-- Form tambah / ubah --}}
  <form id="form" onsubmit="return false" class="bg-[#4F8DB8] text-white rounded-2xl p-6" novalidate>
    <h2 id="judul-form" class="text-center font-semibold">Tambah Layanan</h2>

    <label for="nama_jenis" class="block text-sm mt-4">Jenis Layanan</label>
    <input id="nama_jenis" autocomplete="off" placeholder="Masukkan jenis layanan laundry"
      class="mt-2 w-full bg-[#DCE3EC] text-[#2F5B8C] placeholder-[#4F8DB8] rounded-xl px-4 py-3 text-xs focus:outline-none focus:ring-2 focus:ring-white/60">

    <p id="error" class="hidden mt-3 text-xs bg-white/90 text-red-600 rounded-lg px-3 py-2" role="alert"></p>

    <div class="mt-6 flex items-center justify-end gap-3">
      <button id="batal" type="button" class="hidden text-xs underline">Batal</button>
      <button id="btn" class="flex items-center gap-2 bg-brand text-white text-xs px-5 py-2.5 rounded-xl hover:bg-brand-dark disabled:opacity-60">
        <span class="text-base leading-none">+</span><span id="teks-btn">Simpan Data</span>
      </button>
    </div>
  </form>

  {{-- Pencarian + tabel --}}
  <div>
    <div class="flex items-center gap-3 border border-brand-light rounded-full px-5 py-2.5">
      <svg class="w-4 h-4 text-brand-light shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="11" cy="11" r="6"/><path d="M20 20l-4-4"/></svg>
      <input id="cari" type="search" placeholder="Cari jenis layanan disini"
        class="w-full text-xs text-brand placeholder-brand-light bg-transparent focus:outline-none">
    </div>

    <div class="mt-5 rounded-t-2xl overflow-hidden">
      <table class="w-full text-xs text-brand">
        <thead class="bg-[#4F8DB8] text-white">
          <tr>
            <th class="py-3 px-4 font-medium w-16">No.</th>
            <th class="py-3 px-4 font-medium w-32">Aksi</th>
            <th class="py-3 px-4 font-medium">Jenis Layanan</th>
          </tr>
        </thead>
        <tbody id="tbody" class="text-center"></tbody>
      </table>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
const $ = (id) => document.getElementById(id);
let daftar = [];
let editId = null;

const IKON_EDIT = '<svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 20h4L19 9l-4-4L4 16z"/></svg>';
const IKON_HAPUS = '<svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 7h16M10 11v6M14 11v6M6 7l1 13h10l1-13M9 7V4h6v3"/></svg>';

function gambar() {
  const kata = $('cari').value.trim().toLowerCase();
  const hasil = daftar.filter(x => x.nama_jenis.toLowerCase().includes(kata));
  $('tbody').innerHTML = hasil.map((x, i) => `
    <tr>
      <td class="py-4 px-4">${i + 1}.</td>
      <td class="py-4 px-4">
        <div class="flex items-center justify-center gap-2">
          <button onclick="ubah(${x.id_jenis})" title="Ubah" class="w-7 h-7 rounded-full bg-brand text-white flex items-center justify-center hover:bg-brand-dark">${IKON_EDIT}</button>
          <button onclick="hapus(${x.id_jenis})" title="Hapus" class="w-7 h-7 rounded-full bg-brand text-white flex items-center justify-center hover:bg-brand-dark">${IKON_HAPUS}</button>
        </div>
      </td>
      <td class="py-4 px-4">${esc(x.nama_jenis)}</td>
    </tr>`).join('') || rowsKosong(3, 'Belum ada jenis layanan.');
}

async function muat() {
  try {
    const res = await api('/jenis-layanan');
    daftar = res.data || [];
    gambar();
  } catch (e) { $('tbody').innerHTML = rowsKosong(3, esc(e.message)); }
}

function resetForm() {
  editId = null;
  $('nama_jenis').value = '';
  $('judul-form').textContent = 'Tambah Layanan';
  $('teks-btn').textContent = 'Simpan Data';
  $('batal').classList.add('hidden');
  hideError($('error'));
}

function ubah(id) {
  const x = daftar.find(d => d.id_jenis === id);
  if (!x) return;
  editId = id;
  $('nama_jenis').value = x.nama_jenis;
  $('judul-form').textContent = 'Ubah Jenis Layanan';
  $('teks-btn').textContent = 'Simpan Perubahan';
  $('batal').classList.remove('hidden');
  hideError($('error'));
  $('nama_jenis').focus();
}

$('batal').onclick = resetForm;
$('cari').addEventListener('input', gambar);

$('form').addEventListener('submit', async (e) => {
  e.preventDefault();
  hideError($('error'));
  const nama = $('nama_jenis').value.trim();
  if (!nama) { showError($('error'), new Error('Jenis layanan wajib diisi.')); return; }

  $('btn').disabled = true;
  try {
    await api(editId ? `/admin/jenis-layanan/${editId}` : '/admin/jenis-layanan', {
      method: editId ? 'PUT' : 'POST',
      body: { nama_jenis: nama },
    });
    resetForm();
    muat();
  } catch (ex) { showError($('error'), ex); }
  $('btn').disabled = false;
});

async function hapus(id) {
  if (!confirm('Hapus jenis layanan ini?')) return;
  try {
    await api(`/admin/jenis-layanan/${id}`, { method: 'DELETE' });
    if (editId === id) resetForm();
    muat();
  } catch (e) { alert(e.message); }
}

muat();
</script>
@endpush