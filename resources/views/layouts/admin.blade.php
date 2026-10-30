<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('title', 'Admin') - NAT Laundry</title>

  <script>
    // Hanya admin yang boleh membuka halaman ini
    try {
      const u = JSON.parse(localStorage.getItem('pengguna'));
      if (!localStorage.getItem('token') || !u || u.role !== 'admin') location.replace('/login');
    } catch (e) { location.replace('/login'); }
  </script>

  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: { extend: {
        fontFamily: { sans: ['Poppins', 'sans-serif'] },
        colors: { brand: { DEFAULT: '#2F5B8C', dark: '#274C78', light: '#4F94CD', soft: '#E3ECF6' } },
      } },
    };
  </script>
</head>
<body class="font-sans bg-white text-slate-700">
  <div class="md:flex min-h-screen">

    <aside class="md:w-44 md:shrink-0 bg-brand text-white">
      <div class="px-4 pt-7 pb-4 text-center">
        <p class="text-sm font-semibold">NAT <em class="font-medium">Laundry</em></p>
        <div class="mt-4 border-t border-dashed border-white/30"></div>
      </div>

      @php
        $ikon = [
          'dashboard' => '<path d="M4 4h6v6H4zM14 4h6v6h-6zM4 14h6v6H4zM14 14h6v6h-6z"/>',
          'pesanan'   => '<path d="M3 4h2l2 10h10l2-7H6"/><circle cx="9" cy="19" r="1.5"/><circle cx="17" cy="19" r="1.5"/>',
          'pelanggan' => '<circle cx="9" cy="8" r="3"/><path d="M3 20c0-3 3-5 6-5s6 2 6 5"/><circle cx="17" cy="9" r="2"/><path d="M17 14c2.5 0 4 1.5 4 4"/>',
          'layanan'   => '<path d="M6 7h12l1 13H5zM9 7a3 3 0 0 1 6 0"/>',
          'jenis'     => '<rect x="5" y="4" width="14" height="16" rx="2"/><path d="M9 10h6"/>',
          'laporan'   => '<rect x="6" y="5" width="12" height="16" rx="2"/><path d="M9 5V3h6v2"/>',
        ];
        $menu = [
          ['/admin', 'Dashboard', 'dashboard', request()->is('admin')],
          ['/admin/pesanan', 'Data Pesanan', 'pesanan', request()->is('admin/pesanan', 'admin/pesanan/*')],
          ['/admin/pengguna', 'Data Pelanggan', 'pelanggan', request()->is('admin/pengguna', 'admin/pengguna/*')],
          ['/admin/layanan', 'Data Layanan', 'layanan', request()->is('admin/layanan', 'admin/layanan/*')],
          ['/admin/jenis-layanan', 'Jenis Layanan', 'jenis', request()->is('admin/jenis-layanan')],
          ['/admin/laporan', 'Laporan Transaksi', 'laporan', request()->is('admin/laporan')],
        ];
      @endphp

      <nav class="flex md:flex-col flex-wrap gap-1 px-3 pb-4 text-xs">
        @foreach ($menu as [$url, $nama, $ik, $aktif])
          <a href="{{ $url }}" class="flex items-center gap-2 px-3 py-2 rounded-lg {{ $aktif ? 'font-semibold bg-white/10' : 'hover:bg-white/10' }}">
            <svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">{!! $ikon[$ik] !!}</svg>
            {{ $nama }}
          </a>
        @endforeach
      </nav>
    </aside>

    <main class="flex-1 min-w-0 p-6 md:p-8">
      <header class="flex items-start justify-between gap-4 mb-6">
        <div class="min-w-0">@yield('heading')</div>

        <div class="relative shrink-0">
          <button id="user-btn" type="button" class="flex items-center gap-2 text-left text-brand">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><circle cx="12" cy="8" r="3.5"/><path d="M5 20c0-4 3-6 7-6s7 2 7 6"/></svg>
            <span>
              <span id="u-nama" class="block text-xs font-semibold leading-tight">-</span>
              <span class="block text-[11px] leading-tight">Admin Laundry</span>
            </span>
            <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9l6 6 6-6"/></svg>
          </button>
          <div id="user-menu" class="hidden absolute right-0 mt-2 w-40 bg-white border border-slate-200 rounded-lg shadow-lg text-sm z-40 overflow-hidden">
            <a href="/" class="block px-4 py-2 hover:bg-slate-50">Lihat Website</a>
            <button type="button" onclick="logout()" class="w-full text-left px-4 py-2 hover:bg-slate-50">Logout</button>
          </div>
        </div>
      </header>

      @yield('content')
    </main>
  </div>

  <script src="{{ asset('js/api.js') }}"></script>
  <script src="{{ asset('js/admin.js') }}"></script>
  <script>
    document.getElementById('u-nama').textContent = Auth.getUser()?.nama_pengguna || '-';
    document.getElementById('user-btn').addEventListener('click', (e) => {
      e.stopPropagation();
      document.getElementById('user-menu').classList.toggle('hidden');
    });
    document.addEventListener('click', () => document.getElementById('user-menu').classList.add('hidden'));
  </script>
  @stack('scripts')
</body>
</html>