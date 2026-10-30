function openModal(id) {
  const m = document.getElementById(id);
  m.classList.remove('hidden');
  m.classList.add('flex');
}
function closeModal(id) {
  const m = document.getElementById(id);
  m.classList.add('hidden');
  m.classList.remove('flex');
}
function showError(el, e) {
  el.textContent = e.errors ? Object.values(e.errors).flat().join(' ') : e.message;
  el.classList.remove('hidden');
}
function hideError(el) { el.classList.add('hidden'); }

const tgl = (s) => (s ? String(s).slice(0, 10).split('-').reverse().join('-') : '-');

function badge(teks) {
  const warna = {
    Selesai: 'bg-green-100 text-green-700',
    Diproses: 'bg-amber-100 text-amber-700',
    Dibatalkan: 'bg-red-100 text-red-700',
    Lunas: 'bg-green-100 text-green-700',
    'Belum Lunas': 'bg-red-100 text-red-700',
  }[teks] || 'bg-slate-100 text-slate-600';
  return `<span class="px-2 py-0.5 rounded-full text-xs font-medium ${warna}">${esc(teks)}</span>`;
}

const rowsKosong = (kolom, teks = 'Tidak ada data.') =>
  `<tr><td colspan="${kolom}" class="px-4 py-6 text-center text-slate-400">${teks}</td></tr>`;

// ===== Helper halaman pesanan =====
const kodePesanan = (id) => 'P' + String(id).padStart(4, '0');
const tglGaris = (s) => (s ? String(s).slice(0, 10).split('-').reverse().join('/') : '-');
const rp = (n) => 'Rp. ' + Number(n || 0).toLocaleString('id-ID');
const angka = (n) => Number(n || 0).toLocaleString('id-ID', { maximumFractionDigits: 2 });

// Tanggal hari ini menurut jam komputer (bukan UTC, supaya tidak mundur sehari)
const hariIni = () => {
  const d = new Date();
  return d.getFullYear() + '-' + String(d.getMonth() + 1).padStart(2, '0') + '-' + String(d.getDate()).padStart(2, '0');
};

// ID baris detail pesanan (nama kolom primary key-nya dideteksi otomatis)
const idDetail = (d) =>
  d.id_detail_pesanan ?? d.id_detail ?? d.id ??
  Object.entries(d).find(([k]) => k.startsWith('id') && !['id_pesanan', 'id_layanan'].includes(k))?.[1];