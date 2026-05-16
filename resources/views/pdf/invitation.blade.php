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
            padding: 15mm;
            margin: 0 auto;
            background: white;
        }

        /* Header */
        .header {
            background: linear-gradient(135deg, #B43237 0%, #8B251D 100%);
            color: white;
            padding: 25px;
            border-radius: 12px;
            text-align: center;
            margin-bottom: 20px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        .header img {
            width: 60px;
            height: 60px;
            margin-bottom: 10px;
            filter: brightness(0) invert(1);
        }

        .header-label {
            font-size: 9pt;
            text-transform: uppercase;
            letter-spacing: 2px;
            opacity: 0.9;
            margin-bottom: 5px;
        }

        .header-title {
            font-size: 16pt;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .header-date {
            font-size: 10pt;
            opacity: 0.9;
        }

        /* Main Content */
        .content {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
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
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            height: 100%;
        }

        .card-title {
            font-size: 12pt;
            font-weight: 700;
            color: #B43237;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #B43237;
        }

        /* Student Header */
        .student-header {
            display: flex;
            gap: 15px;
        }

        .student-photo {
            width: 100px;
            height: 130px;
            object-fit: cover;
            border-radius: 8px;
            border: 2px solid #B43237;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .student-photo-placeholder {
            width: 100px;
            height: 130px;
            background: #f3f4f6;
            border: 2px dashed #d1d5db;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 9pt;
            color: #9ca3af;
            text-align: center;
        }

        .student-info {
            flex: 1;
        }

        .info-row {
            margin-bottom: 12px;
            padding-bottom: 12px;
            border-bottom: 1px solid #f3f4f6;
        }

        .info-row:last-child {
            margin-bottom: 0;
            padding-bottom: 0;
            border-bottom: none;
        }

        .info-label {
            font-size: 8pt;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 3px;
            font-weight: 600;
        }

        .info-value {
            font-size: 11pt;
            font-weight: 700;
            color: #111827;
        }

        /* Event Info */
        .event-row {
            margin-bottom: 12px;
            padding-bottom: 12px;
            border-bottom: 1px solid #f3f4f6;
        }

        .event-row:last-child {
            margin-bottom: 0;
            padding-bottom: 0;
            border-bottom: none;
        }

        .event-day {
            font-size: 9pt;
            color: #6b7280;
            font-style: italic;
            margin-top: 2px;
        }

        /* QR Section */
        .qr-section {
            background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
            border: 2px solid #B43237;
            border-radius: 12px;
            padding: 25px;
            text-align: center;
            margin-bottom: 20px;
        }

        .qr-title {
            font-size: 13pt;
            font-weight: 700;
            color: #B43237;
            margin-bottom: 5px;
        }

        .qr-subtitle {
            font-size: 9pt;
            color: #6b7280;
            margin-bottom: 20px;
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
            padding: 8px 25px;
            border-radius: 20px;
            font-size: 10pt;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .qr-note {
            font-size: 9pt;
            color: #6b7280;
        }

        /* Notice */
        .notice {
            background: #fffbeb;
            border-left: 4px solid #f59e0b;
            border-radius: 0 8px 8px 0;
            padding: 15px 18px;
            margin-bottom: 20px;
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
            padding-left: 12px;
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
            border-top: 1px solid #e5e7eb;
        }

        .footer-brand {
            font-size: 10pt;
            font-weight: 700;
            color: #B43237;
            margin-bottom: 3px;
        }

        .footer-text {
            font-size: 9pt;
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
            
            <div class="qr-label">Wisudawan</div>
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