<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Logbook</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #333; }

        .header-logo {
            width: 100%;
            padding: 14px 20px;
            margin-bottom: 20px;
            border-bottom: 2px solid #1e3a8a;
            text-align: center;
        }
        .header-logo table {
            border: none;
            margin: 0 auto;
        }
        .header-logo td {
            border: none;
            padding: 0 18px;
            vertical-align: middle;
            text-align: center;
        }
        .header-logo img {
            height: 42px;
            width: auto;
        }

        h1 { font-size: 18px; margin-bottom: 4px; padding: 0 20px; }
        .subtitle { color: #666; margin-bottom: 20px; padding: 0 20px; }

        table.report-table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        table.report-table th, table.report-table td { border: 1px solid #ddd; padding: 8px; text-align: left; vertical-align: top; }
        table.report-table th { background-color: #f5f5f5; font-weight: bold; }
        .kosong { color: #999; font-style: italic; }
        .col-hari { width: 22%; }
        .col-gambar { width: 25%; }
        .content-wrapper { padding: 0 20px; }

        .entry-images img {
            width: 55px;
            height: 55px;
            object-fit: cover;
            border: 1px solid #ddd;
            margin: 2px;
        }
    </style>
</head>
<body>

    <div class="header-logo">
        <table>
            <tr>
                <td>
                    <img src="{{ public_path('images/logo/smartlogy.png') }}" alt="PT Smartlogy">
                </td>
                <td>
                    <img src="{{ public_path('images/logo/poliwangi.png') }}" alt="Poliwangi">
                </td>
                <td>
                    <img src="{{ public_path('images/logo/diktisaintek.png') }}" alt="Diktisaintek Berdampak">
                </td>
            </tr>
        </table>
    </div>

    <div class="content-wrapper">
        <h1>Laporan Logbook Aktivitas</h1>
        <p class="subtitle">
            {{ $user->name }} &bull; Periode: {{ $periode }}
        </p>

        <table class="report-table">
            <thead>
                <tr>
                    <th class="col-hari">Hari, Tanggal</th>
                    <th>Deskripsi Kegiatan</th>
                    <th class="col-gambar">Gambar</th>
                </tr>
            </thead>
            <tbody>
                @forelse($report as $item)
                    <tr>
                        <td>{{ $item['tanggal'] }}</td>
                        <td>
                            @if($item['deskripsi'])
                                {{ $item['deskripsi'] }}
                            @else
                                <span class="kosong">Tidak ada aktivitas tercatat</span>
                            @endif
                        </td>
                        <td>
                            @if(!empty($item['images']))
                                <div class="entry-images">
                                    @foreach($item['images'] as $img)
                                        <img src="data:{{ $img['mime'] }};base64,{{ $img['base64'] }}">
                                    @endforeach
                                </div>
                            @else
                                <span class="kosong">-</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3">Tidak ada hari kerja pada rentang ini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</body>
</html>