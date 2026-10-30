@extends('layouts.admin')
@section('title', 'Ubah Data Pesanan')

@php
  $in = 'w-full bg-[#EDF0F5] text-[#2F5B8C] rounded-lg px-4 py-2.5 text-xs font-semibold uppercase focus:outline-none focus:ring-2 focus:ring-brand-light';
  $chev = '<svg class="w-3 h-3 absolute right-3 top-1/2 -translate-y-1/2 text-brand-light pointer-events-none" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9l6 6 6-6"/></svg>';
@endphp

@section('heading')
  <h1 class="text-3xl text-brand"><span class="font-semibold">Ubah Data</span> <span class="font-light">Pesanan</span></h1>
@endsection

@section('content')
<a href="/admin/pesanan" title="Kembali"
   class="w-7 h-7 rounded-full border border-brand-light text-brand flex items-center justify-center hover:bg-brand-soft">
  <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M15 6l-6 6 6 6"/></svg>
</a>

<form id="form" onsubmit="return false" novalidate class="mt-4 max-w-4xl">
  <div class="border border-brand-light/50 rounded-2xl p-6 md:p-8">

    <div>
      <p><span id="nama" class="text-sm font-bold text-brand">-</span>
         <span id="kode" class="ml-2 text-[11px] font-semibold text-[#E8913A]"></span></p>
      <p id="tgl" class="text-[11px] text-brand-light"></p>
    </div>

    <div class="mt-5 grid md:grid-cols-2 gap-x-10 gap-y-5">
      <div>
        <label for="status_pesanan" class="block text-xs font-semibold text-brand mb-2">Status Pesanan</label>
        <div class="relative">
          <select id="status_pesanan" class="{{ $in }} appearance-none pr-9">
            <option value="Diproses">Diproses</option>
            <option value="Selesai">Selesai</option>
          </select>
          {!! $chev !!}
        </div>
      </div>
      <div>
        <label for="status_bayar" class="block text-xs font-semibold text-brand mb-2">Status Pembayaran</label>
        <div class="relative">
          <select id="status_bayar" class="{{ $in }} appearance-none pr-9">
            <option value="Belum Lunas">Belum Lunas</option>
            <option value="Lunas">Lunas</option>
          </select>
          {!! $chev !!}
        </div>
      </div>
    </div>

    <h3 class="mt-8 text-xs font-semibold text-brand">LIST PESANAN LAUNDRY</h3>
    <div class="mt-3 rounded-t-xl overflow-x-auto">
      <table class="w-full min-w-[620px] text-xs text-brand">
        <thead class="bg-[#4F8DB8] text-white">
          <tr>
            <th class="py-2.5 px-3 font-medium w-10">No.</th>
            <th class="py-2.5 px-3 font-medium">Layanan</th>
            <th class="py-2.5 px-3 font-medium">Berat / Jumlah</th>
            <th class="py-2.5 px-3 font-medium">Harga</th>
            <th class="py-2.5 px-3 font-medium">Subtotal</th>
            <th class="py-2.5 px-3 font-medium w-14">Aksi</th>
          </tr>
        </thead>
        <tbody id="tbody" class="text-center"></tbody>
      </table>
    </div>

    <div class="mt-5 pt-4 border-t border-brand-light/30 flex flex-wrap items-end justify-between gap-4">
      <div>
        <p class="text-[11px] font-semibold text-brand">TOTAL BIAYA LAUNDRY</p>
        <p id="total" class="text-sm font-bold text-brand">Rp. 0</p>
      </div>
      <div class="flex items-center gap-3">
        <a href="/admin/pesanan" class="bg-brand-soft text-brand text-xs px-6 py-2.5 rounded-lg hover:bg-[#d3e0ef]">Batal</a>
        <button id="btn" class="bg-brand text-white text-xs px-6 py-2.5 rounded-lg hover:bg-brand-dark disabled:opacity-60">Simpan Perubahan</button>
      </div>
    </div>
  </div>

  <p id="error" class="hidden mt-4 text-sm text-red-600 bg-red-50 rounded-lg px-3 py-2" role="alert"></p>
</form>
@endsection

@push('scripts')
<script>
const $ = (id) => document.getElementById(id);
const cocok = location.pathname.match(/\/pesanan\/(\d+)\/ubah/);
const id = cocok ? cocok[1] : null;

let detail = [];
let jenisMap = {};
const dihapus = new Set(); // indeks baris yang ditandai hapus

const IKON_HAPUS = '<svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 7h16M10 11v6M14 11v6M6 7l1 13h10l1-13M9 7V4h6v3"/></svg>';

function isiSelect(sel, nilai) {
  if (nilai && ![...sel.options].some(o => o.value === nilai)) sel.add(new Option(nilai, nilai));
  sel.value = nilai;
}

function gambar() {
  let total = 0, no = 0;
  $('tbody').innerHTML = detail.map((d, i) => {
    if (dihapus.has(i)) return '';
    no++;
    total += Number(d.subtotal);
    const l = d.layanan || {};
    return `
    <tr class="border-b border-slate-100">
      <td class="py-3 px-3">${no}.</td>
      <td class="py-3 px-3 text-left">
        <div>${esc(jenisMap[l.id_jenis] || '')} (${esc(l.tipe || '')})</div>
        <div>${esc(l.nama_layanan || '-')}</div>
      </td>
      <td class="py-3 px-3">${angka(d.kuantitas)} ${esc(l.satuan || '')}</td>
      <td class="py-3 px-3 whitespace-nowrap">${rp(d.harga_satuan)}/${esc(l.satuan || '')}</td>
      <td class="py-3 px-3 whitespace-nowrap">${rp(d.subtotal)}</td>
      <td class="py-3 px-3">
        <button type="button" onclick="tandaiHapus(${i})" title="Hapus" class="w-6 h-6 rounded-full bg-brand text-white inline-flex items-center justify-center hover:bg-brand-dark">${IKON_HAPUS}</button>
      </td>
    </tr>`;
  }).join('') || rowsKosong(6, 'Tidak ada layanan pada pesanan ini.');
  $('total').textContent = rp(total);
}

function tandaiHapus(i) { dihapus.add(i); gambar(); }

(async () => {
  try {
    const [res, j] = await Promise.all([api('/admin/pesanan/' + id), api('/jenis-layanan')]);
    (j.data || []).forEach(x => { jenisMap[x.id_jenis] = x.nama_jenis; });
    const p = res.data;

    $('nama').textContent = (p.pengguna?.nama_pengguna || '-').toUpperCase();
    $('kode').textContent = kodePesanan(p.id_pesanan);
    $('tgl').textContent = 'Tanggal Pesan: ' + tglGaris(p.tgl_pesanan);
    isiSelect($('status_pesanan'), p.status_pesanan);
    isiSelect($('status_bayar'), p.status_pembayaran);

    detail = p.detail_pesanan || [];
    gambar();
  } catch (e) {
    showError($('error'), e);
    $('btn').disabled = true;
  }
})();

$('form').addEventListener('submit', async (e) => {
  e.preventDefault();
  hideError($('error'));
  $('btn').disabled = true;
  try {
    // Hapus layanan yang ditandai (total pesanan dihitung ulang otomatis oleh API)
    for (const i of dihapus) {
      const kid = idDetail(detail[i]);
      if (kid == null) throw new Error('ID rincian layanan tidak ditemukan.');
      await api('/admin/detail-pesanan/' + kid, { method: 'DELETE' });
    }
    await api('/admin/pesanan/' + id, { method: 'PUT', body: {
      status_pesanan: $('status_pesanan').value,
      status_pembayaran: $('status_bayar').value,
    } });
    location.href = '/admin/pesanan';
  } catch (ex) {
    showError($('error'), ex);
    $('btn').disabled = false;
  }
});
</script>
@endpush