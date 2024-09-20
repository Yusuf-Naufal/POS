<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Order PDF</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            margin: 20px;
            color: #333;
        }
        h1 {
            font-size: 24px;
            margin-bottom: 5px;
        }
        p {
            font-size: 12px;
            margin: 3px 0;
        }
        hr {
            border: none;
            border-top: 1px solid #ccc;
            margin: 15px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        td.text-right {
            text-align: right;
        }
        .total {
            font-weight: bold;
            font-size: 14px;
            margin-top: 10px;
        }
        .summary {
            margin-top: 15px;
        }
        .note {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <!-- Outlet Information -->
    <div>
        <h1>{{ $order->outlet->nama_outlet }}</h1>
        <p>{{ $order->outlet->alamat }}</p>
        <p>{{ $order->outlet->no_telp }}</p>
    </div>

    <hr>

    <!-- Order Information -->
    <div>
        <p><strong>Tanggal:</strong> {{ $order->tanggal }}</p>
        <p><strong>Resi:</strong> {{ $order->resi }}</p>
        <p><strong>Pemesan:</strong> {{ $order->nama_pemesan }}</p>
        <p><strong>Jam Ambil:</strong> {{ $order->jam_mengambil }}</p>
    </div>

    <hr>

    <!-- Order Items Table -->
    <table>
        <thead>
            <tr>
                <th>Produk</th>
                <th>Qty</th>
                <th class="text-right">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($order->detailOrders as $detail)
            <tr>
                <td>{{ $detail->produk->nama_produk }}</td>
                <td>{{ $detail->qty }}</td>
                <td class="text-right">Rp. {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <hr>

    <!-- Summary -->
    <div class="summary">
        <p><strong>Total Qty:</strong> {{ $order->total_qty }}</p>
        <p><strong>Sub Total:</strong> Rp. {{ number_format($order->total_belanja, 0, ',', '.') }}</p>
        <p class="total"><strong>Total:</strong> Rp. {{ number_format($order->total_belanja, 0, ',', '.') }}</p>
    </div>

    <hr>

    <!-- Notes Section -->
    <div class="note">
        <p><strong>Catatan:</strong> {{ $order->catatan ?? 'Tidak ada catatan' }}</p>
        <p class="text-red-600 font-light">*Tunjukan stuk ini untuk mengambil pesanan</p>
    </div>
</body>
</html>
