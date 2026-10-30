@extends('layouts.admin')
@section('title', 'Dashboard')

@section('heading')
  <h1 class="text-3xl text-brand"><span class="font-semibold">Dashboard</span> <span class="font-light">Pendapatan</span></h1>
@endsection

@php
  $kartu = 'border border-brand-light/60 rounded-xl p-5 relative bg-white';
  $lingkar = 'absolute top-4 right-4 w-7 h-7 rounded-full bg-[#E3E9F2] text-brand-light flex items-center justify-center';
@endphp

@section('content')
{{-- Kartu ringkasan --}}
<div class="grid sm:grid-cols-2 xl:grid-cols-4 gap-4">
  <div class="{{ $kartu }}">
    <span class="{{ $lingkar }}"><svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 4h2l2 10h10l2-7H6"/><circle cx="9" cy="19" r="1.5"/><circle cx="17" cy="19" r="1.5"/></svg></span>
    <p class="text-xs text-brand pr-8">Pesanan Diproses</p>
    <p id="k-proses" class="mt-2 text-3xl font-bold text-brand">-</p>
    <p id="k-periode" class="text-[10px] text-brand-light text-right"></p>
  </div>

  <div class="{{ $kartu }}">
    <span class="{{ $lingkar }}"><svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="8" r="3"/><path d="M3 20c0-3 3-5 6-5s6 2 6 5"/><circle cx="17" cy="9" r="2"/></svg></span>
    <p class="text-xs text-brand pr-8">Pelanggan Aktif</p>
    <p id="k-pelanggan" class="mt-2 text-3xl font-bold text-brand">-</p>
  </div>

  <div class="{{ $kartu }}">
    <span class="{{ $lingkar }}"><svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="8" r="3"/><path d="M3 20c0-3 3-5 6-5s6 2 6 5"/><circle cx="17" cy="9" r="2"/></svg></span>
    <p class="text-xs text-brand pr-8">Total Pendapatan</p>
    <p id="k-total" class="mt-2 text-2xl font-bold text-brand">-</p>
  </div>

  <div class="{{ $kartu }}">
    <span class="{{ $lingkar }}"><svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="8" r="3"/><path d="M3 20c0-3 3-5 6-5s6 2 6 5"/><circle cx="17" cy="9" r="2"/></svg></span>
    <p id="k-label-bulan" class="text-xs text-brand pr-8">Pendapatan / Bulan</p>
    <p id="k-bulanan" class="mt-2 text-2xl font-bold text-brand">-</p>
  </div>
</div>

<div class="mt-5 grid lg:grid-cols-[minmax(0,1fr)_20rem] gap-4">
  {{-- Grafik --}}
  <div class="border border-brand-light/60 rounded-xl p-5 bg-white">
    <div class="flex flex-wrap items-center justify-between gap-3">
      <h2 class="font-semibold text-brand">Grafik Penjualan</h2>
      <label class="flex items-center gap-3 text-[11px] text-brand-light">
        Filter data
        <span class="relative">
          <select id="tahun" class="appearance-none bg-[#DCE3EC] text-brand text-xs rounded-lg pl-4 pr-9 py-2 focus:outline-none focus:ring-2 focus:ring-brand-light"></select>
          <svg class="w-3 h-3 absolute right-3 top-1/2 -translate-y-1/2 text-brand pointer-events-none" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9l6 6 6-6"/></svg>
        </span>
      </label>
    </div>
    <div class="relative h-64 mt-4"><canvas id="grafik"></canvas></div>
  </div>

  {{-- Data pesanan diproses --}}
  <div class="border border-brand-light/60 rounded-xl p-5 bg-white">
    <div class="flex items-start justify-between gap-3">
      <div>
        <h2 class="font-semibold text-brand">Data Pesanan</h2>
        <p class="text-[10px] text-brand-light">DIPROSES</p>
      </div>
      <a href="/admin/pesanan" class="bg-brand text-white text-[11px] px-3 py-1.5 rounded-lg hover:bg-brand-dark">Detail data</a>
    </div>

    <div class="mt-4 rounded-t-lg overflow-hidden">
      <table class="w-full text-[11px] text-brand">
        <thead class="bg-[#4F8DB8] text-white">
          <tr>
            <th class="py-2 px-2 font-medium w-8">No.</th>
            <th class="py-2 px-2 font-medium">No. Pesanan</th>
            <th class="py-2 px-2 font-medium">Pemesan</th>
          </tr>
        </thead>
        <tbody id="tbody" class="text-center"></tbody>
      </table>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const $ = (id) => document.getElementById(id);

const BULAN_PENUH = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
const BULAN_PENDEK = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agust','Sep','Okt','Nov','Des'];
const WARNA = ['#F2A0A0','#F5D9A0','#B5E0A0','#A0E8F0','#9EC5F0','#E3B5B5','#D8B8A0','#8E8E8E','#F2E8A0','#DDE3EE','#DDE3EE','#DDE3EE'];

const rpt = (n) => 'Rp. ' + Number(n || 0).toLocaleString('id-ID');
const kd = (id) => 'P' + String(id).padStart(4, '0');
const tg = (s) => (s ? String(s).slice(0, 10).split('-').reverse().join('/') : '-');

const sekarang = new Date();
const tahunIni = sekarang.getFullYear();
const bulanIni = sekarang.getMonth() + 1;

// Pilihan tahun: tahun ini dan 4 tahun sebelumnya
$('tahun').innerHTML = Array.from({ length: 5 }, (_, i) => tahunIni - i)
  .map(t => `<option value="${t}">${t}</option>`).join('');
$('tahun').value = tahunIni;

let chart = null;

function singkat(v) {
  if (v >= 1e6) return (v / 1e6).toLocaleString('id-ID', { maximumFractionDigits: 1 }) + ' Jt';
  if (v >= 1e3) return (v / 1e3).toLocaleString('id-ID', { maximumFractionDigits: 0 }) + 'K';
  return v;
}

async function muatRingkasan() {
  try {
    const d = await api(`/admin/dashboard?year=${tahunIni}&month=${bulanIni}`);
    $('k-proses').textContent = d.pesanan_diproses;
    $('k-periode').textContent = BULAN_PENUH[bulanIni - 1] + ' ' + tahunIni;
    $('k-pelanggan').textContent = d.pelanggan_aktif;
    $('k-total').textContent = rpt(d.total_pendapatan);
    $('k-label-bulan').textContent = 'Pendapatan / ' + BULAN_PENUH[bulanIni - 1];
    $('k-bulanan').textContent = rpt(d.pendapatan_bulanan.total);
  } catch (e) { alert(e.message); }
}

async function muatGrafik() {
  try {
    const g = await api(`/admin/dashboard/grafik-penjualan?year=${$('tahun').value}`);
    if (chart) chart.destroy();
    chart = new Chart($('grafik'), {
      type: 'bar',
      data: {
        labels: BULAN_PENDEK,
        datasets: [{
          label: 'Pendapatan',
          data: g.data.map(x => x.total),
          backgroundColor: WARNA,
          borderRadius: 8,
          borderSkipped: false,
          barPercentage: 0.6,
          categoryPercentage: 0.85,
        }],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: { display: false },
          tooltip: { callbacks: { label: (c) => rpt(c.parsed.y) } },
        },
        scales: {
          x: { grid: { display: false }, border: { display: false }, ticks: { color: '#2F5B8C', font: { size: 10 } } },
          y: {
            beginAtZero: true,
            grid: { display: false },
            border: { display: false },
            ticks: { color: '#2F5B8C', font: { size: 10 }, callback: singkat },
          },
        },
      },
    });
  } catch (e) { alert(e.message); }
}

async function muatPesanan() {
  try {
    const p = await api('/admin/dashboard/pesanan?page=1');
    $('tbody').innerHTML = p.data.slice(0, 3).map((x, i) => `
      <tr class="border-b border-slate-100">
        <td class="py-3 px-2">${i + 1}.</td>
        <td class="py-3 px-2">${kd(x.id_pesanan)}</td>
        <td class="py-3 px-2">
          <div class="font-semibold">${esc(x.pengguna?.nama_pengguna || '-')}</div>
          <div class="text-[10px] text-brand-light">${tg(x.tgl_pesanan)}</div>
        </td>
      </tr>`).join('') || rowsKosong(3, 'Tidak ada pesanan diproses.');
  } catch (e) { $('tbody').innerHTML = rowsKosong(3, esc(e.message)); }
}

$('tahun').addEventListener('change', muatGrafik);

muatRingkasan();
muatGrafik();
muatPesanan();
</script>
@endpush