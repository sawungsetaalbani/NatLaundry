@extends('layouts.admin')
@section('title', 'Laporan')

@php $in = 'border border-slate-300 rounded-lg px-3 py-2 text-sm bg-white'; @endphp

@section('content')
<h1 class="text-xl font-semibold text-brand">Laporan Transaksi</h1>

<form id="filter" class="mt-4 flex flex-wrap items-end gap-3 text-sm">
  <div><label class="block text-xs text-slate-500">Dari tanggal</label><input id="f-mulai" type="date" class="{{ $in }}"></div>
  <div><label class="block text-xs text-slate-500">Sampai tanggal</label><input id="f-akhir" type="date" class="{{ $in }}"></div>
  <div><label class="block text-xs text-slate-500">Pembayaran</label>
    <select id="f-bayar" class="{{ $in }}"><option value="">Semua</option><option>Lunas</option><option>Belum Lunas</option></select></div>
  <button class="bg-brand text-white px-4 py-2 rounded-lg hover:bg-brand-dark">Terapkan</button>
  <button type="button" id="btn-pdf" class="border border-brand text-brand px-4 py-2 rounded-lg hover:bg-brand-soft">Export PDF</button>
</form>

<div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4 mt-6">
  <div class="bg-white border border-slate-200 rounded-xl p-5"><p class="text-xs text-slate-500">Jumlah Pesanan</p><p id="r-jumlah" class="text-2xl font-semibold text-brand mt-1">-</p></div>
  <div class="bg-white border border-slate-200 rounded-xl p-5"><p class="text-xs text-slate-500">Total Pendapatan</p><p id="r-total" class="text-2xl font-semibold text-brand mt-1">-</p></div>
  <div class="bg-white border border-slate-200 rounded-xl p-5"><p class="text-xs text-slate-500">Lunas</p><p id="r-lunas" class="text-2xl font-semibold text-brand mt-1">-</p></div>
  <div class="bg-white border border-slate-200 rounded-xl p-5"><p class="text-xs text-slate-500">Belum Lunas</p><p id="r-belum" class="text-2xl font-semibold text-brand mt-1">-</p></div>
</div>

<div class="bg-white border border-slate-200 rounded-xl mt-6 overflow-hidden">
  <div class="overflow-x-auto">
    <table class="w-full text-sm">
      <thead class="bg-slate-50 text-left text-slate-500">
        <tr>
          <th class="px-4 py-2">ID</th><th class="px-4 py-2">Pelanggan</th><th class="px-4 py-2">Tanggal</th>
          <th class="px-4 py-2">Status</th><th class="px-4 py-2">Total</th><th class="px-4 py-2">Pembayaran</th>
        </tr>
      </thead>
      <tbody id="tbody"></tbody>
    </table>
  </div>
  <div class="flex items-center justify-between px-5 py-3 text-sm">
    <span id="info-halaman" class="text-slate-500"></span>
    <div class="flex gap-2">
      <button id="prev" class="px-3 py-1 border rounded-lg disabled:opacity-40">Sebelumnya</button>
      <button id="next" class="px-3 py-1 border rounded-lg disabled:opacity-40">Berikutnya</button>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
const $ = (id) => document.getElementById(id);
let halaman = 1;

function filterQuery(denganHalaman) {
  const q = new URLSearchParams();
  if ($('f-mulai').value) q.set('tanggal_mulai', $('f-mulai').value);
  if ($('f-akhir').value) q.set('tanggal_akhir', $('f-akhir').value);
  if ($('f-bayar').value) q.set('status_pembayaran', $('f-bayar').value);
  if (denganHalaman) q.set('page', halaman);
  const s = q.toString();
  return s ? '?' + s : '';
}

async function muat() {
  try {
    const res = await api('/admin/laporan' + filterQuery(true));
    const r = res.ringkasan, p = res.data;
    $('r-jumlah').textContent = r.jumlah_pesanan;
    $('r-total').textContent = rupiah(r.total_pendapatan);
    $('r-lunas').textContent = r.jumlah_lunas;
    $('r-belum').textContent = r.jumlah_belum_lunas;
    $('tbody').innerHTML = p.data.map(x => `
      <tr class="border-t border-slate-100">
        <td class="px-4 py-2">#${esc(x.id_pesanan)}</td>
        <td class="px-4 py-2">${esc(x.pengguna?.nama_pengguna || '-')}</td>
        <td class="px-4 py-2">${tgl(x.tgl_pesanan)}</td>
        <td class="px-4 py-2">${badge(x.status_pesanan)}</td>
        <td class="px-4 py-2">${rupiah(x.total_harga)}</td>
        <td class="px-4 py-2">${badge(x.status_pembayaran)}</td>
      </tr>`).join('') || rowsKosong(6, 'Tidak ada transaksi.');
    $('info-halaman').textContent = `Halaman ${p.current_page} dari ${p.last_page || 1}`;
    $('prev').disabled = p.current_page <= 1;
    $('next').disabled = p.current_page >= p.last_page;
  } catch (e) { $('tbody').innerHTML = rowsKosong(6, esc(e.message)); }
}

$('filter').addEventListener('submit', (e) => { e.preventDefault(); halaman = 1; muat(); });
$('prev').onclick = () => { halaman--; muat(); };
$('next').onclick = () => { halaman++; muat(); };

$('btn-pdf').onclick = async () => {
  $('btn-pdf').disabled = true;
  try { await downloadFile('/admin/laporan/export-pdf' + filterQuery(false), 'laporan-transaksi.pdf'); }
  catch (e) { alert(e.message); }
  $('btn-pdf').disabled = false;
};

muat();
</script>
@endpush