<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Undangan Wisuda</title>
    <style>
        * {
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Helvetica, Arial, sans-serif;
            font-size: 9pt;
            line-height: 1.3;
            color: #333;
        }

        .container {
            width: 100%;
            padding: 10px;
        }

        /* Compact Header */
        .header {
            background-color: #B43237;
            color: white;
            padding: 12px;
            text-align: center;
            margin-bottom: 12px;
            border-radius: 6px;
        }

        .header img {
            width: 40px;
            height: 40px;
            margin-bottom: 5px;
        }

        .header-small {
            font-size: 7pt;
            text-transform: uppercase;
            letter-spacing: 1px;
            opacity: 0.9;
        }

        .header-title {
            font-size: 12pt;
            font-weight: bold;
            margin: 3px 0;
        }

        .header-date {
            font-size: 8pt;
            opacity: 0.9;
        }

        /* Main Table */
        .main-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }

        .main-table td {
            vertical-align: top;
        }

        .left-cell {
            width: 60%;
            padding-right: 8px;
        }

        .right-cell {
            width: 40%;
            padding-left: 8px;
        }

        /* Compact Card */
        .card {
            border: 1px solid #ddd;
            border-radius: 6px;
            padding: 10px;
            background: #fafafa;
        }

        .card-title {
            font-size: 10pt;
            font-weight: bold;
            color: #B43237;
            margin-bottom: 8px;
            padding-bottom: 5px;
            border-bottom: 2px solid #B43237;
        }

        /* Photo */
        .photo {
            width: 70px;
            height: 90px;
            object-fit: cover;
            border-radius: 4px;
            border: 2px solid #B43237;
        }

        .photo-placeholder {
            width: 70px;
            height: 90px;
            background: #f0f0f0;
            border: 2px dashed #ccc;
            border-radius: 4px;
            text-align: center;
            font-size: 7pt;
            color: #999;
            padding-top: 25px;
        }

        /* Info */
        .info-label {
            font-size: 6pt;
            color: #666;
            text-transform: uppercase;
            margin-bottom: 1px;
            font-weight: bold;
        }

        .info-value {
            font-size: 9pt;
            font-weight: bold;
            color: #000;
        }

        .info-row {
            margin-bottom: 5px;
        }

        /* QR Section */
        .qr-section {
            background: #fef2f2;
            border: 2px solid #B43237;
            border-radius: 8px;
            padding: 12px;
            text-align: center;
            margin-bottom: 12px;
        }

        .qr-title {
            font-size: 10pt;
            font-weight: bold;
            color: #B43237;
            margin-bottom: 3px;
        }

        .qr-subtitle {
            font-size: 8pt;
            color: #666;
            margin-bottom: 8px;
        }

        .qr-image {
            width: 100px;
            height: 100px;
            margin-bottom: 5px;
        }

        .qr-badge {
            background: #B43237;
            color: white;
            padding: 3px 15px;
            border-radius: 12px;
            font-size: 8pt;
            font-weight: bold;
            display: inline-block;
            margin-bottom: 3px;
        }

        .qr-note {
            font-size: 7pt;
            color: #666;
        }

        /* Compact Notice */
        .notice {
            background: #fffbeb;
            border-left: 3px solid #f59e0b;
            padding: 8px 10px;
            margin-bottom: 10px;
            border-radius: 0 4px 4px 0;
        }

        .notice-title {
            font-size: 8pt;
            font-weight: bold;
            color: #92400e;
            margin-bottom: 3px;
        }

        .notice-item {
            font-size: 7pt;
            color: #78350f;
            margin-bottom: 2px;
        }

        /* Compact Footer */
        .footer {
            text-align: center;
            padding-top: 8px;
            border-top: 1px solid #eee;
        }

        .footer-brand {
            font-size: 8pt;
            font-weight: bold;
            color: #B43237;
        }

        .footer-text {
            font-size: 7pt;
            color: #666;
        }

        .footer-date {
            font-size: 6pt;
            color: #999;
            margin-top: 3px;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <img src="{{ public_path('logo-ewisuda.png') }}" alt="Logo">
            <div class="header-small">Undangan Wisuda</div>
            <div class="header-title">{{ $event->name }}</div>
            <div class="header-date">{{ $event->date->locale('id')->isoFormat('dddd, D MMMM YYYY') }}</div>
        </div>

        <!-- Content -->
        <table class="main-table">
            <tr>
                <!-- Left: Student Info -->
                <td class="left-cell">
                    <div class="card">
                        <div class="card-title">Informasi Mahasiswa</div>
                        
                        <table style="width: 100%; border-collapse: collapse;">
                            <tr>
                                <td style="width: 80px; vertical-align: top; padding-right: 10px;">
                                    @if($mahasiswa->foto_wisuda && file_exists(public_path('storage/graduation-photos/' . $mahasiswa->foto_wisuda)))
                                        <img src="{{ public_path('storage/graduation-photos/' . $mahasiswa->foto_wisuda) }}" alt="Foto" class="photo">
                                    @else
                                        <div class="photo-placeholder"><br><br>Foto Belum<br>Tersedia</div>
                                    @endif
                                </td>
                                <td style="vertical-align: top;">
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
                                </td>
                            </tr>
                        </table>
                    </div>
                </td>

                <!-- Right: Event Info -->
                <td class="right-cell">
                    <div class="card">
                        <div class="card-title">Detail Acara</div>
                        
                        <div class="info-row">
                            <div class="info-label">Tanggal</div>
                            <div class="info-value">{{ $event->date->locale('id')->isoFormat('D MMMM YYYY') }}</div>
                            <div style="font-size: 7pt; color: #666;">{{ $event->date->locale('id')->isoFormat('dddd') }}</div>
                        </div>
                        
                        <div class="info-row">
                            <div class="info-label">Waktu</div>
                            <div class="info-value">{{ \Carbon\Carbon::parse($event->time)->format('H:i') }} WIB</div>
                        </div>
                        
                        <div class="info-row">
                            <div class="info-label">Lokasi</div>
                            <div class="info-value" style="font-size: 8pt;">{{ $event->location_name }}</div>
                            <div style="font-size: 6pt; color: #888;">{{ $event->location_address }}</div>
                        </div>
                    </div>
                </td>
            </tr>
        </table>

        <!-- QR Code -->
        <div class="qr-section">
            <div class="qr-title">QR Code Absensi &amp; Konsumsi</div>
            <div class="qr-subtitle">Tunjukkan QR Code ini kepada panitia saat acara berlangsung</div>
            
            <img src="{{ $qrCodes['mahasiswa'] }}" alt="QR Code" class="qr-image">
            
            <div class="qr-badge">Wisudawan</div>
            <div class="qr-note">Scan pagi untuk absensi | Scan sore untuk konsumsi</div>
        </div>

        <!-- Notice -->
        <div class="notice">
            <div class="notice-title">Penting untuk Diperhatikan</div>
            <div class="notice-item">• Simpan undangan ini untuk dibawa saat acara wisuda</div>
            <div class="notice-item">• QR Code yang sama akan discan 2 kali: pagi untuk absensi, sore untuk konsumsi</div>
            <div class="notice-item">• Pastikan membawa undangan ini saat menghadiri acara</div>
            <div class="notice-item">• Kehilangan undangan dapat menyebabkan keterlambatan proses absensi</div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <div class="footer-brand">Universitas Sanggabuana YPKP</div>
            <div class="footer-text">&copy; {{ date('Y') }} Sistem Absensi Wisuda Digital</div>
            <div class="footer-date">Dicetak pada: {{ now()->isoFormat('dddd, D MMMM YYYY HH:mm') }} WIB</div>
        </div>
    </div>
</body>
</html>