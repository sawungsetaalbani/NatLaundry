@extends('layouts.app')
@section('title', 'Daftar Layanan - NAT Laundry')

@section('content')
<section class="max-w-6xl mx-auto px-6 py-10">
  <h1 class="text-3xl text-brand text-center"><span class="font-semibold">Daftar</span> <span class="font-light">Layanan</span></h1>
  <div id="daftar" class="mt-10 space-y-12">
    <p class="text-sm text-slate-400 text-center">Memuat layanan...</p>
  </div>
</section>
@endsection

@push('scripts')
<script>
const ikon = `<svg class="w-9 h-9 text-brand" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="3" width="16" height="18" rx="3"/><circle cx="12" cy="13" r="4"/><path d="M8 7h.01M11 7h.01"/></svg>`;

// badge paket: Reguler = biru tua, Express = biru muda (sesuaikan field API)
const paketOf = (l) => l.paket?.nama_paket || l.nama_paket || l.paket || '';
const badge = (l) => {
  const p = String(paketOf(l));
  if (!p) return '';
  const express = /express/i.test(p);
  return `<span class="absolute top-0 right-0 text-[9px] text-white px-3 py-1 rounded-bl-lg rounded-tr-xl ${express ? 'bg-brand-light' : 'bg-brand'}">${esc(p)}</span>`;
};

const kartu = (l) => `
  <div class="relative shrink-0 w-[calc(25%-12px)] min-w-[200px] bg-white border border-brand-soft rounded-xl shadow-sm px-4 pt-9 pb-4 snap-start">
    ${badge(l)}
    <p class="text-xs font-semibold text-brand">${esc(l.nama_layanan)}</p>
    <div class="mt-5 flex items-end justify-between gap-2">
      <span class="text-[10px] text-slate-400">Harga Layanan</span>
      <span class="text-[11px] font-semibold text-brand whitespace-nowrap">${rupiah(l.harga)} / ${esc(l.satuan)}</span>
    </div>
  </div>`;

const seksi = (j, i) => `
  <div>
    <div class="flex items-start gap-3">
      ${ikon}
      <div>
        <h2 class="text-xl font-semibold text-brand">${esc(j.nama_jenis)}</h2>
        <p class="text-xs text-slate-500">${esc(j.deskripsi || '')}</p>
      </div>
    </div>
    <div class="mt-4 flex items-center gap-4">
      <div class="flex flex-col gap-2">
        <button onclick="geser(${i}, 1)" aria-label="Berikutnya" class="w-6 h-6 rounded-full border border-brand text-brand text-xs hover:bg-brand-soft">&rsaquo;</button>
        <button onclick="geser(${i}, -1)" aria-label="Sebelumnya" class="w-6 h-6 rounded-full border border-brand text-brand text-xs hover:bg-brand-soft">&lsaquo;</button>
      </div>
      <div id="row-${i}" class="flex gap-4 overflow-x-auto snap-x scroll-smooth flex-1 pb-2" style="scrollbar-width:none">
        ${(j.layanan || []).map(kartu).join('') || '<p class="text-xs text-slate-400">Belum ada layanan.</p>'}
      </div>
    </div>
  </div>`;

function geser(i, arah) {
  const el = document.getElementById('row-' + i);
  el.scrollBy({ left: arah * el.clientWidth * 0.8, behavior: 'smooth' });
}

(async () => {
  const box = document.getElementById('daftar');
  try {
    const res = await api('/jenis-layanan');
    const list = res.data || [];
    box.innerHTML = list.map(seksi).join('') ||
      '<p class="text-sm text-slate-400 text-center">Belum ada layanan.</p>';
  } catch (e) {
    box.innerHTML = `<p class="text-sm text-red-500 text-center">${esc(e.message)}</p>`;
  }
})();
</script>
@endpush