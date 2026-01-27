<!DOCTYPE html>
<html>

<head>
    <title>Dashboard Export</title>
    <style>
        body {
            font-family: sans-serif;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .header h1 {
            margin: 0;
            font-size: 24px;
            color: #333;
        }

        .header p {
            margin: 5px 0 0;
            color: #666;
            font-size: 14px;
        }

        /* Stats Cards */
        .stats-row {
            width: 100%;
            text-align: center;
            margin-bottom: 15px;
            /* Vertical gap */
        }

        .stats-card {
            display: inline-block;
            padding: 15px 10px;
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            text-align: center;
            vertical-align: top;
            margin: 0 8px;
        }

        .stats-card.highlight {
            background: #f8fafc;
            border: 1px solid #cbd5e1;
        }

        .stats-title {
            font-size: 9px;
            font-weight: 800;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 5px;
        }

        .stats-value {
            font-size: 28px;
            font-weight: 800;
            line-height: 1.2;
            margin-bottom: 2px;
        }

        .stats-sub {
            font-size: 7px;
            font-weight: 700;
            color: #94a3b8;
            letter-spacing: 1px;
        }

        /* Tables for Charts */
        .chart-section {
            margin-bottom: 30px;
            page-break-inside: avoid;
        }

        .section-title {
            font-size: 14px;
            font-weight: 800;
            /* Bold like headers */
            text-transform: uppercase;
            margin-bottom: 15px;
            border-bottom: 2px solid #eee;
            padding-bottom: 5px;
            color: #444;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th {
            text-align: left;
            font-size: 11px;
            /* Match rekap header size roughly */
            font-weight: 800;
            /* Bold */
            text-transform: uppercase;
            color: #444;
            /* Darker like rekap */
            padding: 8px 5px;
            background-color: #f9fafb;
            /* Slight bg for header */
            border-bottom: 2px solid #e2e8f0;
        }

        td {
            padding: 8px 5px;
            border-bottom: 1px solid #eee;
            vertical-align: middle;
        }

        .rank {
            width: 30px;
            font-weight: bold;
            color: #888;
            font-size: 12px;
        }

        .name {
            font-size: 12px;
            font-weight: bold;
            color: #333;
        }

        .unit {
            font-size: 10px;
            color: #888;
            font-style: italic;
        }

        .bar-container {
            width: 100%;
            height: 6px;
            background: #f0f0f0;
            border-radius: 3px;
            margin-top: 5px;
            overflow: hidden;
        }

        .bar-fill {
            height: 100%;
            border-radius: 3px;
        }

        .count {
            text-align: right;
            font-size: 14px;
            font-weight: bold;
            color: #333;
        }

        .count-label {
            font-size: 8px;
            color: #888;
            text-transform: uppercase;
        }

        .tk-color {
            background-color: #ef4444;
        }

        .tm-color {
            background-color: #f59e0b;
        }

        .pc-color {
            background-color: #10b981;
        }

        .peserta-color {
            background-color: #3b82f6;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>Laporan Statistik Absensi</h1>
        <p>{{ $kedeputianLabel }} &mdash; Periode: {{ $periodLabel }}</p>
    </div>

    <!-- Stats Row 1: Total Peserta (Centered) -->
    <div class="stats-row">
        <div class="stats-card highlight" style="width: 30%;">
            <div class="stats-title">Total Peserta</div>
            <div class="stats-value" style="color: #2563eb">{{ $stats['total_peserta'] }}</div>
            <div class="stats-sub">PESERTA AKTIF</div>
        </div>
    </div>

    <!-- Stats Row 2: TK, TM, PC (Centered) -->
    <div class="stats-row" style="margin-bottom: 40px;">
        <div class="stats-card" style="width: 28%;">
            <div class="stats-title">Total TK</div>
            <div class="stats-value" style="color: #dc2626">{{ $stats['total_tk'] }}</div>
            <div class="stats-sub">TANPA KETERANGAN</div>
        </div>
        <div class="stats-card" style="width: 28%;">
            <div class="stats-title">Total TM</div>
            <div class="stats-value" style="color: #d97706">{{ $stats['total_tm'] }}</div>
            <div class="stats-sub">TERLAMBAT MASUK</div>
        </div>
        <div class="stats-card" style="width: 28%;">
            <div class="stats-title">Total PC</div>
            <div class="stats-value" style="color: #059669">{{ $stats['total_pc'] }}</div>
            <div class="stats-sub">PULANG CEPAT</div>
        </div>
    </div>

    <!-- Top 5 TK -->
    <div class="chart-section">
        <div class="section-title" style="color: #dc2626">Top 5 Tanpa Keterangan (TK)</div>
        @if($topTK->count() > 0)
        <table>
            @foreach($topTK as $row)
            @php $percent = ($row->count / $maxTK) * 100; @endphp
            <tr>
                <td class="rank">{{ $loop->iteration }}</td>
                <td style="width: 60%">
                    <div class="name">{{ $row->pesertaMagang->nama }}</div>
                    <div class="unit">{{ $row->pesertaMagang->kedeputian->nama }}</div>
                    <div class="bar-container">
                        <div class="bar-fill tk-color" style="width: {{ $percent }}%"></div>
                    </div>
                </td>
                <td class="count">
                    {{ $row->count }} <span class="count-label">Kali</span>
                </td>
            </tr>
            @endforeach
        </table>
        @else
        <p style="text-align: center; color: #999; font-style: italic; padding: 20px;">Tidak ada data TK.</p>
        @endif
    </div>

    <!-- Top 5 TM -->
    <div class="chart-section">
        <div class="section-title" style="color: #d97706">Top 5 Terlambat Masuk (TM)</div>
        @if($topTM->count() > 0)
        <table>
            @foreach($topTM as $row)
            @php $percent = ($row->count / $maxTM) * 100; @endphp
            <tr>
                <td class="rank">{{ $loop->iteration }}</td>
                <td style="width: 60%">
                    <div class="name">{{ $row->pesertaMagang->nama }}</div>
                    <div class="unit">{{ $row->pesertaMagang->kedeputian->nama }}</div>
                    <div class="bar-container">
                        <div class="bar-fill tm-color" style="width: {{ $percent }}%"></div>
                    </div>
                </td>
                <td class="count">
                    {{ $row->count }} <span class="count-label">Kali</span>
                </td>
            </tr>
            @endforeach
        </table>
        @else
        <p style="text-align: center; color: #999; font-style: italic; padding: 20px;">Tidak ada data TM.</p>
        @endif
    </div>

    <!-- Detail Tables: TK -->
    @if(isset($tkList) && $tkList->isNotEmpty())
    <div style="page-break-before: always;">
        <div class="header">
            <h3 style="color: #dc2626; border-bottom: 2px solid #dc2626; padding-bottom: 10px; display: inline-block;">
                DETAIL DATA TANPA KETERANGAN (TK)
            </h3>
        </div>
        <table>
            <thead>
                <tr style="background: #fef2f2;">
                    <th style="width: 50px; text-align: center;">No</th>
                    <th>Nama Peserta</th>
                    <th>Unit Kedeputian</th>
                    <th style="text-align: right;">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($tkList as $row)
                <tr>
                    <td style="text-align: center; color: #888;">{{ $loop->iteration }}</td>
                    <td class="name">{{ $row->pesertaMagang->nama }}</td>
                    <td class="unit">{{ $row->pesertaMagang->kedeputian->nama }}</td>
                    <td style="text-align: right; font-weight: bold; color: #dc2626;">{{ $row->total }} Kali</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <!-- Detail Tables: TM -->
    @if(isset($tmList) && $tmList->isNotEmpty())
    <div style="page-break-before: always;">
        <div class="header">
            <h3 style="color: #d97706; border-bottom: 2px solid #d97706; padding-bottom: 10px; display: inline-block;">
                DETAIL DATA TERLAMBAT MASUK (TM)
            </h3>
        </div>
        <table>
            <thead>
                <tr style="background: #fffbeb;">
                    <th style="width: 50px; text-align: center;">No</th>
                    <th>Nama Peserta</th>
                    <th>Unit Kedeputian</th>
                    <th style="text-align: right;">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($tmList as $row)
                <tr>
                    <td style="text-align: center; color: #888;">{{ $loop->iteration }}</td>
                    <td class="name">{{ $row->pesertaMagang->nama }}</td>
                    <td class="unit">{{ $row->pesertaMagang->kedeputian->nama }}</td>
                    <td style="text-align: right; font-weight: bold; color: #d97706;">{{ $row->total }} Kali</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <!-- Detail Tables: PC -->
    @if(isset($pcList) && $pcList->isNotEmpty())
    <div style="page-break-before: always;">
        <div class="header">
            <h3 style="color: #059669; border-bottom: 2px solid #059669; padding-bottom: 10px; display: inline-block;">
                DETAIL DATA PULANG CEPAT (PC)
            </h3>
        </div>
        <table>
            <thead>
                <tr style="background: #ecfdf5;">
                    <th style="width: 50px; text-align: center;">No</th>
                    <th>Nama Peserta</th>
                    <th>Unit Kedeputian</th>
                    <th style="text-align: right;">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pcList as $row)
                <tr>
                    <td style="text-align: center; color: #888;">{{ $loop->iteration }}</td>
                    <td class="name">{{ $row->pesertaMagang->nama }}</td>
                    <td class="unit">{{ $row->pesertaMagang->kedeputian->nama }}</td>
                    <td style="text-align: right; font-weight: bold; color: #059669;">{{ $row->total }} Kali</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <div style="margin-top: 50px; text-align: center; font-size: 10px; color: #aaa;">
        Dicetak pada: {{ date('d-m-Y H:i') }} oleh Sistem Informasi Absensi Magang
    </div>
</body>

</html>