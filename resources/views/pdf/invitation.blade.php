<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Undangan Wisuda</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            font-size: 11pt;
            line-height: 1.5;
            color: #1a1a1a;
            background: white;
        }

        .page {
            width: 210mm;
            min-height: 297mm;
            padding: 12mm;
            margin: 0 auto;
            background: white;
            page-break-inside: avoid;
        }

        /* Header */
        .header {
            background: #B43237;
            color: white;
            padding: 15px;
            border-radius: 10px;
            text-align: center;
            margin-bottom: 15px;
        }

        .header img {
            max-width: 50%;
            height: auto;
            margin-bottom: 8px;
        }

        .header-label {
            font-size: 9pt;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: #000000;
            margin-bottom: 5px;
        }

        .header-title {
            font-size: 14pt;
            font-weight: 700;
            margin-bottom: 3px;
            color: #000000;
        }

        .header-date {
            font-size: 9pt;
            color: #000000;
        }

        /* Main Content */
        .content {
            display: flex;
            gap: 10px;
            margin-bottom: 12px;
        }

        .content-left {
            flex: 1.8;
        }

        .content-right {
            flex: 1;
        }

        /* Card */
        .card {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 12px;
            height: 100%;
        }

        .card-title {
            font-size: 11pt;
            font-weight: 700;
            color: #B43237;
            margin-bottom: 10px;
            padding-bottom: 6px;
            border-bottom: 2px solid #B43237;
        }

        /* Student Header */
        .student-header {
            display: flex;
            gap: 10px;
        }

        .student-photo {
            width: 80px;
            height: 100px;
            object-fit: cover;
            border-radius: 6px;
            border: 2px solid #B43237;
        }

        .student-photo-placeholder {
            width: 80px;
            height: 100px;
            background: #f3f4f6;
            border: 2px dashed #d1d5db;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 8pt;
            color: #9ca3af;
            text-align: center;
        }

        .student-info {
            flex: 1;
        }

        .info-row {
            margin-bottom: 6px;
            padding-bottom: 6px;
            border-bottom: 1px solid #f3f4f6;
        }

        .info-row:last-child {
            margin-bottom: 0;
            padding-bottom: 0;
            border-bottom: none;
        }

        .info-label {
            font-size: 7pt;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 2px;
            font-weight: 600;
        }

        .info-value {
            font-size: 10pt;
            font-weight: 700;
            color: #111827;
        }

        /* Event Info */
        .event-row {
            margin-bottom: 8px;
            padding-bottom: 8px;
            border-bottom: 1px solid #f3f4f6;
        }

        .event-row:last-child {
            margin-bottom: 0;
            padding-bottom: 0;
            border-bottom: none;
        }

        .event-day {
            font-size: 8pt;
            color: #6b7280;
            font-style: italic;
            margin-top: 2px;
        }

        /* QR Section */
        .qr-section {
            background: #fef2f2;
            border: 2px solid #B43237;
            border-radius: 10px;
            padding: 15px;
            text-align: center;
            margin-bottom: 12px;
        }

        .qr-title {
            font-size: 12pt;
            font-weight: 700;
            color: #B43237;
            margin-bottom: 3px;
        }

        .qr-subtitle {
            font-size: 8pt;
            color: #6b7280;
            margin-bottom: 10px;
        }

        .qr-container {
            background: white;
            border-radius: 8px;
            padding: 12px;
            display: inline-block;
            margin-bottom: 8px;
        }

        .qr-image {
            width: 120px;
            height: 120px;
            display: block;
        }

        .qr-label {
            display: inline-block;
            background: #B43237;
            color: white;
            padding: 5px 20px;
            border-radius: 15px;
            font-size: 9pt;
            font-weight: 700;
            margin-bottom: 3px;
        }

        .qr-note {
            font-size: 8pt;
            color: #6b7280;
        }

        /* Notice */
        .notice {
            background: #fffbeb;
            border-left: 3px solid #f59e0b;
            border-radius: 0 6px 6px 0;
            padding: 10px 12px;
            margin-bottom: 12px;
        }

        .notice-title {
            font-size: 9pt;
            font-weight: 700;
            color: #92400e;
            margin-bottom: 5px;
        }

        .notice-item {
            font-size: 8pt;
            color: #78350f;
            margin-bottom: 2px;
            padding-left: 10px;
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
            padding-top: 10px;
            border-top: 1px solid #e5e7eb;
        }

        .footer-brand {
            font-size: 9pt;
            font-weight: 700;
            color: #B43237;
            margin-bottom: 2px;
        }

        .footer-text {
            font-size: 8pt;
            color: #6b7280;
        }
    </style>
</head>
<body>
    <div class="page">
        <!-- Header -->
        <div class="header">
            <img src="{{ public_path('logo-ewisuda.png') }}" alt="Logo">
            <div class="header-label">Undangan Wisuda</div>
            <div class="header-title">{{ $event->name }}</div>
            <div class="header-date">{{ $event->date->locale('id')->isoFormat('dddd, D MMMM YYYY') }}</div>
        </div>

        <!-- Main Content -->
        <div class="content">
            <!-- Left: Student Info -->
            <div class="content-left">
                <div class="card">
                    <div class="card-title">Informasi Mahasiswa</div>
                    
                    <div class="student-header">
                        @if($mahasiswa->foto_wisuda && file_exists(public_path('storage/graduation-photos/' . $mahasiswa->foto_wisuda)))
                            <img src="{{ public_path('storage/graduation-photos/' . $mahasiswa->foto_wisuda) }}" alt="Foto" class="student-photo">
                        @else
                            <div class="student-photo-placeholder">
                                Foto Belum
                                <br>Tersedia
                            </div>
                        @endif
                        
                        <div class="student-info">
                            <div class="info-row">
                                <div class="info-label">Nama Lengkap</div>
                                <div class="info-value">{{ $mahasiswa->nama }}</div>
                            </div>

                            <div class="info-row">
                                <div class="info-label">Nomor Pokok Mahasiswa</div>
                                <div class="info-value">{{ $mahasiswa->npm }}</div>
                            </div>

                            <div class="info-row">
                                <div class="info-label">Program Studi</div>
                                <div class="info-value">{{ $mahasiswa->program_studi }}</div>
                            </div>

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
                    
                    <div class="event-row">
                        <div class="info-label">Tanggal</div>
                        <div class="info-value">{{ $event->date->locale('id')->isoFormat('D MMMM YYYY') }}</div>
                        <div class="event-day">{{ $event->date->locale('id')->isoFormat('dddd') }}</div>
                    </div>

                    <div class="event-row">
                        <div class="info-label">Waktu</div>
                        <div class="info-value">{{ \Carbon\Carbon::parse($event->time)->format('H:i') }} WIB</div>
                    </div>

                    <div class="event-row">
                        <div class="info-label">Lokasi</div>
                        <div class="info-value">{{ $event->location_name }}</div>
                        <div style="font-size: 9pt; color: #6b7280;">{{ $event->location_address }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- QR Code -->
        <div class="qr-section">
            <div class="qr-title">QR Code Absensi &amp; Konsumsi</div>
            <div class="qr-subtitle">Tunjukkan QR Code ini kepada panitia saat acara berlangsung</div>
            
            <div class="qr-container">
                <img src="{{ $qrCodes['mahasiswa'] }}" alt="QR Code" class="qr-image">
            </div>
            
            <div class="qr-note">Scan pagi untuk absensi | Scan sore untuk konsumsi</div>
        </div>

        <!-- Notice -->
        <div class="notice">
            <div class="notice-title">Penting untuk Diperhatikan</div>
            <div class="notice-item">Simpan undangan ini untuk dibawa saat acara wisuda</div>
            <div class="notice-item">QR Code yang sama akan discan 2 kali: pagi untuk absensi, sore untuk konsumsi</div>
            <div class="notice-item">Pastikan membawa undangan ini saat menghadiri acara</div>
            <div class="notice-item">Kehilangan undangan dapat menyebabkan keterlambatan proses absensi</div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <div class="footer-brand">Universitas Sanggabuana YPKP</div>
            <div class="footer-text">&copy; {{ date('Y') }} Sistem Absensi Wisuda Digital</div>
        </div>
    </div>
</body>
</html>