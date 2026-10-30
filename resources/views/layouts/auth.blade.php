<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('title', 'NAT Laundry')</title>

  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
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
<body class="font-sans text-slate-700 bg-brand-soft min-h-screen flex items-center justify-center p-6">

  <div class="w-full max-w-md">
    <a href="/" class="block text-center font-bold text-brand mb-5">&larr; Kembali ke Beranda</a>
    @yield('content')
  </div>

  <script src="{{ asset('js/api.js') }}"></script>
  @stack('scripts')
</body>
</html>