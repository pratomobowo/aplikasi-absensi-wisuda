<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Undangan Wisuda - {{ $mahasiswa->nama }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 10pt;
            line-height: 1.5;
            color: #1a1a1a;
            background: white;
        }

        .page {
            width: 210mm;
            min-height: 297mm;
            padding: 20mm;
            margin: 0 auto;
            background: white;
            position: relative;
        }

        /* Elegant Header */
        .header {
            text-align: center;
            padding-bottom: 20px;
            border-bottom: 3px double #B43237;
            margin-bottom: 25px;
        }

        .header-logo {
            width: 80px;
            height: 80px;
            margin-bottom: 15px;
            object-fit: contain;
        }

        .header-title {
            font-size: 14pt;
            font-weight: 700;
            color: #B43237;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 5px;
        }

        .header-subtitle {
            font-size: 11pt;
            color: #666;
            font-weight: 600;
        }

        .header-date {
            font-size: 10pt;
            color: #888;
            margin-top: 8px;
            font-style: italic;
        }

        /* Main Content */
        .content {
            display: table;
            width: 100%;
            margin-bottom: 25px;
        }

        .content-left {
            display: table-cell;
            width: 60%;
            padding-right: 15px;
            vertical-align: top;
        }

        .content-right {
            display: table-cell;
            width: 40%;
            padding-left: 15px;
            vertical-align: top;
        }

        /* Card Style */
        .card {
            background: #fafafa;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 18px;
            margin-bottom: 15px;
        }

        .card-title {
            font-size: 11pt;
            font-weight: 700;
            color: #B43237;
            margin-bottom: 15px;
            padding-bottom: 8px;
            border-bottom: 2px solid #B43237;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* Student Photo & Info */
        .student-header {
            display: table;
            width: 100%;
            margin-bottom: 15px;
        }

        .student-photo-cell {
            display: table-cell;
            width: 110px;
            vertical-align: top;
            padding-right: 15px;
        }

        .student-photo {
            width: 110px;
            height: 140px;
            object-fit: cover;
            border-radius: 6px;
            border: 2px solid #B43237;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .student-photo-placeholder {
            width: 110px;
            height: 140px;
            background: linear-gradient(135deg, #f5f5f5 0%, #e8e8e8 100%);
            border-radius: 6px;
            border: 2px dashed #ccc;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 9pt;
            color: #999;
            text-align: center;
        }

        .student-info-cell {
            display: table-cell;
            vertical-align: top;
        }

        .info-row {
            margin-bottom: 10px;
        }

        .info-label {
            font-size: 8pt;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 2px;
            font-weight: 600;
        }

        .info-value {
            font-size: 11pt;
            font-weight: 700;
            color: #1a1a1a;
        }

        .info-divider {
            border-top: 1px solid #e0e0e0;
            margin: 8px 0;
        }

        /* Event Info */
        .event-detail {
            margin-bottom: 10px;
        }

        .event-detail:last-child {
            margin-bottom: 0;
        }

        /* QR Section */
        .qr-section {
            background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
            border: 2px solid #B43237;
            border-radius: 10px;
            padding: 25px;
            text-align: center;
            margin-bottom: 20px;
            page-break-inside: avoid;
        }

        .qr-title {
            font-size: 12pt;
            font-weight: 700;
            color: #B43237;
            margin-bottom: 5px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .qr-subtitle {
            font-size: 9pt;
            color: #666;
            margin-bottom: 20px;
            font-style: italic;
        }

        .qr-container {
            background: white;
            border-radius: 10px;
            padding: 20px;
            display: inline-block;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            margin-bottom: 15px;
        }

        .qr-image {
            width: 150px;
            height: 150px;
            display: block;
        }

        .qr-label {
            display: inline-block;
            background: #B43237;
            color: white;
            padding: 8px 20px;
            border-radius: 20px;
            font-size: 10pt;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .qr-note {
            font-size: 9pt;
            color: #666;
        }

        /* Important Notice */
        .notice {
            background: #fffbeb;
            border-left: 4px solid #f59e0b;
            border-radius: 0 8px 8px 0;
            padding: 15px 18px;
            margin-bottom: 20px;
            page-break-inside: avoid;
        }

        .notice-title {
            font-size: 10pt;
            font-weight: 700;
            color: #92400e;
            margin-bottom: 8px;
        }

        .notice-item {
            font-size: 9pt;
            color: #78350f;
            margin-bottom: 4px;
            padding-left: 15px;
            position: relative;
        }

        .notice-item::before {
            content: "•";
            position: absolute;
            left: 0;
            color: #f59e0b;
            font-weight: bold;
        }

        /* Footer */
        .footer {
            text-align: center;
            padding-top: 20px;
            border-top: 1px solid #e0e0e0;
            margin-top: 20px;
        }

        .footer-text {
            font-size: 9pt;
            color: #666;
            margin-bottom: 3px;
        }

        .footer-brand {
            font-size: 10pt;
            font-weight: 700;
            color: #B43237;
        }

        .footer-date {
            font-size: 8pt;
            color: #999;
            margin-top: 8px;
            font-style: italic;
        }

        /* Print Optimization */
        @media print {
            body {
                background: white;
            }
            .page {
                width: 100%;
                padding: 15mm;
                margin: 0;
            }
        }
    </style>
</head>
<body>
    <div class="page">
        <!-- Header -->
        <div class="header">
            <img src="{{ public_path('logo-ewisuda.png') }}" alt="Logo" class="header-logo">
            <div class="header-title">Undangan Wisuda</div>
            <div class="header-subtitle">{{ $event->name }}</div>
            <div class="header-date">{{ $event->date->locale('id')->isoFormat('dddd, D MMMM YYYY') }}</div>
        </div>

        <!-- Main Content -->
        <div class="content">
            <!-- Left: Student Info -->
            <div class="content-left">
                <div class="card">
                    <div class="card-title">Informasi Mahasiswa</div>
                    
                    <div class="student-header">
                        <div class="student-photo-cell">
                            @if($mahasiswa->foto_wisuda && file_exists(public_path('storage/graduation-photos/' . $mahasiswa->foto_wisuda)))
                                <img src="{{ public_path('storage/graduation-photos/' . $mahasiswa->foto_wisuda) }}" alt="Foto" class="student-photo">
                            @else
                                <div class="student-photo-placeholder">
                                    Foto
                                    <br>Belum
                                    <br>Tersedia
                                </div>
                            @endif
                        </div>
                        <div class="student-info-cell">
                            <div class="info-row">
                                <div class="info-label">Nama Lengkap</div>
                                <div class="info-value">{{ $mahasiswa->nama }}</div>
                            </div>
                            
                            <div class="info-divider"></div>
                            
                            <div class="info-row">
                                <div class="info-label">Nomor Pokok Mahasiswa</div>
                                <div class="info-value">{{ $mahasiswa->npm }}</div>
                            </div>
                            
                            <div class="info-divider"></div>
                            
                            <div class="info-row">
                                <div class="info-label">Program Studi</div>
                                <div class="info-value">{{ $mahasiswa->program_studi }}</div>
                            </div>
                            
                            <div class="info-divider"></div>
                            
                            <div class="info-row">
                                <div class="info-label">Nomor Kursi</div>
                                <div class="info-value">{{ $mahasiswa->nomor_kursi ?? 'Belum ditentukan' }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right: Event Info -->
            <div class="content-right">
                <div class="card">
                    <div class="card-title">Detail Acara</div>
                    
                    <div class="event-detail">
                        <div class="info-label">Tanggal</div>
                        <div class="info-value" style="font-size: 10pt;">{{ $event->date->locale('id')->isoFormat('D MMMM YYYY') }}</div>
                        <div style="font-size: 9pt; color: #666; font-style: italic;">{{ $event->date->locale('id')->isoFormat('dddd') }}</div>
                    </div>
                    
                    <div class="info-divider"></div>
                    
                    <div class="event-detail">
                        <div class="info-label">Waktu</div>
                        <div class="info-value" style="font-size: 10pt;">{{ \Carbon\Carbon::parse($event->time)->format('H:i') }} WIB</div>
                    </div>
                    
                    <div class="info-divider"></div>
                    
                    <div class="event-detail">
                        <div class="info-label">Lokasi</div>
                        <div class="info-value" style="font-size: 10pt;">{{ $event->location_name }}</div>
                        <div style="font-size: 8pt; color: #888; margin-top: 3px;">{{ $event->location_address }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- QR Code Section -->
        <div class="qr-section">
            <div class="qr-title">QR Code Absensi &amp; Konsumsi</div>
            <div class="qr-subtitle">Tunjukkan QR Code ini kepada panitia saat acara berlangsung</div>
            
            <div class="qr-container">
                <img src="{{ $qrCodes['mahasiswa'] }}" alt="QR Code" class="qr-image">
            </div>
            
            <div class="qr-label">Wisudawan</div>
            <div class="qr-note">Scan pagi untuk absensi | Scan sore untuk konsumsi</div>
        </div>

        <!-- Important Notice -->
        <div class="notice">
            <div class="notice-title">Penting untuk Diperhatikan</div>
            <div class="notice-item">Simpan undangan ini atau unduh PDF untuk dibawa saat acara wisuda</div>
            <div class="notice-item">QR Code yang sama akan discan 2 kali: pagi untuk absensi, sore untuk konsumsi</div>
            <div class="notice-item">Pastikan membawa undangan ini saat menghadiri acara</div>
            <div class="notice-item">Kehilangan undangan dapat menyebabkan keterlambatan proses absensi</div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <div class="footer-text">&copy; {{ date('Y') }} Sistem Absensi Wisuda Digital</div>
            <div class="footer-brand">Universitas Sanggabuana YPKP</div>
            <div class="footer-date">Dicetak pada: {{ now()->isoFormat('dddd, D MMMM YYYY HH:mm') }} WIB</div>
        </div>
    </div>
</body>
</html>