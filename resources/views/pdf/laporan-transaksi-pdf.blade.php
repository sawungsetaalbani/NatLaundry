
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Transaksi NAT Laundry</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 10px;
        }

        h2, p {
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            border: 1px solid #555;
            padding: 6px;
            text-align: left;
        }

        th {
            background-color: #e5e7eb;
        }

        .total {
            margin-top: 15px;
            text-align: right;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <h2>NAT LAUNDRY</h2>
    <p>Laporan Transaksi</p>

    <p>
        Periode:
        {{ $tanggalMulai ?? 'Semua tanggal' }}
        sampai
        {{ $tanggalAkhir ?? 'Semua tanggal' }}
    </p>

    <p>
        Status pembayaran:
        {{ $statusPembayaran ?? 'Semua status' }}
    </p>

    <table>
        <thead>
            <tr>
                <th>No.</th>
                <th>ID Pesanan</th>
                <th>Nama Pelanggan</th>
                <th>Tanggal Pesanan</th>
                <th>Status Pesanan</th>
                <th>Total Harga</th>
                <th>Status Pembayaran</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($pesanan as $item)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $item->id_pesanan }}</td>
                    <td>{{ $item->pengguna->nama_pengguna ?? '-' }}</td>
                    <td>{{ $item->tgl_pesanan->format('d-m-Y') }}</td>
                    <td>{{ $item->status_pesanan }}</td>
                    <td>Rp {{ number_format((float) $item->total_harga, 0, ',', '.') }}</td>
                    <td>{{ $item->status_pembayaran }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7">Tidak ada data transaksi.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <p class="total">
        Total pendapatan:
        Rp {{ number_format((float) $totalPendapatan, 0, ',', '.') }}
    </p>
</body>
</html>
