<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Undangan Wisuda - {{ $mahasiswa->nama }}</title>
    <style>
        :root {
            --primary-color: #B43237;
            --primary-dark: #8B251D;
            --primary-light: #E8595C;
            --red-50: #fef2f2;
            --red-100: #fee2e2;
            --blue-50: #eff6ff;
            --blue-100: #dbeafe;
            --blue-600: #2563eb;
            --emerald-50: #f0fdf4;
            --emerald-100: #dcfce7;
            --emerald-600: #16a34a;
            --gray-500: #6b7280;
            --gray-600: #4b5563;
            --gray-900: #111827;
            --amber-50: #fffbeb;
            --amber-400: #fbbf24;
            --amber-800: #b45309;
            --amber-900: #78350f;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', 'Arial', sans-serif;
            font-size: 11pt;
            line-height: 1.6;
            color: var(--gray-900);
            padding: 15px;
            background: #f5f5f5;
        }

        .container {
            max-width: 100%;
            margin: 0 auto;
            background: white;
            padding: 20px;
        }

        /* Header */
        .header-card {
            background-color: var(--primary-color);
            color: white;
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 20px;
            page-break-inside: avoid;
        }

        .header-text {
            vertical-align: middle;
        }

        .header-label {
            font-size: 9pt;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 1px;
            opacity: 0.9;
            margin-bottom: 5px;
        }

        .header-title {
            font-size: 18pt;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .header-date {
            font-size: 10pt;
            opacity: 0.9;
        }

        /* Content Grid */
        .content-grid {
            display: table;
            width: 100%;
            margin-bottom: 20px;
            table-layout: fixed;
        }

        .content-col {
            display: table-cell;
            vertical-align: top;
            padding-right: 15px;
        }

        .content-col:last-child {
            padding-right: 0;
        }

        .col-2-3 {
            width: 65%;
        }

        .col-1-3 {
            width: 35%;
        }

        /* Card Styling */
        .card {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 20px;
            page-break-inside: avoid;
        }

        .card-header {
            margin-bottom: 15px;
            font-size: 13pt;
            font-weight: 600;
            color: var(--primary-color);
            border-bottom: 2px solid #f0f0f0;
            padding-bottom: 10px;
        }

        .info-item {
            margin-bottom: 12px;
            display: table;
            width: 100%;
        }

        .info-label {
            display: table-cell;
            width: 35%;
            font-size: 9pt;
            font-weight: 500;
            color: var(--gray-500);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding-right: 10px;
            vertical-align: top;
        }

        .info-value {
            display: table-cell;
            width: 65%;
            font-size: 11pt;
            font-weight: 600;
            color: var(--gray-900);
        }

        /* QR Codes Section */
        .qr-section {
            margin-top: 20px;
            page-break-inside: avoid;
        }

        .qr-grid {
            display: table;
            width: 100%;
            margin-top: 12px;
            table-layout: fixed;
        }

        .qr-item {
            display: table-cell;
            width: 33.33%;
            text-align: center;
            padding: 0 8px;
            vertical-align: top;
        }

        .qr-card {
            border-radius: 10px;
            padding: 12px;
        }

        .qr-card-mahasiswa {
            background: linear-gradient(135deg, var(--red-50) 0%, var(--red-100) 100%);
        }

        .qr-card-pendamping1 {
            background: linear-gradient(135deg, var(--blue-50) 0%, var(--blue-100) 100%);
        }

        .qr-card-pendamping2 {
            background: linear-gradient(135deg, var(--emerald-50) 0%, var(--emerald-100) 100%);
        }

        .qr-image-wrapper {
            background: white;
            border-radius: 8px;
            padding: 8px;
            display: inline-block;
            margin-bottom: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .qr-image {
            width: 120px;
            height: 120px;
            display: block;
        }

        .qr-label {
            font-weight: 600;
            font-size: 11pt;
            margin-bottom: 3px;
        }

        .qr-label-mahasiswa {
            color: var(--primary-color);
            background-color: var(--primary-color);
            color: white;
            padding: 6px;
            border-radius: 6px;
            display: inline-block;
        }

        .qr-label-pendamping1 {
            color: var(--blue-600);
            background-color: var(--blue-600);
            color: white;
            padding: 6px;
            border-radius: 6px;
            display: inline-block;
        }

        .qr-label-pendamping2 {
            color: var(--emerald-600);
            background-color: var(--emerald-600);
            color: white;
            padding: 6px;
            border-radius: 6px;
            display: inline-block;
        }

        .qr-sublabel {
            font-size: 8pt;
            color: #666;
            margin-top: 4px;
        }

        /* Important Note */
        .important-note {
            background-color: var(--amber-50);
            border-left: 4px solid var(--amber-400);
            padding: 12px;
            margin-top: 20px;
            font-size: 10pt;
            page-break-inside: avoid;
        }

        .important-note-title {
            font-weight: 600;
            color: var(--amber-900);
            margin-bottom: 8px;
        }

        .important-note-item {
            margin-bottom: 5px;
            color: var(--amber-800);
        }

        /* Footer */
        .footer {
            margin-top: 25px;
            padding-top: 15px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            font-size: 9pt;
            color: var(--gray-600);
            page-break-inside: avoid;
        }

        /* Print Styles */
        @media print {
            body {
                padding: 0;
                background: white;
            }
            .container {
                padding: 0;
                box-shadow: none;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header-card">
            <div class="header-text" style="width: 100%;">
                <div class="header-label">Undangan Wisuda</div>
                <div class="header-title">{{ $event->name }}</div>
                <div class="header-date">{{ $event->date->locale('id')->isoFormat('dddd, D MMMM YYYY') }}</div>
            </div>
        </div>

        <!-- Content Grid -->
        <div class="content-grid">
            <!-- Left Column (2/3) - Student Information -->
            <div class="content-col col-2-3">
                <div class="card">
                    <div class="card-header">
                        Informasi Mahasiswa
                    </div>
                    <div class="info-item">
                        <div class="info-label">Nama Mahasiswa</div>
                        <div class="info-value">{{ $mahasiswa->nama }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Nomor Pokok Mahasiswa</div>
                        <div class="info-value">{{ $mahasiswa->npm }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Program Studi</div>
                        <div class="info-value">{{ $mahasiswa->program_studi }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Nomor Kursi</div>
                        <div class="info-value">{{ $mahasiswa->nomor_kursi ?? 'Belum ditentukan' }}</div>
                    </div>
                </div>
            </div>

            <!-- Right Column (1/3) - Event Information -->
            <div class="content-col col-1-3">
                <div class="card">
                    <div class="card-header">
                        Informasi Acara
                    </div>
                    <div class="info-item">
                        <div class="info-label">Tanggal</div>
                        <div class="info-value">{{ $event->date->locale('id')->isoFormat('D MMMM YYYY') }}</div>
                    </div>
                    <div style="font-size: 9pt; color: var(--gray-600); margin-bottom: 12px;">{{ $event->date->locale('id')->isoFormat('dddd') }}</div>
                    <div class="info-item">
                        <div class="info-label">Waktu</div>
                        <div class="info-value">{{ \Carbon\Carbon::parse($event->time)->format('H:i') }} WIB</div>
                    </div>
                    <div style="border-top: 1px solid #e5e7eb; margin: 10px 0;"></div>
                    <div class="info-item">
                        <div class="info-label">Lokasi</div>
                        <div class="info-value">{{ $event->location_name }}</div>
                    </div>
                    <div style="font-size: 9pt; color: var(--gray-600);">{{ $event->location_address }}</div>
                </div>
            </div>
        </div>

        <!-- QR Codes Section -->
        <div class="card qr-section">
            <div class="card-header">
                QR Code Absensi
            </div>
            <p style="margin-bottom: 12px; font-size: 10pt; color: var(--gray-600);">
                Tunjukkan QR Code kepada panitia untuk proses absensi
            </p>

            <div class="qr-grid">
                <!-- Mahasiswa -->
                <div class="qr-item">
                    <div class="qr-card qr-card-mahasiswa">
                        <div class="qr-image-wrapper">
                            <img src="{{ $qrCodes['mahasiswa'] }}" alt="QR Code Mahasiswa" class="qr-image">
                        </div>
                        <div class="qr-label-mahasiswa">Wisudawan</div>
                        <div class="qr-sublabel">Pengguna Utama</div>
                    </div>
                </div>

                <!-- Pendamping 1 -->
                <div class="qr-item">
                    <div class="qr-card qr-card-pendamping1">
                        <div class="qr-image-wrapper">
                            <img src="{{ $qrCodes['pendamping1'] }}" alt="QR Code Pendamping 1" class="qr-image">
                        </div>
                        <div class="qr-label-pendamping1">Pendamping 1</div>
                        <div class="qr-sublabel">Orang Tua/Wali</div>
                    </div>
                </div>

                <!-- Pendamping 2 -->
                <div class="qr-item">
                    <div class="qr-card qr-card-pendamping2">
                        <div class="qr-image-wrapper">
                            <img src="{{ $qrCodes['pendamping2'] }}" alt="QR Code Pendamping 2" class="qr-image">
                        </div>
                        <div class="qr-label-pendamping2">Pendamping 2</div>
                        <div class="qr-sublabel">Orang Tua/Wali</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Important Note -->
        <div class="important-note">
            <div class="important-note-title">Penting untuk Diperhatikan</div>
            <div class="important-note-item">• Simpan halaman ini atau unduh PDF untuk dibawa saat acara wisuda</div>
            <div class="important-note-item">• Setiap QR Code hanya dapat digunakan sekali untuk absensi</div>
            <div class="important-note-item">• Pastikan membawa undangan ini saat menghadiri acara</div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>© {{ date('Y') }} Sistem Absensi Wisuda Digital</p>
            <p>Universitas Sanggabuana YPKP</p>
            <p style="margin-top: 8px; font-size: 8pt;">Dicetak pada: {{ now()->isoFormat('dddd, D MMMM YYYY HH:mm') }} WIB</p>
        </div>
    </div>
</body>
</html>
