<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buku Wisuda - {{ $event->name }}</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 20mm 25mm;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 9pt;
            line-height: 1.4;
            color: #1e293b;
            background: #fff;
        }

        /* Cover Page */
        .cover {
            width: 100vw;
            height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            page-break-after: always;
            background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 50%, #3b82f6 100%);
            color: white;
        }

        .cover-content {
            background: rgba(255, 255, 255, 0.95);
            padding: 60px 80px;
            border-radius: 20px;
            box-shadow: 0 25px 80px rgba(0, 0, 0, 0.3);
            color: #1e293b;
            max-width: 600px;
        }

        .cover-icon {
            width: 80px;
            height: 80px;
            background: #1e40af;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 30px;
            font-size: 36px;
            color: white;
        }

        .cover h1 {
            font-size: 42pt;
            font-weight: 800;
            margin-bottom: 15px;
            text-transform: uppercase;
            letter-spacing: 4px;
            color: #1e40af;
        }

        .cover h2 {
            font-size: 18pt;
            font-weight: 500;
            margin-bottom: 10px;
            color: #334155;
        }

        .cover-divider {
            width: 120px;
            height: 4px;
            background: #1e40af;
            margin: 25px auto;
            border-radius: 2px;
        }

        .cover-info {
            font-size: 11pt;
            margin-top: 25px;
            color: #64748b;
        }

        .cover-info p {
            margin: 5px 0;
        }

        /* Content Pages */
        .page {
            width: 100%;
            padding: 0;
            page-break-after: always;
        }

        .page:last-child {
            page-break-after: auto;
        }

        /* Two Column Layout */
        .two-column {
            display: flex;
            gap: 20px;
            width: 100%;
        }

        .column {
            flex: 1;
            width: 50%;
        }

        /* Student Card */
        .student-card {
            background: #ffffff;
            border-radius: 12px;
            border: 1.5px solid #e2e8f0;
            overflow: hidden;
            margin-bottom: 16px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }

        .card-body {
            padding: 16px;
        }

        /* Info Section */
        .info-section {
            display: flex;
            gap: 14px;
            margin-bottom: 12px;
        }

        .photo-wrapper {
            flex-shrink: 0;
        }

        .student-photo {
            width: 90px;
            height: 120px;
            object-fit: cover;
            border-radius: 8px;
            border: 2px solid #e2e8f0;
        }

        .photo-placeholder {
            width: 90px;
            height: 120px;
            border-radius: 8px;
            background: #f1f5f9;
            border: 2px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 8pt;
            color: #94a3b8;
            text-align: center;
        }

        .data-wrapper {
            flex: 1;
            min-width: 0;
        }

        .data-row {
            display: flex;
            margin-bottom: 3px;
            align-items: baseline;
        }

        .data-label {
            width: 85px;
            font-weight: 700;
            color: #1e40af;
            font-size: 8pt;
            flex-shrink: 0;
        }

        .data-separator {
            width: 15px;
            font-weight: 700;
            color: #1e40af;
            font-size: 8pt;
            flex-shrink: 0;
        }

        .data-value {
            color: #334155;
            font-size: 8.5pt;
            font-weight: 500;
            word-break: break-word;
        }

        /* Thesis Section */
        .thesis-section {
            background: #eff6ff;
            border-radius: 10px;
            padding: 10px 12px;
            border: 1px solid #dbeafe;
        }

        .thesis-header {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 6px;
        }

        .thesis-icon {
            width: 22px;
            height: 22px;
            background: #1e40af;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 10px;
            flex-shrink: 0;
        }

        .thesis-label {
            font-size: 7pt;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #1e40af;
        }

        .thesis-title {
            font-size: 8pt;
            color: #475569;
            line-height: 1.5;
            font-style: italic;
            padding-left: 30px;
        }

        /* Print optimization */
        @media print {
            .student-card {
                break-inside: avoid;
            }
            
            .page {
                break-after: page;
            }
        }
    </style>
</head>
<body>
    <!-- Cover Page -->
    <div class="cover">
        <div class="cover-content">
            <div class="cover-icon">
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M22 10v6M2 10l10-5 10 5-10 5z"></path>
                    <path d="M6 12v5c3 3 9 3 12 0v-5"></path>
                </svg>
            </div>
            <h1>Buku Wisuda</h1>
            <div class="cover-divider"></div>
            <h2>{{ $event->name }}</h2>
            <div class="cover-info">
                <p>{{ $event->date->format('l, d F Y') }}</p>
                <p>Pukul {{ $event->time->format('H:i') }} WIB</p>
                <p>{{ $event->location_name }}</p>
                <p style="margin-top: 15px; font-weight: 600;">Total Wisudawan: {{ $mahasiswa->count() }}</p>
            </div>
        </div>
    </div>

    <!-- Content Pages -->
    @php
        $totalMahasiswa = $mahasiswa->count();
        $itemsPerPage = 2;
        $totalPages = ceil($totalMahasiswa / $itemsPerPage);
    @endphp
    
    @for($page = 0; $page < $totalPages; $page++)
        <div class="page">
            <div class="two-column">
                @for($col = 0; $col < 2; $col++)
                    @php
                        $index = ($page * $itemsPerPage) + $col;
                    @endphp
                    
                    @if($index < $totalMahasiswa)
                        @php $mhs = $mahasiswa[$index]; @endphp
                        
                        <div class="column">
                            <div class="student-card">
                                <div class="card-body">
                                    <!-- Section 1: Photo + Info -->
                                    <div class="info-section">
                                        <div class="photo-wrapper">
                                            @if($mhs->foto_wisuda && file_exists(public_path('storage/graduation-photos/' . $mhs->foto_wisuda)))
                                                <img src="{{ public_path('storage/graduation-photos/' . $mhs->foto_wisuda) }}" 
                                                     alt="{{ $mhs->nama }}"
                                                     class="student-photo">
                                            @else
                                                <div class="photo-placeholder">
                                                    Foto
                                                    <br>
                                                    Tidak
                                                    <br>
                                                    Tersedia
                                                </div>
                                            @endif
                                        </div>
                                        
                                        <div class="data-wrapper">
                                            <div class="data-row">
                                                <span class="data-label">NPM</span>
                                                <span class="data-separator">:</span>
                                                <span class="data-value">{{ $mhs->npm }}</span>
                                            </div>
                                            
                                            <div class="data-row">
                                                <span class="data-label">Nama Lengkap</span>
                                                <span class="data-separator">:</span>
                                                <span class="data-value">{{ $mhs->nama }}</span>
                                            </div>
                                            
                                            <div class="data-row">
                                                <span class="data-label">Program Studi</span>
                                                <span class="data-separator">:</span>
                                                <span class="data-value">{{ $mhs->program_studi ?? '-' }}</span>
                                            </div>
                                            
                                            <div class="data-row">
                                                <span class="data-label">IPK</span>
                                                <span class="data-separator">:</span>
                                                <span class="data-value">{{ $mhs->ipk ?? '-' }}</span>
                                            </div>
                                            
                                            <div class="data-row">
                                                <span class="data-label">Yudisium</span>
                                                <span class="data-separator">:</span>
                                                <span class="data-value">{{ $mhs->yudisium ?? '-' }}</span>
                                            </div>
                                            
                                            <div class="data-row">
                                                <span class="data-label">Email</span>
                                                <span class="data-separator">:</span>
                                                <span class="data-value">{{ $mhs->email ?? '-' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Section 2: Thesis Title -->
                                    @if($mhs->judul_skripsi)
                                        <div class="thesis-section">
                                            <div class="thesis-header">
                                                <div class="thesis-icon">
                                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                                        <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                                                        <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                                                    </svg>
                                                </div>
                                                <div class="thesis-label">Judul Skripsi / Tugas Akhir</div>
                                            </div>
                                            <div class="thesis-title">{{ $mhs->judul_skripsi }}</div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif
                @endfor
            </div>
        </div>
    @endfor
</body>
</html>