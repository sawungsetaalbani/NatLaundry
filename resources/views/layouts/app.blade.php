<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('title', 'NAT Laundry')</title>

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
<body class="font-sans text-slate-700 bg-white min-h-screen flex flex-col">

  {{-- NAVBAR (pill) --}}
  <header class="px-6 pt-4">
    <nav class="max-w-6xl mx-auto h-12 px-6 flex items-center justify-between border border-slate-200 rounded-full shadow-sm">
      <a href="/" class="font-bold text-brand text-sm">NAT Laundry</a>
      <div class="flex items-center gap-6">
        <a href="/" class="text-xs hover:text-brand-light {{ request()->is('/') ? 'text-brand font-semibold' : '' }}">Beranda</a>
        <a href="/layanan" class="text-xs hover:text-brand-light {{ request()->is('layanan') ? 'text-brand font-semibold' : '' }}">Layanan</a>
        <div id="nav-auth" class="flex items-center gap-6 text-xs"></div>
      </div>
    </nav>
  </header>

  <main class="flex-1">
    @yield('content')
  </main>

  {{-- FOOTER --}}
  <footer class="bg-brand text-white text-center text-xs py-6 mt-16 leading-relaxed">
    <p><span class="font-semibold">NAT Laundry</span> &nbsp;|&nbsp; Pakaian Wangi ya NAT Laundry Solusinya</p>
    <p class="mt-1">Dikembangkan oleh Tim Capstone {{ date('Y') }}</p>
  </footer>

  <script src="/js/api.js"></script>
  @stack('scripts')
</body>
</html>