const API_URL = '/api';

const Auth = {
  getToken: () => localStorage.getItem('token'),
  isLoggedIn: () => !!localStorage.getItem('token'),
  setSession(token, pengguna) {
    localStorage.setItem('token', token);
    localStorage.setItem('pengguna', JSON.stringify(pengguna));
  },
  getUser() {
    try { return JSON.parse(localStorage.getItem('pengguna')); } catch { return null; }
  },
  clear() {
    localStorage.removeItem('token');
    localStorage.removeItem('pengguna');
  },
};

async function api(path, { method = 'GET', body = null } = {}) {
  const headers = { Accept: 'application/json' };
  if (body) headers['Content-Type'] = 'application/json';
  const token = Auth.getToken();
  if (token) headers['Authorization'] = 'Bearer ' + token;

  const res = await fetch(API_URL + path, {
    method,
    headers,
    body: body ? JSON.stringify(body) : null,
  });

  let json = null;
  try { json = await res.json(); } catch {}

  if (!res.ok) {
    if (res.status === 401 && token) {
      Auth.clear();
      if (location.pathname.startsWith('/admin')) location.href = '/login';
    }
    const err = new Error(json?.message || 'Terjadi kesalahan.');
    err.status = res.status;
    err.errors = json?.errors;
    throw err;
  }
  return json;
}

// Unduh file (PDF) yang butuh token login
async function downloadFile(path, filename) {
  const res = await fetch(API_URL + path, {
    headers: {
      Accept: 'application/json, application/pdf',
      Authorization: 'Bearer ' + Auth.getToken(),
    },
  });
  if (!res.ok) {
    let msg = 'Gagal mengunduh file.';
    try { msg = (await res.json()).message || msg; } catch {}
    throw new Error(msg);
  }
  const url = URL.createObjectURL(await res.blob());
  const a = document.createElement('a');
  a.href = url;
  a.download = filename;
  document.body.appendChild(a);
  a.click();
  a.remove();
  URL.revokeObjectURL(url);
}

const rupiah = (n) => 'Rp ' + Number(n || 0).toLocaleString('id-ID');

const esc = (s) =>
  String(s ?? '').replace(/[&<>"']/g, (c) => (
    { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]
  ));

async function logout() {
  try { await api('/auth/logout', { method: 'POST' }); } catch {}
  Auth.clear();
  location.href = '/';
}

function renderNavAuth() {
  const el = document.getElementById('nav-auth');
  if (!el) return;
  if (Auth.isLoggedIn()) {
    const user = Auth.getUser();
    const menu = user?.role === 'admin'
      ? '<a href="/admin" class="text-sm hover:text-brand-light">Admin</a>'
      : '<a href="/pesanan" class="text-sm hover:text-brand-light">Pesanan</a>';
    el.innerHTML = `
      ${menu}
      <span class="text-sm text-slate-400 hidden sm:inline">${esc(user?.nama_pengguna || '')}</span>
      <button onclick="logout()" class="bg-brand-light text-white text-sm px-4 py-1.5 rounded-lg hover:opacity-90">Logout</button>`;
  } else {
    el.innerHTML = `<a href="/login" class="bg-brand-light text-white text-sm px-4 py-1.5 rounded-lg hover:opacity-90">Login</a>`;
  }
}
document.addEventListener('DOMContentLoaded', renderNavAuth);