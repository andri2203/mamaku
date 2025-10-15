<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Produk Terlaris</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, sans-serif;
            margin: 40px;
            color: #333;
        }

        h1,
        h2 {
            text-align: center;
            margin-bottom: 0;
        }

        h2 {
            font-weight: normal;
            font-size: 16px;
            color: #777;
            margin-top: 4px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px;
            font-size: 14px;
        }

        th,
        td {
            border: 1px solid #ccc;
            padding: 10px 12px;
            text-align: left;
        }

        th {
            background-color: #f7e4ec;
            color: #333;
        }

        tr:nth-child(even) {
            background-color: #fafafa;
        }

        tr:hover {
            background-color: #fdf1f5;
        }

        .text-right {
            text-align: right;
        }

        footer {
            margin-top: 40px;
            text-align: center;
            font-size: 12px;
            color: #888;
        }

        /* Print style */
        @media print {
            body {
                margin: 0;
                padding: 20px;
            }

            header,
            footer {
                page-break-after: avoid;
            }

            table {
                page-break-inside: auto;
            }

            tr {
                page-break-inside: avoid;
            }
        }
    </style>
</head>

<body onload="window.print()">

    <header>
        <h1>Laporan Produk Terlaris</h1>
        <h2>Periode : {{ $periode }}</h2>
    </header>

    <table>
        <thead>
            <tr>
                <th style="width: 5%">No</th>
                <th>Product Name</th>
                <th>Brand</th>
                <th class="text-right">Total Quantity</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($trends as $index => $item)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $item['product']['name'] ?? '-' }}</td>
                <td>{{ $item['product']['brand'] ?? '-' }}</td>
                <td class="text-right">{{ $item['total_quantity'] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <footer>
        Dicetak pada {{ \Carbon\Carbon::now()->translatedFormat('d F Y, H:i') }}
    </footer>

</body>

</html>
