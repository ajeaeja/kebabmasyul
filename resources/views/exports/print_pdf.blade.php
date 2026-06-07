<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #333;
            margin: 20px;
            padding: 0;
            font-size: 11px;
            background-color: #fff;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            font-size: 20px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .header p {
            margin: 5px 0 0 0;
            color: #666;
            font-size: 12px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px 10px;
            text-align: left;
        }
        th {
            background-color: #f8f9fa;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 10px;
            color: #495057;
            border-bottom: 2px solid #dee2e6;
        }
        tr:nth-child(even) {
            background-color: #fdfdfd;
        }
        .footer {
            margin-top: 40px;
            text-align: right;
            font-size: 10px;
            color: #777;
        }
        .print-btn-container {
            margin-bottom: 20px;
            text-align: center;
        }
        .btn-print {
            background-color: #ee4d2d;
            color: white;
            border: none;
            padding: 8px 20px;
            font-size: 13px;
            font-weight: bold;
            border-radius: 6px;
            cursor: pointer;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        @media print {
            .print-btn-container {
                display: none;
            }
            body {
                margin: 0;
            }
        }
    </style>
</head>
<body>

    <div class="print-btn-container">
        <button onclick="window.print()" class="btn-print">Cetak / Simpan Sebagai PDF</button>
    </div>

    <div class="header">
        <h1>Masyul Kebab</h1>
        <p>{{ $title }} - Dicetak pada {{ date('d F Y H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                @foreach($headers as $h)
                    <th>{{ $h }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($data as $row)
                <tr>
                    @foreach($row as $val)
                        <td>{{ $val }}</td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Sistem Manajemen UMKM Masyul Kebab &copy; {{ date('Y') }}</p>
    </div>

    <script>
        window.onload = function() {
            // Auto open print dialog
            setTimeout(function() {
                window.print();
            }, 500);
        };
    </script>
</body>
</html>
