@extends('layouts.admin')
@section('title', 'Form Layanan')

@php
  $edit = !request()->is('admin/layanan/tambah');
  $in = 'w-full bg-[#EDF0F5] text-[#2F5B8C] placeholder-[#4F8DB8] rounded-lg px-4 py-2.5 text-xs focus:outline-none focus:ring-2 focus:ring-brand-light';
  $chev = '<svg class="w-3 h-3 absolute right-3 top-1/2 -translate-y-1/2 text-brand-light pointer-events-none" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9l6 6 6-6"/></svg>';
@endphp

@section('heading')
  <h1 class="text-3xl text-brand">
    @if ($edit)
      <span class="font-semibold">Ubah Data</span> <span class="font-light">Layanan</span>
    @else
      <span class="font-semibold">Tambah</span> <span class="font-light">Layanan</span>
    @endif
  </h1>
@endsection

@section('content')
<a href="/admin/layanan" title="Kembali"
   class="w-7 h-7 rounded-full border border-brand-light text-brand flex items-center justify-center hover:bg-brand-soft">
  <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M15 6l-6 6 6 6"/></svg>
</a>

<form id="form" onsubmit="return false" novalidate class="mt-4 max-w-4xl">
  <div class="border border-brand-light/50 rounded-2xl p-6 md:p-8 grid md:grid-cols-2 gap-x-10 gap-y-5">

    <div>
      <label for="id_jenis" class="block text-xs font-semibold text-brand mb-2">Jenis Layanan</label>
      <div class="relative">
        <select id="id_jenis" class="{{ $in }} appearance-none pr-9"></select>
        {!! $chev !!}
      </div>
    </div>

    <div>
      <label for="tipe" class="block text-xs font-semibold text-brand mb-2">Tipe Laundry</label>
      <div class="relative">
        <select id="tipe" class="{{ $in }} appearance-none pr-9">
          <option value="Regular 3x24 jam">REGULAR 3X24 JAM</option>
          <option value="Express 1x24 jam">EXPRESS 1X24 JAM</option>
        </select>
        {!! $chev !!}
      </div>
    </div>

    <div>
      <label for="nama_layanan" class="block text-xs font-semibold text-brand mb-2">Nama Layanan</label>
      <input id="nama_layanan" autocomplete="off" placeholder="Masukkan nama layanan" class="{{ $in }}">
    </div>

    <div class="grid grid-cols-[1fr_7rem] gap-4">
      <div>
        <label for="harga" class="block text-xs font-semibold text-brand mb-2">Harga</label>
        <div class="flex items-center bg-[#EDF0F5] rounded-lg focus-within:ring-2 focus-within:ring-brand-light">
          <span class="px-3 text-xs text-brand-light border-r border-slate-300">Rp.</span>
          <input id="harga" type="number" min="0" step="any" placeholder="Harga layanan"
            class="w-full bg-transparent text-[#2F5B8C] placeholder-[#4F8DB8] px-3 py-2.5 text-xs focus:outline-none">
        </div>
      </div>
      <div>
        <label for="satuan" class="block text-xs font-semibold text-brand mb-2">Satuan</label>
        <div class="relative">
          <select id="satuan" class="{{ $in }} appearance-none pr-8">
            <option value="pcs">pcs</option>
            <option value="kg">kg</option>
          </select>
          {!! $chev !!}
        </div>
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
const cocok = location.pathname.match(/\/layanan\/(\d+)\/ubah/);
const editId = cocok ? cocok[1] : null;

// Kalau nilai dari database tidak ada di pilihan, tambahkan supaya tidak hilang
function setPilihan(sel, nilai) {
  if (nilai && ![...sel.options].some(o => o.value === nilai)) sel.add(new Option(nilai, nilai));
  sel.value = nilai;
}

(async () => {
  try {
    const j = await api('/jenis-layanan');
    $('id_jenis').innerHTML =
      '<option value="" disabled selected>Pilih Jenis Layanan</option>' +
      (j.data || []).map(x => `<option value="${x.id_jenis}">${esc(x.nama_jenis)}</option>`).join('');

    if (editId) {
      const res = await api('/layanan/' + editId);
      const x = res.data;
      $('id_jenis').value = x.id_jenis;
      setPilihan($('tipe'), x.tipe);
      $('nama_layanan').value = x.nama_layanan;
      $('harga').value = Number(x.harga);
      setPilihan($('satuan'), x.satuan);
    }
  } catch (e) {
    showError($('error'), e);
    if (editId) $('btn').disabled = true;
  }
})();

$('form').addEventListener('submit', async (e) => {
  e.preventDefault();
  hideError($('error'));

  const body = {
    id_jenis: $('id_jenis').value,
    tipe: $('tipe').value,
    nama_layanan: $('nama_layanan').value.trim(),
    harga: $('harga').value,
    satuan: $('satuan').value,
  };
  if (!body.id_jenis || !body.nama_layanan || body.harga === '') {
    showError($('error'), new Error('Jenis layanan, nama layanan, dan harga wajib diisi.'));
    return;
  }

  $('btn').disabled = true;
  try {
    await api(editId ? `/admin/layanan/${editId}` : '/admin/layanan', {
      method: editId ? 'PUT' : 'POST',
      body,
    });
    location.href = '/admin/layanan';
  } catch (ex) {
    showError($('error'), ex);
    $('btn').disabled = false;
  }
});
</script>
@endpush