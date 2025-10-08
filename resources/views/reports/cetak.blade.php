<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Laporan - {{ $title }}</title>
    <!-- vite blade -->
    @vite('resources/css/app.css')
</head>

<body onload="window.print()" class="p-8">
    <h1 class="text-xl text-center font-bold mb-4">{{ $title }}</h1>
    <table class="w-full border-collapse">
        <thead>
            @if (isset($table['foot']))
            <tr>
                <th colspan="{{ count($table['header']) }}" class="border border-gray-300 px-4 py-2 text-left bg-gray-100">Stock Awal : {{ $table['foot']['starting_stock'] }}</th>
            </tr>
            @endif
            <tr>
                @foreach ($table['header'] as $column)
                <th class="border border-gray-300 px-4 py-2 text-left">{{ $column['label'] }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach ($table['rows'] as $row)
            <tr>
                @foreach ($table['header'] as $column)
                <td class="border border-gray-300 px-4 py-2 text-sm">{{ $row[$column['key']] }}</td>
                @endforeach
            </tr>
            @endforeach
        </tbody>
        @if (isset($table['foot']))
        <tfoot>
            <tr>
                <td colspan="{{ count($table['header']) - 1 }}" class="border border-gray-300 px-4 py-2 text-right font-bold">Stok Akhir:</td>
                <td class="border border-gray-300 px-4 py-2 text-sm font-bold">{{ $table['foot']['final_stock'] }}</td>
            </tr>
        </tfoot>
        @endif
    </table>
</body>

</html>
