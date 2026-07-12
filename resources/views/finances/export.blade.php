<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Kas Keuangan - {{ $team->name }}</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body {
            font-family: 'Outfit', sans-serif;
            color: #1e293b;
            background-color: #ffffff;
            margin: 0;
            padding: 40px;
            font-size: 14px;
            line-height: 1.5;
        }
        .container {
            max-width: 900px;
            margin: 0 auto;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-b: 2px solid #e2e8f0;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .logo-section h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: -0.5px;
        }
        .logo-section p {
            margin: 4px 0 0 0;
            font-size: 12px;
            color: #64748b;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .meta-section {
            text-align: right;
        }
        .meta-section p {
            margin: 2px 0;
            font-size: 12px;
            color: #64748b;
        }
        .meta-section strong {
            color: #0f172a;
        }
        .stats-grid {
            display: grid;
            grid-template-cols: repeat(3, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-card {
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            padding: 16px 20px;
            background-color: #f8fafc;
        }
        .stat-title {
            font-size: 11px;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 6px;
        }
        .stat-value {
            font-size: 18px;
            font-weight: 800;
            color: #0f172a;
        }
        .stat-value.income {
            color: #10b981;
        }
        .stat-value.expense {
            color: #ef4444;
        }
        .stat-value.balance {
            color: #0284c7;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 40px;
        }
        th {
            background-color: #f1f5f9;
            border-bottom: 2px solid #cbd5e1;
            padding: 12px 16px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            color: #475569;
            text-align: left;
        }
        td {
            padding: 12px 16px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 13px;
        }
        tr:nth-child(even) {
            background-color: #fafafa;
        }
        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 6px;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
        }
        .badge-income {
            background-color: #dcfce7;
            color: #15803d;
            border: 1px solid #bbf7d0;
        }
        .badge-expense {
            background-color: #fee2e2;
            color: #b91c1c;
            border: 1px solid #fecaca;
        }
        .text-right {
            text-align: right;
        }
        .actions-bar {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 15px 20px;
            border-radius: 16px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }
        .btn {
            background-color: #10b981;
            color: #ffffff;
            border: none;
            padding: 8px 16px;
            font-size: 12px;
            font-weight: 700;
            border-radius: 10px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            text-decoration: none;
            transition: all 0.2s;
        }
        .btn:hover {
            background-color: #059669;
        }
        .btn-secondary {
            background-color: #ffffff;
            color: #475569;
            border: 1px solid #cbd5e1;
        }
        .btn-secondary:hover {
            background-color: #f1f5f9;
        }
        .footer-note {
            text-align: center;
            font-size: 11px;
            color: #94a3b8;
            margin-top: 50px;
            border-top: 1px solid #e2e8f0;
            padding-top: 20px;
        }
        
        @media print {
            .actions-bar {
                display: none;
            }
            .stats-grid {
                display: none;
            }
            body {
                padding: 30px;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <!-- Action Bar (hidden when printing) -->
    <div class="actions-bar">
        <div>
            <span style="font-weight: 700; color: #475569;"><i class="fa-solid fa-file-pdf text-emerald-600 mr-1.5"></i>Laporan Siap Cetak</span>
            <p style="margin: 3px 0 0 0; font-size: 11px; color: #64748b;">Gunakan dialog browser untuk menyimpan sebagai PDF atau cetak fisik.</p>
        </div>
        <div style="display: flex; gap: 10px;">
            <button onclick="window.print()" class="btn">
                <i class="fa-solid fa-print"></i> Cetak / Simpan PDF
            </button>
            <button onclick="window.close()" class="btn btn-secondary">
                Tutup Halaman
            </button>
        </div>
    </div>

    <!-- Document Header -->
    <div class="header">
        <div class="logo-section">
            <h1>{{ \App\Models\Setting::get('web_name', 'FutsalHub') }}</h1>
            <p>Laporan Keuangan Kas Tim</p>
        </div>
        <div class="meta-section">
            <p>Tim: <strong>{{ $team->name }}</strong></p>
            <p>Tanggal Cetak: <strong>{{ now()->isoFormat('D MMMM YYYY') }}</strong></p>
            <p>Periode: <strong>Seluruh Transaksi</strong></p>
        </div>
    </div>

    <!-- Financial Stats Cards -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-title">Total Pemasukan</div>
            <div class="stat-value income">Rp {{ number_format($income, 0, ',', '.') }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-title">Total Pengeluaran</div>
            <div class="stat-value expense">Rp {{ number_format($expense, 0, ',', '.') }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-title">Saldo Akhir Kas</div>
            <div class="stat-value balance">Rp {{ number_format($balance, 0, ',', '.') }}</div>
        </div>
    </div>

    <!-- Transactions Table -->
    <table>
        <thead>
            <tr>
                <th style="width: 50px; text-align: center;">No</th>
                <th style="width: 110px;">Tanggal</th>
                <th style="width: 100px;">Jenis</th>
                <th style="width: 140px;">Kategori</th>
                <th>Keterangan / Deskripsi</th>
                <th style="width: 140px;" class="text-right">Jumlah (Rupiah)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($finances as $idx => $f)
                <tr>
                    <td style="text-align: center; color: #64748b; font-weight: 500;">{{ $idx + 1 }}</td>
                    <td style="white-space: nowrap;">{{ \Carbon\Carbon::parse($f->date)->isoFormat('D MMM YYYY') }}</td>
                    <td>
                        <span class="badge {{ $f->type === 'Pemasukan' ? 'badge-income' : 'badge-expense' }}">
                            {{ $f->type }}
                        </span>
                    </td>
                    <td style="font-weight: 600; color: #475569;">{{ $f->category }}</td>
                    <td style="color: #334155;">{{ $f->description }}</td>
                    <td class="text-right" style="font-weight: 700; color: {{ $f->type === 'Pemasukan' ? '#10b981' : '#ef4444' }}">
                        {{ $f->type === 'Pemasukan' ? '+' : '-' }} Rp {{ number_format($f->amount, 0, ',', '.') }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center; color: #94a3b8; padding: 30px;">
                        Tidak ada transaksi kas keuangan tercatat.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Verification / Signatures Section -->
    <div style="margin-top: 60px; display: flex; justify-content: space-between; font-size: 12px; color: #475569;">
        <div style="width: 200px; text-align: center;">
            <p>Dilaporkan Oleh,</p>
            <div style="height: 70px;"></div>
            <p style="border-top: 1px solid #cbd5e1; padding-top: 6px; font-weight: 700; color: #0f172a;">{{ Auth::user()->name }}</p>
            <p style="font-size: 10px; color: #94a3b8; text-transform: uppercase; margin-top: 2px;">Manajemen Tim</p>
        </div>
        <div style="width: 200px; text-align: center;">
            <p>Mengetahui/Menyetujui,</p>
            <div style="height: 70px;"></div>
            <p style="border-top: 1px solid #cbd5e1; padding-top: 6px; font-weight: 700; color: #0f172a;">{{ $team->name }}</p>
            <p style="font-size: 10px; color: #94a3b8; text-transform: uppercase; margin-top: 2px;">Direksi / Official Klub</p>
        </div>
    </div>

    <!-- System footer tag -->
    <div class="footer-note">
        <p>Laporan kas keuangan ini diunduh secara otomatis melalui sistem {{ \App\Models\Setting::get('web_name', 'FutsalHub') }}.</p>
        <p style="margin-top: 4px; font-size: 10px;">ID Tim: {{ $team->id }} | Keamanan Data Terisolasi (Multi-Tenant SaaS)</p>
    </div>
</div>

<script>
    // Trigger dialog print otomatis ketika halaman selesai memuat
    window.onload = function() {
        setTimeout(function() {
            window.print();
        }, 300);
    }
</script>
</body>
</html>
