<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Riwayat Transaksi NAT Laundry</title>

    <style>
        body {
            font-family: sans-serif;
            font-size: 11px;
            color: #333;
        }

        h2, h3 {
            text-align: center;
            margin-bottom: 5px;
        }

        .informasi {
            margin-top: 25px;
            margin-bottom: 15px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th, td {
            border: 1px solid #ccc;
            padding: 7px;
            text-align: left;
            vertical-align: top;
        }

        th {
            background-color: #eee;
        }

        .total {
            text-align: right;
            font-weight: bold;
        }

        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 10px;
        }
    </style>
</head>

<body>
    <h2>NAT LAUNDRY</h2>
    <h3>Riwayat Transaksi Pelanggan</h3>

    <div class="informasi">
        <p>
            Nama Pelanggan:
            {{ $pengguna->nama_pengguna }}
        </p>

        <p>
            Periode:
            {{ \Carbon\Carbon::createFromDate($tahun, $bulan, 1)->translatedFormat('F Y') }}
        </p>

        <p>
            Tanggal Cetak:
            {{ now()->format('d-m-Y') }}
        </p>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID Pesanan</th>
                <th>Tanggal</th>
                <th>Rincian Layanan</th>
                <th>Total Harga</th>
                <th>Status Pesanan</th>
                <th>Status Pembayaran</th>
            </tr>
        </thead>

        <tbody>
            @forelse ($pesanan as $item)
                <tr>
                    <td>{{ $item->id_pesanan }}</td>

                    <td>
                        {{ \Carbon\Carbon::parse($item->tgl_pesanan)->format('d-m-Y') }}
                    </td>

                    <td>
                        @forelse ($item->detailPesanan as $detail)
                            {{ $detail->layanan->nama_layanan ?? 'Layanan tidak tersedia' }}
                            ({{ $detail->kuantitas }})

                            @if (!$loop->last)
                                <br>
                            @endif
                        @empty
                            Tidak ada rincian layanan
                        @endforelse
                    </td>

                    <td>
                        Rp {{ number_format($item->total_harga, 0, ',', '.') }}
                    </td>

                    <td>{{ $item->status_pesanan }}</td>

                    <td>{{ $item->status_pembayaran }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center;">
                        Tidak ada transaksi pada periode ini.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="total">
        <p>
            Jumlah Pesanan: {{ $pesanan->count() }}
        </p>

        <p>
            Total Nilai Pesanan:
            Rp {{ number_format($pesanan->sum('total_harga'), 0, ',', '.') }}
        </p>
    </div>

    <div class="footer">
        Dokumen ini dibuat secara otomatis oleh sistem NAT Laundry.
    </div>
</body>
</html>
