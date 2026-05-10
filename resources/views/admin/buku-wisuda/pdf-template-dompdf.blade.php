<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Buku Wisuda - {{ $event->name }}</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 15mm 20mm;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 9pt;
            line-height: 1.4;
            color: #1e293b;
        }

        /* Cover Page */
        .cover {
            width: 100%;
            height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            page-break-after: always;
            background: #1e40af;
            color: white;
        }
        
        .cover-content {
            background: white;
            padding: 50px 60px;
            border-radius: 16px;
            color: #1e293b;
            width: 80%;
        }
        
        .cover h1 {
            font-size: 32pt;
            font-weight: bold;
            margin-bottom: 15px;
            text-transform: uppercase;
            letter-spacing: 3px;
            color: #1e40af;
        }
        
        .cover h2 {
            font-size: 16pt;
            font-weight: 600;
            margin-bottom: 10px;
            color: #334155;
        }
        
        .cover-divider {
            width: 100px;
            height: 3px;
            background: #1e40af;
            margin: 20px auto;
        }
        
        .cover-info {
            font-size: 10pt;
            margin-top: 20px;
            color: #64748b;
        }
        
        .cover-info p {
            margin: 4px 0;
        }

        /* Page */
        .page {
            width: 100%;
            page-break-after: always;
        }

        .page:last-child {
            page-break-after: auto;
        }

        /* Two Column */
        .two-column {
            width: 100%;
            display: table;
            border-spacing: 15px 0;
        }
        
        .column {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }

        /* Student Card */
        .student-card {
            background: #ffffff;
            border-radius: 10px;
            border: 1px solid #e2e8f0;
            overflow: hidden;
            margin-bottom: 12px;
        }
        
        .card-body {
            padding: 12px;
        }

        /* Info Section */
        .info-section {
            display: table;
            width: 100%;
            margin-bottom: 10px;
        }
        
        .photo-cell {
            display: table-cell;
            width: 85px;
            vertical-align: top;
            padding-right: 10px;
        }
        
        .data-cell {
            display: table-cell;
            vertical-align: top;
        }
        
        .student-photo {
            width: 80px;
            height: 105px;
            object-fit: cover;
            border-radius: 6px;
            border: 1px solid #e2e8f0;
        }
        
        .photo-placeholder {
            width: 80px;
            height: 105px;
            border-radius: 6px;
            background: #f1f5f9;
            border: 1px solid #e2e8f0;
            display: table-cell;
            vertical-align: middle;
            text-align: center;
            font-size: 7pt;
            color: #94a3b8;
        }

        /* Data Rows */
        .data-row {
            display: table;
            width: 100%;
            margin-bottom: 2px;
        }
        
        .data-label {
            display: table-cell;
            width: 80px;
            font-weight: bold;
            color: #1e40af;
            font-size: 8pt;
        }
        
        .data-separator {
            display: table-cell;
            width: 12px;
            font-weight: bold;
            color: #1e40af;
            font-size: 8pt;
        }
        
        .data-value {
            display: table-cell;
            color: #334155;
            font-size: 8pt;
        }

        /* Thesis Section */
        .thesis-section {
            background: #eff6ff;
            border-radius: 8px;
            padding: 8px 10px;
            border: 1px solid #dbeafe;
        }
        
        .thesis-header {
            margin-bottom: 4px;
        }
        
        .thesis-label {
            font-size: 7pt;
            font-weight: bold;
            text-transform: uppercase;
            color: #1e40af;
        }
        
        .thesis-title {
            font-size: 8pt;
            color: #475569;
            line-height: 1.4;
            font-style: italic;
            padding-left: 5px;
        }

        /* Print */
        @media print {
            .student-card {
                break-inside: avoid;
            }
        }
    </style>
</head>
<body>
    <!-- Cover Page -->
    <div class="cover">
        <div class="cover-content">
            <h1>Buku Wisuda</h1>
            <div class="cover-divider"></div>
            <h2>{{ $event->name }}</h2>
            <div class="cover-info">
                <p>{{ $event->date->format('l, d F Y') }}</p>
                <p>Pukul {{ $event->time->format('H:i') }} WIB</p>
                <p>{{ $event->location_name }}</p>
                <p style="margin-top: 12px; font-weight: bold;">Total Wisudawan: {{ $mahasiswa->count() }}</p>
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
                                        <div class="photo-cell">
                                            @if($mhs->foto_wisuda && file_exists(public_path('storage/graduation-photos/' . $mhs->foto_wisuda)))
                                                <img src="{{ public_path('storage/graduation-photos/' . $mhs->foto_wisuda) }}" 
                                                     alt="{{ $mhs->nama }}"
                                                     class="student-photo">
                                            @else
                                                <div class="photo-placeholder">
                                                    Foto Tidak Tersedia
                                                </div>
                                            @endif
                                        </div>
                                        
                                        <div class="data-cell">
                                            <div class="data-row">
                                                <span class="data-label">NPM</span>
                                                <span class="data-separator">:</span>
                                                <span class="data-value">{{ $mhs->npm }}</span>
                                            </div>
                                            
                                            <div class="data-row">
                                                <span class="data-label">Nama</span>
                                                <span class="data-separator">:</span>
                                                <span class="data-value">{{ $mhs->nama }}</span>
                                            </div>
                                            
                                            <div class="data-row">
                                                <span class="data-label">Prodi</span>
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
                                                <span class="thesis-label">Judul Skripsi / Tugas Akhir</span>
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