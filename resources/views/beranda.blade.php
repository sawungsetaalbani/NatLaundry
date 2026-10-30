@extends('layouts.app')
@section('title', 'Beranda - NAT Laundry')

@php
  $ikon = fn($d) => '<svg class="w-8 h-8 text-brand" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">'.$d.'</svg>';
@endphp

@section('content')

{{-- HERO --}}
<section class="max-w-6xl mx-auto px-6 pt-10 grid md:grid-cols-2 gap-10 items-center">
  <div>
    <h1 class="text-3xl md:text-4xl text-brand leading-tight">
      <span class="font-semibold">Cucian Lebih Higienis,</span>
      <span class="font-light">Rapi Sempurna Tanpa Khawatir.</span>
    </h1>
    <p class="mt-5 text-xs text-brand max-w-md leading-relaxed">
      Pencucian khusus tanpa dicampur dengan setrika uap yang aman untuk segala jenis pakaian.
      Pakaian awet, rapi, dan wangi sepanjang hari.
    </p>
    <div class="mt-7 flex items-center gap-5">
      <a href="/layanan" class="bg-brand text-white text-xs px-6 py-2.5 rounded-lg hover:bg-brand-dark">Lihat Layanan</a>
      <a href="/pesanan" class="flex items-center gap-2 text-xs text-brand">
        <span class="w-6 h-6 rounded-full bg-brand-soft flex items-center justify-center">&rsaquo;</span> Pesan sekarang!
      </a>
    </div>
  </div>
  {{-- taruh gambar mesin cuci di public/images/hero.png --}}
  <img src="{{ asset('images/hero.png') }}" alt="NAT Laundry" class="w-full max-w-md mx-auto" onerror="this.style.display='none'">
</section>

{{-- KEUNGGULAN --}}
<section class="max-w-6xl mx-auto px-6 mt-8">
  <div class="grid sm:grid-cols-3 gap-4">
    @foreach ([
      ['1 Mesin', '1 Pelanggan'],
      ['Request Cuci', 'Manual'],
      ['Menggunakan', 'Setrika Uap'],
    ] as [$a, $b])
      <div class="bg-white border border-slate-200 rounded-xl px-4 py-3 shadow-sm flex items-center gap-3">
        <span class="w-8 h-8 rounded-md bg-brand-soft"></span>
        <p class="text-[11px] font-medium text-brand leading-tight">{{ $a }}<br>{{ $b }}</p>
      </div>
    @endforeach
  </div>
</section>

{{-- LAYANAN KAMI (data dari API) --}}
<section class="max-w-6xl mx-auto px-6 mt-20">
  <div class="flex items-start justify-between">
    <div class="flex items-start gap-3">
      {!! $ikon('<rect x="4" y="3" width="16" height="18" rx="3"/><circle cx="12" cy="13" r="4"/>') !!}
      <div>
        <h2 class="text-xl font-semibold text-brand">Layanan Kami</h2>
        <p class="text-xs text-brand">Banyak pilihan, sesuaikan dengan kebutuhan anda!</p>
      </div>
    </div>
    <a href="/layanan" class="text-xs bg-brand text-white px-4 py-1.5 rounded-lg hover:bg-brand-dark">Detail Layanan &rsaquo;</a>
  </div>
  <div id="layanan-grid" class="mt-8 grid md:grid-cols-3 gap-8">
    <p class="text-sm text-slate-400">Memuat layanan...</p>
  </div>
</section>

{{-- ALUR CARA KERJA --}}
<section class="max-w-6xl mx-auto px-6 mt-20">
  <div class="flex items-start gap-3">
    {!! $ikon('<rect x="3" y="3" width="13" height="13" rx="2"/><path d="M8 21h11a2 2 0 0 0 2-2V8"/>') !!}
    <div>
      <h2 class="text-xl font-semibold text-brand">Alur Cara Kerja</h2>
      <p class="text-xs text-brand">Pengecekan mudah dan lebih aman!</p>
    </div>
  </div>
  <div class="mt-8 grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
    @foreach ([
      ['alur-1.jpg', 'Datang & Serahkan Pakaian', 'Bawa pakaian kotor Anda langsung ke gerai kami. Tim kami siap menerima dan mencatat pesanan Anda.'],
      ['alur-2.jpg', 'Registrasi & Login', 'Lakukan registrasi agar pesanan Anda tercatat. Cukup login dengan akun yang sudah dibuat.'],
      ['alur-3.jpg', 'Pantau via Web', 'Cek status pesanan Anda kapan saja melalui website, dari proses hingga selesai.'],
      ['alur-4.jpg', 'Ambil Pakaian Rapi & Wangi', 'Setelah status "Selesai", ambil pakaian Anda di gerai dalam keadaan rapi dan wangi.'],
    ] as $i => [$img, $judul, $isi])
      <div class="relative">
        <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden h-full">
          <div class="h-28 bg-brand-soft">
            <img src="{{ asset('images/'.$img) }}" alt="{{ $judul }}" class="w-full h-full object-cover" onerror="this.style.display='none'">
          </div>
          <div class="p-4">
            <p class="text-[11px] font-semibold text-brand">{{ $judul }}</p>
            <p class="mt-2 text-[10px] text-slate-500 leading-relaxed">{{ $isi }}</p>
          </div>
        </div>
        @if ($i < 3)
          <span class="hidden lg:block absolute -right-5 top-1/2 -translate-y-1/2 text-slate-400 text-sm">&rarr;</span>
        @endif
      </div>
    @endforeach
  </div>
</section>

{{-- KUNJUNGI KAMI --}}
<section class="max-w-6xl mx-auto px-6 mt-20">
  <div class="bg-brand rounded-3xl p-8 text-white grid md:grid-cols-2 gap-8">
    <div>
      <h2 class="text-xl font-semibold">Kunjungi Kami</h2>
      <p class="text-xs text-white/80">Informasi kontak dan alamat laundry</p>

      <div class="mt-6 bg-white text-slate-700 rounded-xl p-5 text-xs">
        <p class="font-semibold text-brand">Jam Operasional</p>
        <div class="mt-3 grid grid-cols-2 gap-y-1">
          <span class="font-medium">Senin - Sabtu</span><span>08.00 - 20.00</span>
          <span class="font-medium">Minggu</span><span class="text-red-600 font-medium">Tutup</span>
        </div>
      </div>

      <div class="mt-5 flex items-center gap-3">
        <span class="w-9 h-9 rounded-full bg-white text-brand flex items-center justify-center text-sm">&#9742;</span>
        <div class="text-xs">
          <p class="text-white/80">Kontak</p>
          <p class="font-semibold">081234567891</p>
        </div>
      </div>
    </div>

    <div class="space-y-4">
      <iframe class="w-full h-44 rounded-xl border-0" loading="lazy" title="Peta NAT Laundry"
        src="https://www.google.com/maps/place/NAT+laundryshop/@-7.9146412,112.6340265,17z/data=!3m1!4b1!4m6!3m5!1s0x2dd62baf44bd8fcd:0xa0e03f087d0ee5fe!8m2!3d-7.9146412!4d112.6340265!16s%2Fg%2F11t03qjs89?entry=ttu&g_ep=EgoyMDI2MTAyMC4wIKXMDSoASAFQAw%3D%3D"></iframe>
      <div class="bg-white text-slate-700 rounded-xl p-4 text-xs flex gap-3">
        <span class="text-brand">&#128205;</span>
        <div>
          <p class="font-semibold text-brand">Alamat</p>
          <p class="mt-1">Gunung Ring, Tanjungrejo, Kec. Bringsatan, Kabupaten Malang, Jawa Timur 65313, Indonesia</p>
        </div>
      </div>
    </div>
  </div>
</section>

@endsection

@push('scripts')
<script>
// paket dari field API (sesuaikan kalau beda)
const paketOf = (l) => l.paket?.nama_paket || l.nama_paket || l.paket || '';

(async () => {
  const grid = document.getElementById('layanan-grid');
  try {
    const res = await api('/jenis-layanan');
    const list = res.data || [];
    if (!list.length) {
      grid.innerHTML = '<p class="text-sm text-slate-400">Belum ada layanan.</p>';
      return;
    }
    grid.innerHTML = list.slice(0, 3).map(j => {
      const paket = [...new Set((j.layanan || []).map(paketOf).filter(Boolean))];
      // kalau belum ada data paket, tampilkan nama layanan sebagai gantinya
      const item = paket.length ? paket : (j.layanan || []).slice(0, 3).map(l => l.nama_layanan);
      return `
        <div>
          <div class="inline-block bg-brand text-white text-xs font-semibold px-6 py-2.5 rounded-t-xl uppercase">${esc(j.nama_jenis)}</div>
          <div class="bg-[#D3DCE6] rounded-b-xl rounded-tr-xl px-6 py-5 min-h-[96px]">
            <ul class="space-y-1.5 text-xs text-brand">
              ${item.map(t => `<li class="flex gap-2"><span>&bull;</span><span>${esc(t)}</span></li>`).join('')
                || '<li class="text-slate-500">Belum ada layanan.</li>'}
            </ul>
          </div>
        </div>`;
    }).join('');
  } catch (e) {
    grid.innerHTML = `<p class="text-sm text-red-500">${esc(e.message)}</p>`;
  }
})();
</script>
@endpush