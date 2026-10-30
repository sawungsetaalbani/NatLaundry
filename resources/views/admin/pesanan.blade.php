@extends('layouts.admin')
@section('title', 'Data Pesanan')

@section('heading')
  <h1 class="text-3xl text-brand"><span class="font-semibold">Data</span> <span class="font-light">Pesanan</span></h1>
@endsection

@section('content')
<div class="flex flex-wrap items-center justify-between gap-3">
  <a href="/admin/pesanan/tambah" class="flex items-center gap-2 bg-brand text-white text-xs px-5 py-2.5 rounded-lg hover:bg-brand-dark">
    <span class="text-base leading-none">+</span> Tambah Pesanan
  </a>

  <div class="flex items-center gap-3 border border-brand-light rounded-full px-5 py-2 w-full sm:w-96">
    <svg class="w-4 h-4 text-brand-light shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="11" cy="11" r="6"/><path d="M20 20l-4-4"/></svg>
    <input id="cari" type="search" placeholder="Cari berdasarkan nama atau kode pesanan"
      class="w-full text-xs text-brand placeholder-brand-light bg-transparent focus:outline-none">
  </div>
</div>

<div class="mt-5 rounded-t-2xl overflow-x-auto">
  <table class="w-full min-w-[900px] text-xs text-brand">
    <thead class="bg-[#4F8DB8] text-white">
      <tr>
        <th class="py-3 px-3 font-medium w-12">No.</th>
        <th class="py-3 px-3 font-medium w-32">Aksi</th>
        <th class="py-3 px-3 font-medium">Kode Pesanan</th>
        <th class="py-3 px-3 font-medium">Pelanggan</th>
        <th class="py-3 px-3 font-medium">Layanan</th>
        <th class="py-3 px-3 font-medium">Berat / Jumlah</th>
        <th class="py-3 px-3 font-medium">Total</th>
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
const IKON_WA = '<svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 20l1.3-4A8 8 0 1 1 8 18.7z"/></svg>';

// Tautan WhatsApp ke pelanggan berisi ringkasan pesanan
function linkWa(x) {
  let no = String(x.pengguna?.no_telpon || '').replace(/\D/g, '');
  if (no.startsWith('0')) no = '62' + no.slice(1);
  else if (!no.startsWith('62')) no = '62' + no;
  const teks = `Halo ${x.pengguna?.nama_pengguna || ''}, pesanan laundry ${kodePesanan(x.id_pesanan)} di NAT Laundry berstatus ${x.status_pesanan}. ` +
               `Total biaya ${rp(x.total_harga)}. Terima kasih.`;
  return 'https://wa.me/' + no + '?text=' + encodeURIComponent(teks);
}

function gambar() {
  const kata = $('cari').value.trim().toLowerCase();
  const hasil = daftar.filter(x =>
    (x.pengguna?.nama_pengguna || '').toLowerCase().includes(kata) ||
    kodePesanan(x.id_pesanan).toLowerCase().includes(kata));

  $('tbody').innerHTML = hasil.map((x, i) => {
    const dts = x.detail_pesanan || [];
    const warna = x.status_pesanan === 'Selesai' ? 'text-green-600' : 'text-[#E8913A]';
    return `
    <tr class="border-b border-slate-100 align-middle">
      <td class="py-3.5 px-3">${i + 1}.</td>
      <td class="py-3.5 px-3">
        <div class="flex items-center justify-center gap-2">
          <a href="/admin/pesanan/${x.id_pesanan}/ubah" title="Ubah" class="w-7 h-7 rounded-full bg-brand text-white flex items-center justify-center hover:bg-brand-dark">${IKON_EDIT}</a>
          <button onclick="hapus(${x.id_pesanan})" title="Hapus" class="w-7 h-7 rounded-full bg-brand text-white flex items-center justify-center hover:bg-brand-dark">${IKON_HAPUS}</button>
          <a href="${linkWa(x)}" target="_blank" rel="noopener" title="Kirim WhatsApp" class="w-7 h-7 rounded-full bg-[#4CAF6A] text-white flex items-center justify-center hover:opacity-90">${IKON_WA}</a>
        </div>
      </td>
      <td class="py-3.5 px-3 font-medium">${kodePesanan(x.id_pesanan)}</td>
      <td class="py-3.5 px-3">
        <div class="font-medium">${esc(x.pengguna?.nama_pengguna || '-')}</div>
        <div class="text-[10px] text-brand-light">${tglGaris(x.tgl_pesanan)}</div>
      </td>
      <td class="py-3.5 px-3">
        ${dts.map(d => `<div>${esc(d.layanan?.nama_layanan || '-')} - ${esc(d.layanan?.tipe || '')}</div>`).join('') || '<div class="text-slate-400">-</div>'}
        <div class="text-[10px] font-semibold ${warna}">${esc(x.status_pesanan).toUpperCase()} / ${esc(x.status_pembayaran).toUpperCase()}</div>
      </td>
      <td class="py-3.5 px-3">
        ${dts.map(d => `<div>${angka(d.kuantitas)} ${esc(d.layanan?.satuan || '')}</div>`).join('') || '-'}
      </td>
      <td class="py-3.5 px-3 whitespace-nowrap">${rp(x.total_harga)}</td>
    </tr>`;
  }).join('') || rowsKosong(7, 'Belum ada pesanan.');
}

async function muat() {
  try {
    const res = await api('/admin/pesanan');
    daftar = (res.data || []).sort((a, b) => b.id_pesanan - a.id_pesanan); // terbaru di atas
    gambar();
  } catch (e) { $('tbody').innerHTML = rowsKosong(7, esc(e.message)); }
}

async function hapus(id) {
  const x = daftar.find(d => d.id_pesanan === id);
  if (!confirm('Hapus pesanan ' + kodePesanan(id) + '?')) return;
  try {
    // Hapus rincian layanannya dulu, baru pesanannya
    for (const d of (x?.detail_pesanan || [])) {
      await api('/admin/detail-pesanan/' + idDetail(d), { method: 'DELETE' });
    }
    await api('/admin/pesanan/' + id, { method: 'DELETE' });
  } catch (e) { alert(e.message); }
  muat();
}

$('cari').addEventListener('input', gambar);
muat();
</script>
@endpush