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
            font-size: 10pt;
            line-height: 1.4;
            color: #333;
        }

        .container {
            width: 100%;
            padding: 15px;
        }

        /* Header */
        .header {
            background-color: #B43237;
            color: white;
            padding: 20px;
            text-align: center;
            margin-bottom: 20px;
            border-radius: 8px;
        }

        .header img {
            width: 50px;
            height: 50px;
            margin-bottom: 10px;
        }

        .header-small {
            font-size: 8pt;
            text-transform: uppercase;
            letter-spacing: 2px;
            opacity: 0.9;
        }

        .header-title {
            font-size: 14pt;
            font-weight: bold;
            margin: 5px 0;
        }

        .header-date {
            font-size: 9pt;
            opacity: 0.9;
        }

        /* Table Layout */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        td {
            vertical-align: top;
        }

        .card {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            background: #fafafa;
        }

        .card-title {
            font-size: 11pt;
            font-weight: bold;
            color: #B43237;
            margin-bottom: 12px;
            padding-bottom: 8px;
            border-bottom: 2px solid #B43237;
        }

        /* Photo */
        .photo {
            width: 90px;
            height: 110px;
            object-fit: cover;
            border-radius: 6px;
            border: 2px solid #B43237;
        }

        .photo-placeholder {
            width: 90px;
            height: 110px;
            background: #f0f0f0;
            border: 2px dashed #ccc;
            border-radius: 6px;
            text-align: center;
            font-size: 8pt;
            color: #999;
            padding-top: 35px;
        }

        /* Info */
        .info-label {
            font-size: 7pt;
            color: #666;
            text-transform: uppercase;
            margin-bottom: 2px;
            font-weight: bold;
        }

        .info-value {
            font-size: 10pt;
            font-weight: bold;
            color: #000;
        }

        .info-row {
            margin-bottom: 10px;
        }

        .info-divider {
            border-top: 1px solid #eee;
            margin: 8px 0;
        }

        /* QR Section */
        .qr-box {
            background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
            border: 2px solid #B43237;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            margin-bottom: 20px;
        }

        .qr-title {
            font-size: 11pt;
            font-weight: bold;
            color: #B43237;
            margin-bottom: 3px;
        }

        .qr-subtitle {
            font-size: 8pt;
            color: #666;
            margin-bottom: 15px;
        }

        .qr-image-box {
            background: white;
            border-radius: 8px;
            padding: 15px;
            display: inline-block;
            margin-bottom: 10px;
        }

        .qr-image {
            width: 130px;
            height: 130px;
        }

        .qr-badge {
            background: #B43237;
            color: white;
            padding: 5px 15px;
            border-radius: 15px;
            font-size: 9pt;
            font-weight: bold;
            display: inline-block;
            margin-bottom: 3px;
        }

        .qr-note {
            font-size: 8pt;
            color: #666;
        }

        /* Notice */
        .notice {
            background: #fffbeb;
            border-left: 4px solid #f59e0b;
            padding: 12px 15px;
            margin-bottom: 20px;
            border-radius: 0 6px 6px 0;
        }

        .notice-title {
            font-size: 9pt;
            font-weight: bold;
            color: #92400e;
            margin-bottom: 5px;
        }

        .notice-item {
            font-size: 8pt;
            color: #78350f;
            margin-bottom: 3px;
        }

        /* Footer */
        .footer {
            text-align: center;
            padding-top: 15px;
            border-top: 1px solid #eee;
        }

        .footer-brand {
            font-size: 9pt;
            font-weight: bold;
            color: #B43237;
        }

        .footer-text {
            font-size: 8pt;
            color: #666;
        }

        .footer-date {
            font-size: 7pt;
            color: #999;
            margin-top: 5px;
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

        <!-- Content Table -->
        <table>
            <tr>
                <!-- Left: Student Info -->
                <td style="width: 65%; padding-right: 10px;">
                    <div class="card">
                        <div class="card-title">Informasi Mahasiswa</div>
                        
                        <table>
                            <tr>
                                <td style="width: 100px; padding-right: 10px;">
                                    @if($mahasiswa->foto_wisuda && file_exists(public_path('storage/graduation-photos/' . $mahasiswa->foto_wisuda)))
                                        <img src="{{ public_path('storage/graduation-photos/' . $mahasiswa->foto_wisuda) }}" alt="Foto" class="photo">
                                    @else
                                        <div class="photo-placeholder">Foto Belum Tersedia</div>
                                    @endif
                                </td>
                                <td>
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
                                </td>
                            </tr>
                        </table>
                    </div>
                </td>

                <!-- Right: Event Info -->
                <td style="width: 35%; padding-left: 10px;">
                    <div class="card">
                        <div class="card-title">Detail Acara</div>
                        
                        <div class="info-row">
                            <div class="info-label">Tanggal</div>
                            <div class="info-value">{{ $event->date->locale('id')->isoFormat('D MMMM YYYY') }}</div>
                            <div style="font-size: 8pt; color: #666;">{{ $event->date->locale('id')->isoFormat('dddd') }}</div>
                        </div>
                        
                        <div class="info-divider"></div>
                        
                        <div class="info-row">
                            <div class="info-label">Waktu</div>
                            <div class="info-value">{{ \Carbon\Carbon::parse($event->time)->format('H:i') }} WIB</div>
                        </div>
                        
                        <div class="info-divider"></div>
                        
                        <div class="info-row">
                            <div class="info-label">Lokasi</div>
                            <div class="info-value" style="font-size: 9pt;">{{ $event->location_name }}</div>
                            <div style="font-size: 7pt; color: #888;">{{ $event->location_address }}</div>
                        </div>
                    </div>
                </td>
            </tr>
        </table>

        <!-- QR Code -->
        <div class="qr-box">
            <div class="qr-title">QR Code Absensi &amp; Konsumsi</div>
            <div class="qr-subtitle">Tunjukkan QR Code ini kepada panitia saat acara berlangsung</div>
            
            <div class="qr-image-box">
                <img src="{{ $qrCodes['mahasiswa'] }}" alt="QR Code" class="qr-image">
            </div>
            
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