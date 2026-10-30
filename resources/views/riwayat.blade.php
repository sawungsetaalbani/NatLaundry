@extends('layouts.app')
@section('title', 'Riwayat Pesanan - NAT Laundry')

@php $in = 'border border-slate-300 rounded-md px-3 py-1.5 text-xs bg-white focus:outline-none focus:border-brand-light'; @endphp

@section('content')
<section class="max-w-6xl mx-auto px-6 py-10">
  <div class="flex items-center gap-3">
    <a href="/pesanan" aria-label="Kembali" class="w-8 h-8 rounded-full border border-brand text-brand flex items-center justify-center hover:bg-brand-soft">&larr;</a>
    <h1 class="text-3xl font-light text-brand">Riwayat Pesanan</h1>
  </div>

  <div class="mt-6 flex items-center gap-4">
    <div class="relative flex-1">
      <span class="absolute left-4 top-1/2 -translate-y-1/2 text-brand-light text-xs">&#128269;</span>
      <input id="cari" type="search" placeholder="Cari data riwayat pesanan disini"
        class="w-full border border-brand-light rounded-full pl-10 pr-4 py-2 text-xs focus:outline-none">
    </div>
    <button id="btn-pdf" class="bg-brand-light text-white text-xs px-6 py-2 rounded-lg hover:opacity-90">Export PDF</button>
  </div>

  <div class="mt-5">
    <label class="block text-[11px] text-brand-light">Filter berdasarkan bulan</label>
    <select id="bulan" class="{{ $in }} mt-1 min-w-[150px]"></select>
  </div>

  <div class="mt-4 flex flex-wrap items-center justify-between gap-3 text-xs">
    <span id="total" class="font-semibold text-brand uppercase">Total data : 0 data</span>
    <div class="flex items-center gap-4 text-brand">
      <span id="info-halaman"></span>
      <label class="flex items-center gap-2">Item per halaman
        <select id="per-halaman" class="{{ $in }}">
          <option>5</option><option>10</option><option>20</option>
        </select>
      </label>
      <button id="prev" aria-label="Sebelumnya" class="w-7 h-7 rounded-full border border-brand disabled:opacity-40">&lsaquo;</button>
      <button id="next" aria-label="Berikutnya" class="w-7 h-7 rounded-full border border-brand disabled:opacity-40">&rsaquo;</button>
    </div>
  </div>

  <div id="daftar" class="mt-5 grid md:grid-cols-2 gap-6">
    <p class="text-sm text-slate-400">Memuat riwayat...</p>
  </div>
</section>
@endsection

@push('scripts')
<script>
(() => {
  const u = Auth.getUser();
  if (!Auth.isLoggedIn()) location.replace('/login');
  else if (u?.role === 'admin') location.replace('/admin');
})();

const $ = (id) => document.getElementById(id);
const NAMA_BULAN = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];

const tglF = (s) => {
  if (!s) return '-';
  const [t, j] = String(s).replace('T', ' ').split(' ');
  const tgl = t.slice(0, 10).split('-').reverse().join('/');
  return j ? `${tgl} ${j.slice(0, 5).replace(':', '.')}` : tgl;
};

const namaPelanggan = (x) => {
  const u = Auth.getUser() || {};
  return x.pelanggan?.nama || x.nama_pelanggan || u.nama || u.name || '';
};

const labelBayar = (s) => {
  const t = String(s || '');
  const lunas = /lunas/i.test(t) && !/belum/i.test(t);
  return `<span class="text-[11px] font-semibold ${lunas ? 'text-green-300' : 'text-red-300'}">${esc(t.toUpperCase())}</span>`;
};

const labelJenis = (x) => {
  const d = (x.detail_pesanan || [])[0]?.layanan;
  const jenis = d?.jenis_layanan?.nama_jenis || d?.jenis?.nama_jenis || '';
  const paket = x.paket || x.paket_layanan?.nama_paket || '';
  return [jenis, paket].filter(Boolean).join(' - ').toUpperCase();
};

const baris = (d) => {
  const satuan = d.layanan?.satuan || '';
  const kg = /kg/i.test(satuan);
  return `
    <div class="grid grid-cols-[70px_1fr] gap-y-1 text-[11px]">
      <span class="text-white/70">Layanan</span>
      <span class="font-semibold">${esc(d.layanan?.nama_layanan || '-')}</span>
      <span class="text-white/70">${kg ? 'Berat' : 'Jumlah'}</span>
      <span class="font-semibold">${esc(d.kuantitas)} ${esc(satuan)}</span>
    </div>`;
};

const kartu = (x) => `
  <div class="bg-brand text-white rounded-xl px-5 py-4 shadow">
    <div class="flex items-start justify-between gap-3">
      <div>
        <p class="text-sm font-semibold">${esc(namaPelanggan(x))}</p>
        <p class="text-[11px] text-white/80">Pemesanan : ${tglF(x.tgl_pesanan || x.created_at)}</p>
      </div>
      <span class="text-[10px] bg-white text-brand font-medium px-3 py-0.5 rounded-full">${esc(String(x.status_pesanan).toUpperCase())}</span>
    </div>
    <div class="mt-2 flex items-center justify-between">
      ${labelBayar(x.status_pembayaran)}
      <span class="text-[11px] font-semibold">${esc(labelJenis(x))}</span>
    </div>
    <div class="mt-1 border-t border-white/30"></div>
    <div class="mt-3 space-y-3">
      ${(x.detail_pesanan || []).map(baris).join('') || '<p class="text-xs text-white/70">Tidak ada rincian layanan.</p>'}
    </div>
    <div class="mt-4 flex items-center justify-between text-[11px]">
      <span class="text-white/70">Total Harga</span>
      <span class="font-semibold">${rupiah(x.total_harga)}</span>
    </div>
  </div>`;

let semua = [];
let halaman = 1;

function tampil() {
  const kata = $('cari').value.trim().toLowerCase();
  const bln = $('bulan').value; // "YYYY-MM" atau ""
  const perHal = parseInt($('per-halaman').value, 10);

  const hasil = semua.filter(x => {
    const tanggal = String(x.tgl_pesanan).slice(0, 10);
    if (bln && tanggal.slice(0, 7) !== bln) return false;
    if (kata) {
      const teks = ('#' + x.id_pesanan + ' ' + namaPelanggan(x) + ' ' +
        (x.detail_pesanan || []).map(d => d.layanan?.nama_layanan || '').join(' ')).toLowerCase();
      if (!teks.includes(kata)) return false;
    }
    return true;
  });

  const totalHal = Math.max(1, Math.ceil(hasil.length / perHal));
  if (halaman > totalHal) halaman = totalHal;
  const potong = hasil.slice((halaman - 1) * perHal, halaman * perHal);

  $('total').textContent = `Total data : ${hasil.length} data`;
  $('daftar').innerHTML = potong.map(kartu).join('') ||
    '<p class="text-sm text-slate-400">Tidak ada riwayat pesanan.</p>';
  $('info-halaman').innerHTML = `Halaman <b>${halaman}</b> dari ${totalHal}`;
  $('prev').disabled = halaman <= 1;
  $('next').disabled = halaman >= totalHal;
}

$('btn-pdf').onclick = async () => {
  const v = $('bulan').value;
  if (!v) { alert('Pilih bulan terlebih dahulu untuk Export PDF.'); return; }
  const [thn, bln] = v.split('-');
  $('btn-pdf').disabled = true;
  try {
    await downloadFile(`/pelanggan/riwayat/export-pdf?bulan=${parseInt(bln, 10)}&tahun=${thn}`,
      `riwayat-transaksi-${thn}-${bln}.pdf`);
  } catch (e) { alert(e.message); }
  $('btn-pdf').disabled = false;
};

['cari', 'bulan', 'per-halaman'].forEach(id =>
  $(id).addEventListener(id === 'cari' ? 'input' : 'change', () => { halaman = 1; tampil(); }));
$('prev').onclick = () => { halaman--; tampil(); };
$('next').onclick = () => { halaman++; tampil(); };

(async () => {
  try {
    const res = await api('/pelanggan/riwayat');
    semua = res.data || [];

    // opsi "September 2026" dari data yang ada, terbaru di atas
    const bulanAda = [...new Set(semua.map(x => String(x.tgl_pesanan).slice(0, 7)))].sort().reverse();
    $('bulan').innerHTML = '<option value="">Semua bulan</option>' +
      bulanAda.map(v => {
        const [t, b] = v.split('-');
        return `<option value="${v}">${NAMA_BULAN[parseInt(b, 10) - 1]} ${t}</option>`;
      }).join('');

    tampil();
  } catch (e) {
    if (e.status === 401) { location.href = '/login'; return; }
    $('daftar').innerHTML = `<p class="text-sm text-red-500">${esc(e.message)}</p>`;
  }
})();
</script>
@endpush