<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Detail Pesanan NAT Laundry</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
        }

        h2 {
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th, td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #eee;
        }
    </style>
</head>
<body>
    <h2>NAT Laundry</h2>
    <h3>Detail Pesanan</h3>

    <p>ID Pesanan: {{ $pesanan->id_pesanan }}</p>
    <p>Nama Pelanggan: {{ $pesanan->pengguna->nama_pengguna }}</p>
    <p>Tanggal Pesanan: {{ $pesanan->tgl_pesanan }}</p>
    <p>Status Pesanan: {{ $pesanan->status_pesanan }}</p>
    <p>Status Pembayaran: {{ $pesanan->status_pembayaran }}</p>

    <table>
        <thead>
            <tr>
                <th>Layanan</th>
                <th>Kuantitas</th>
                <th>Harga Satuan</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($pesanan->detailPesanan as $detail)
                <tr>
                    <td>{{ $detail->layanan->nama_layanan }}</td>
                    <td>{{ $detail->kuantitas }}</td>
                    <td>Rp {{ number_format($detail->harga_satuan, 0, ',', '.') }}</td>
                    <td>Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h3>
        Total:
        Rp {{ number_format($pesanan->total_harga, 0, ',', '.') }}
    </h3>
</body>
</html>