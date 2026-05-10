<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buku Wisuda - {{ $event->name }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        @page {
            size: A4 portrait;
            margin: 18mm 16mm 22mm 16mm;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            font-size: 8.5pt;
            line-height: 1.35;
            color: #1a1a2e;
            background: white;
        }

        /* ===== COVER PAGE ===== */
        .cover {
            width: 100%;
            height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            page-break-after: always;
            position: relative;
            background: linear-gradient(180deg, #f8f9fa 0%, #ffffff 100%);
        }

        .cover::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 6px;
            background: linear-gradient(90deg, #1e3a8a, #3b82f6);
        }

        .cover-content {
            padding: 40px;
            max-width: 85%;
        }

        .cover-logo {
            width: 100px;
            height: 100px;
            margin: 0 auto 30px;
            background: #1e3a8a;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .cover-logo svg {
            width: 50px;
            height: 50px;
            color: white;
        }

        .cover-title {
            font-size: 28pt;
            font-weight: 800;
            color: #1e3a8a;
            margin-bottom: 8px;
            letter-spacing: 2px;
            text-transform: uppercase;
        }

        .cover-subtitle {
            font-size: 14pt;
            font-weight: 600;
            color: #334155;
            margin-bottom: 25px;
        }

        .cover-divider {
            width: 80px;
            height: 3px;
            background: linear-gradient(90deg, #1e3a8a, #3b82f6);
            margin: 0 auto 25px;
            border-radius: 2px;
        }

        .cover-event {
            font-size: 11pt;
            color: #475569;
            margin-bottom: 8px;
        }

        .cover-date {
            font-size: 10pt;
            color: #64748b;
            margin-bottom: 5px;
        }

        .cover-location {
            font-size: 10pt;
            color: #64748b;
            margin-bottom: 30px;
        }

        .cover-stats {
            display: inline-block;
            background: #f1f5f9;
            padding: 10px 25px;
            border-radius: 20px;
            font-size: 10pt;
            font-weight: 600;
            color: #1e3a8a;
        }

        .cover-footer {
            position: absolute;
            bottom: 40px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 9pt;
            color: #94a3b8;
        }

        /* ===== CONTENT PAGE ===== */
        .content-page {
            width: 100%;
            page-break-after: always;
            position: relative;
        }

        .content-page:last-child {
            page-break-after: auto;
        }

        /* Page Header */
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 0;
            border-bottom: 2px solid #1e3a8a;
            margin-bottom: 14px;
        }

        .page-header-left {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .page-header-icon {
            width: 22px;
            height: 22px;
            background: #1e3a8a;
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .page-header-icon svg {
            width: 13px;
            height: 13px;
            color: white;
        }

        .page-header-title {
            font-size: 9pt;
            font-weight: 700;
            color: #1e3a8a;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .page-header-event {
            font-size: 8pt;
            color: #64748b;
            font-weight: 500;
        }

        /* Page Footer */
        .page-footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            text-align: center;
            padding: 8px 0;
            border-top: 1px solid #e2e8f0;
            font-size: 8pt;
            color: #94a3b8;
        }

        .page-number {
            font-weight: 600;
            color: #64748b;
        }

        /* ===== GRID LAYOUT: 2x2 ===== */
        .students-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            grid-template-rows: 1fr 1fr;
            gap: 12px;
            height: calc(100% - 50px);
        }

        /* ===== STUDENT CARD ===== */
        .student-card {
            border: 1.5px solid #e2e8f0;
            border-radius: 10px;
            padding: 12px;
            background: white;
            display: flex;
            flex-direction: column;
            page-break-inside: avoid;
        }

        .student-header {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            margin-bottom: 8px;
        }

        .student-photo-wrapper {
            flex-shrink: 0;
        }

        .student-photo {
            width: 70px;
            height: 90px;
            object-fit: cover;
            border-radius: 6px;
            border: 1.5px solid #e2e8f0;
        }

        .student-photo-placeholder {
            width: 70px;
            height: 90px;
            border-radius: 6px;
            background: #f8fafc;
            border: 1.5px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 7pt;
            color: #94a3b8;
            text-align: center;
            flex-shrink: 0;
        }

        .student-info {
            flex: 1;
            min-width: 0;
        }

        .info-row {
            display: flex;
            margin-bottom: 2px;
            font-size: 7.5pt;
        }

        .info-label {
            width: 72px;
            font-weight: 700;
            color: #1e3a8a;
            flex-shrink: 0;
        }

        .info-separator {
            width: 10px;
            font-weight: 700;
            color: #1e3a8a;
            flex-shrink: 0;
        }

        .info-value {
            flex: 1;
            color: #334155;
            font-weight: 500;
            word-wrap: break-word;
        }

        /* Thesis Section */
        .thesis-section {
            background: linear-gradient(135deg, #eff6ff 0%, #f0f9ff 100%);
            border: 1px solid #dbeafe;
            border-radius: 6px;
            padding: 6px 8px;
            margin-top: auto;
        }

        .thesis-header {
            display: flex;
            align-items: center;
            gap: 5px;
            margin-bottom: 3px;
        }

        .thesis-icon {
            width: 14px;
            height: 14px;
            background: #1e3a8a;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .thesis-icon svg {
            width: 8px;
            height: 8px;
            color: white;
        }

        .thesis-label-text {
            font-size: 6.5pt;
            font-weight: 700;
            text-transform: uppercase;
            color: #1e3a8a;
            letter-spacing: 0.3px;
        }

        .thesis-content {
            font-size: 7.5pt;
            color: #475569;
            line-height: 1.4;
            font-style: italic;
            padding-left: 19px;
        }

        /* Print optimization */
        @media print {
            .student-card {
                break-inside: avoid;
            }
        }
    </style>
</head>
<body>
    <!-- ===== COVER PAGE ===== -->
    <div class="cover">
        <div class="cover-content">
            <div class="cover-logo">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 10v6M2 10l10-5 10 5-10 5z"></path>
                    <path d="M6 12v5c3 3 9 3 12 0v-5"></path>
                </svg>
            </div>
            <div class="cover-title">Buku Wisuda</div>
            <div class="cover-subtitle">Universitas Sangga Buana YPKP</div>
            <div class="cover-divider"></div>
            <div class="cover-event">{{ $event->name }}</div>
            <div class="cover-date">{{ $event->date->format('l, d F Y') }}</div>
            <div class="cover-location">{{ $event->location_name }}</div>
            <div class="cover-stats">Total Wisudawan: {{ $mahasiswa->count() }} Orang</div>
        </div>
        <div class="cover-footer">
            Dokumen Resmi Wisuda - Universitas Sangga Buana YPKP
        </div>
    </div>

    <!-- ===== CONTENT PAGES ===== -->
    @php
        $totalMahasiswa = $mahasiswa->count();
        $itemsPerPage = 4;
        $totalPages = ceil($totalMahasiswa / $itemsPerPage);
    @endphp

    @for($page = 0; $page < $totalPages; $page++)
        <div class="content-page">
            <!-- Page Header -->
            <div class="page-header">
                <div class="page-header-left">
                    <div class="page-header-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M22 10v6M2 10l10-5 10 5-10 5z"></path>
                            <path d="M6 12v5c3 3 9 3 12 0v-5"></path>
                        </svg>
                    </div>
                    <div class="page-header-title">Buku Wisuda</div>
                </div>
                <div class="page-header-event">{{ $event->name }}</div>
            </div>

            <!-- Students Grid (2x2) -->
            <div class="students-grid">
                @for($i = 0; $i < 4; $i++)
                    @php
                        $index = ($page * $itemsPerPage) + $i;
                    @endphp
                    
                    @if($index < $totalMahasiswa)
                        @php $mhs = $mahasiswa[$index]; @endphp
                        
                        <div class="student-card">
                            <div class="student-header">
                                <div class="student-photo-wrapper">
                                    @if($mhs->foto_wisuda && file_exists(public_path('storage/graduation-photos/' . $mhs->foto_wisuda)))
                                        <img src="{{ public_path('storage/graduation-photos/' . $mhs->foto_wisuda) }}" 
                                             alt="{{ $mhs->nama }}"
                                             class="student-photo">
                                    @else
                                        <div class="student-photo-placeholder">
                                            Foto
                                            <br>
                                            Tidak
                                            <br>
                                            Tersedia
                                        </div>
                                    @endif
                                </div>
                                
                                <div class="student-info">
                                    <div class="info-row">
                                        <span class="info-label">NPM</span>
                                        <span class="info-separator">:</span>
                                        <span class="info-value">{{ $mhs->npm }}</span>
                                    </div>
                                    <div class="info-row">
                                        <span class="info-label">Nama</span>
                                        <span class="info-separator">:</span>
                                        <span class="info-value">{{ $mhs->nama }}</span>
                                    </div>
                                    <div class="info-row">
                                        <span class="info-label">Prodi</span>
                                        <span class="info-separator">:</span>
                                        <span class="info-value">{{ $mhs->program_studi ?? '-' }}</span>
                                    </div>
                                    <div class="info-row">
                                        <span class="info-label">IPK</span>
                                        <span class="info-separator">:</span>
                                        <span class="info-value">{{ $mhs->ipk ?? '-' }}</span>
                                    </div>
                                    <div class="info-row">
                                        <span class="info-label">Yudisium</span>
                                        <span class="info-separator">:</span>
                                        <span class="info-value">{{ $mhs->yudisium ?? '-' }}</span>
                                    </div>
                                    <div class="info-row">
                                        <span class="info-label">Email</span>
                                        <span class="info-separator">:</span>
                                        <span class="info-value">{{ $mhs->email ?? '-' }}</span>
                                    </div>
                                </div>
                            </div>
                            
                            @if($mhs->judul_skripsi)
                                <div class="thesis-section">
                                    <div class="thesis-header">
                                        <div class="thesis-icon">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                                                <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                                            </svg>
                                        </div>
                                        <span class="thesis-label-text">Judul Skripsi / Tugas Akhir</span>
                                    </div>
                                    <div class="thesis-content">{{ $mhs->judul_skripsi }}</div>
                                </div>
                            @endif
                        </div>
                    @endif
                @endfor
            </div>

            <!-- Page Footer with Number -->
            <div class="page-footer">
                Halaman <span class="page-number">{{ $page + 1 }}</span> dari {{ $totalPages }}
            </div>
        </div>
    @endfor
</body>
</html>