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
            font-family: 'Arial', sans-serif;
            font-size: 12pt;
            line-height: 1.6;
            color: #333;
            padding: 20px;
        }

        .container {
            max-width: 100%;
            margin: 0 auto;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 3px solid #1e40af;
        }

        .header h1 {
            color: #1e40af;
            font-size: 24pt;
            margin-bottom: 10px;
        }

        .header p {
            font-size: 14pt;
            color: #666;
        }

        .section {
            margin-bottom: 25px;
        }

        .section-title {
            font-size: 16pt;
            font-weight: bold;
            color: #1e40af;
            margin-bottom: 10px;
            padding-bottom: 5px;
            border-bottom: 2px solid #e5e7eb;
        }

        .info-row {
            display: table;
            width: 100%;
            margin-bottom: 8px;
        }

        .info-label {
            display: table-cell;
            width: 35%;
            font-weight: bold;
            padding-right: 10px;
        }

        .info-value {
            display: table-cell;
            width: 65%;
        }

        .qr-section {
            margin-top: 30px;
            page-break-inside: avoid;
        }

        .qr-grid {
            display: table;
            width: 100%;
            margin-top: 15px;
        }

        .qr-item {
            display: table-cell;
            width: 33.33%;
            text-align: center;
            padding: 10px;
            vertical-align: top;
        }

        .qr-item img {
            width: 180px;
            height: 180px;
            margin: 0 auto;
            display: block;
        }

        .qr-label {
            font-weight: bold;
            font-size: 11pt;
            margin-top: 10px;
            color: #1e40af;
        }

        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px solid #e5e7eb;
            text-align: center;
            font-size: 10pt;
            color: #666;
        }

        .important-note {
            background-color: #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 15px;
            margin-top: 20px;
            font-size: 10pt;
        }

        .important-note strong {
            color: #f59e0b;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>UNDANGAN WISUDA</h1>
            <p>{{ $event->name }}</p>
        </div>

        <!-- Student Information -->
        <div class="section">
            <div class="section-title">Informasi Mahasiswa</div>
            <div class="info-row">
                <div class="info-label">Nama</div>
                <div class="info-value">{{ $mahasiswa->nama }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">NPM</div>
                <div class="info-value">{{ $mahasiswa->npm }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Program Studi</div>
                <div class="info-value">{{ $mahasiswa->program_studi }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Nomor Kursi</div>
                <div class="info-value">{{ $mahasiswa->nomor_kursi ?? '-' }}</div>
            </div>
            @if($mahasiswa->judul_skripsi)
            <div class="info-row">
                <div class="info-label">Judul Skripsi</div>
                <div class="info-value">{{ $mahasiswa->judul_skripsi }}</div>
            </div>
            @endif
        </div>

        <!-- Event Information -->
        <div class="section">
            <div class="section-title">Informasi Acara</div>
            <div class="info-row">
                <div class="info-label">Tanggal</div>
                <div class="info-value">{{ \Carbon\Carbon::parse($event->date)->isoFormat('dddd, D MMMM YYYY') }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Waktu</div>
                <div class="info-value">{{ \Carbon\Carbon::parse($event->time)->format('H:i') }} WIB</div>
            </div>
            <div class="info-row">
                <div class="info-label">Lokasi</div>
                <div class="info-value">{{ $event->location_name }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Alamat</div>
                <div class="info-value">{{ $event->location_address }}</div>
            </div>
        </div>

        <!-- QR Codes -->
        <div class="qr-section">
            <div class="section-title">QR Code Absensi</div>
            <p style="margin-bottom: 15px; font-size: 10pt; color: #666;">
                Tunjukkan QR Code berikut kepada panitia untuk melakukan absensi
            </p>
            
            <div class="qr-grid">
                <div class="qr-item">
                    <img src="{{ $qrCodes['mahasiswa'] }}" alt="QR Code Mahasiswa">
                    <div class="qr-label">Mahasiswa</div>
                </div>
                <div class="qr-item">
                    <img src="{{ $qrCodes['pendamping1'] }}" alt="QR Code Pendamping 1">
                    <div class="qr-label">Pendamping 1</div>
                </div>
                <div class="qr-item">
                    <img src="{{ $qrCodes['pendamping2'] }}" alt="QR Code Pendamping 2">
                    <div class="qr-label">Pendamping 2</div>
                </div>
            </div>
        </div>

        <!-- Important Note -->
        <div class="important-note">
            <strong>Catatan Penting:</strong><br>
            • Harap datang 30 menit sebelum acara dimulai<br>
            • Tunjukkan QR Code ini kepada panitia untuk absensi<br>
            • Setiap QR Code hanya dapat digunakan satu kali<br>
            • Mahasiswa wajib melakukan absensi dengan QR Code "Mahasiswa"<br>
            • Maksimal 2 pendamping per mahasiswa
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>Dokumen ini digenerate secara otomatis oleh Sistem Absensi Wisuda Digital</p>
            <p>Dicetak pada: {{ now()->isoFormat('dddd, D MMMM YYYY HH:mm') }} WIB</p>
        </div>
    </div>
</body>
</html>
