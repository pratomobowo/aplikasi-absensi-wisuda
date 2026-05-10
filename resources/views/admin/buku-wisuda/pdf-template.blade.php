<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Buku Wisuda - {{ $event->name }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 15mm 12mm 18mm 12mm;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Helvetica, Arial, sans-serif;
            font-size: 8pt;
            line-height: 1.3;
            color: #1a1a2e;
        }

        /* ===== COVER PAGE ===== */
        .cover {
            width: 100%;
            height: 100vh;
            text-align: center;
            padding: 60px 40px;
            page-break-after: always;
            position: relative;
        }

        .cover-border {
            border: 3px solid #1e3a8a;
            border-radius: 15px;
            padding: 50px 30px;
            height: 100%;
        }

        .cover-icon {
            font-size: 48pt;
            color: #1e3a8a;
            margin-bottom: 20px;
        }

        .cover-title {
            font-size: 26pt;
            font-weight: bold;
            color: #1e3a8a;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .cover-subtitle {
            font-size: 13pt;
            font-weight: bold;
            color: #334155;
            margin-bottom: 20px;
        }

        .cover-line {
            width: 80px;
            height: 3px;
            background: #1e3a8a;
            margin: 0 auto 20px;
        }

        .cover-event {
            font-size: 11pt;
            color: #475569;
            margin-bottom: 6px;
        }

        .cover-info {
            font-size: 10pt;
            color: #64748b;
            margin-bottom: 4px;
        }

        .cover-stats {
            margin-top: 25px;
            padding: 10px 20px;
            background: #f1f5f9;
            border-radius: 15px;
            display: inline-block;
            font-size: 10pt;
            font-weight: bold;
            color: #1e3a8a;
        }

        .cover-footer {
            position: absolute;
            bottom: 30px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 9pt;
            color: #94a3b8;
        }

        /* ===== CONTENT PAGE ===== */
        .page {
            width: 100%;
            page-break-after: always;
            position: relative;
            min-height: 100vh;
        }

        .page:last-child {
            page-break-after: auto;
        }

        /* Page Header */
        .page-header {
            border-bottom: 2px solid #1e3a8a;
            padding: 6px 0 8px 0;
            margin-bottom: 10px;
            overflow: hidden;
        }

        .page-header-left {
            float: left;
        }

        .page-header-right {
            float: right;
            text-align: right;
        }

        .page-header-title {
            font-size: 9pt;
            font-weight: bold;
            color: #1e3a8a;
            text-transform: uppercase;
        }

        .page-header-jurusan {
            font-size: 8pt;
            color: #64748b;
            font-weight: 600;
        }

        .page-header-event {
            font-size: 7.5pt;
            color: #94a3b8;
        }

        /* Page Footer */
        .page-footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            text-align: center;
            padding: 6px 0;
            border-top: 1px solid #e2e8f0;
            font-size: 8pt;
            color: #94a3b8;
        }

        /* ===== STUDENT CARDS LAYOUT ===== */
        .cards-row {
            overflow: hidden;
            margin-bottom: 8px;
        }

        .card-left {
            width: 48.5%;
            float: left;
        }

        .card-right {
            width: 48.5%;
            float: right;
        }

        /* Student Card */
        .student-card {
            border: 1.5px solid #e2e8f0;
            border-radius: 8px;
            padding: 10px;
            background: white;
            margin-bottom: 6px;
        }

        .student-main {
            overflow: hidden;
            margin-bottom: 6px;
        }

        .student-photo {
            width: 60px;
            height: 75px;
            float: left;
            margin-right: 8px;
            border-radius: 5px;
            border: 1px solid #e2e8f0;
            object-fit: cover;
        }

        .student-photo-placeholder {
            width: 60px;
            height: 75px;
            float: left;
            margin-right: 8px;
            border-radius: 5px;
            border: 1px solid #e2e8f0;
            background: #f8fafc;
            text-align: center;
            line-height: 75px;
            font-size: 7pt;
            color: #94a3b8;
        }

        .student-data {
            overflow: hidden;
        }

        .data-line {
            margin-bottom: 1px;
            font-size: 7.5pt;
        }

        .data-label {
            font-weight: bold;
            color: #1e3a8a;
            display: inline;
        }

        .data-sep {
            font-weight: bold;
            color: #1e3a8a;
            display: inline;
        }

        .data-value {
            color: #334155;
            display: inline;
        }

        /* Thesis */
        .thesis-box {
            background: #f0f7ff;
            border: 1px solid #dbeafe;
            border-radius: 5px;
            padding: 5px 7px;
            clear: both;
        }

        .thesis-label {
            font-size: 6.5pt;
            font-weight: bold;
            color: #1e3a8a;
            text-transform: uppercase;
            margin-bottom: 2px;
        }

        .thesis-text {
            font-size: 7.5pt;
            color: #475569;
            font-style: italic;
            line-height: 1.3;
        }

        /* Clearfix */
        .clearfix::after {
            content: "";
            display: table;
            clear: both;
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
    <!-- ===== COVER PAGE ===== -->
    <div class="cover">
        <div class="cover-border">
            <div class="cover-icon">&#127891;</div>
            <div class="cover-title">Buku Wisuda</div>
            <div class="cover-subtitle">Universitas Sangga Buana YPKP</div>
            <div class="cover-line"></div>
            <div class="cover-event">{{ $event->name }}</div>
            <div class="cover-info">{{ $event->date->format('l, d F Y') }}</div>
            <div class="cover-info">{{ $event->location_name }}</div>
            <div class="cover-stats">Total Wisudawan: {{ $mahasiswa->count() }} Orang</div>
        </div>
        <div class="cover-footer">
            Dokumen Resmi Wisuda - Universitas Sangga Buana YPKP
        </div>
    </div>

    <!-- ===== CONTENT PAGES ===== -->
    @php
        $itemsPerPage = 8;
        $currentPage = 0;
        $allMahasiswa = [];
        
        // Flatten grouped data
        foreach($grouped as $jurusan => $listMahasiswa) {
            foreach($listMahasiswa as $mhs) {
                $mhs->_jurusan = $jurusan;
                $allMahasiswa[] = $mhs;
            }
        }
        
        $totalItems = count($allMahasiswa);
        $totalPages = ceil($totalItems / $itemsPerPage);
    @endphp

    @for($page = 0; $page < $totalPages; $page++)
        <div class="page">
            @php
                $startIdx = $page * $itemsPerPage;
                $endIdx = min($startIdx + $itemsPerPage, $totalItems);
                $pageItems = array_slice($allMahasiswa, $startIdx, $itemsPerPage);
                $firstItem = $pageItems[0] ?? null;
                $jurusanName = $firstItem ? ($firstItem->_jurusan ?? '-') : '-';
            @endphp
            
            <!-- Page Header -->
            <div class="page-header clearfix">
                <div class="page-header-left">
                    <div class="page-header-title">Buku Wisuda</div>
                    <div class="page-header-jurusan">{{ $jurusanName }}</div>
                </div>
                <div class="page-header-right">
                    <div class="page-header-event">{{ $event->name }}</div>
                </div>
            </div>

            <!-- Students Grid -->
            @for($row = 0; $row < 4; $row++)
                @php
                    $idx1 = $startIdx + ($row * 2);
                    $idx2 = $idx1 + 1;
                @endphp
                
                @if($idx1 < $endIdx)
                    <div class="cards-row clearfix">
                        @php $mhs1 = $allMahasiswa[$idx1]; @endphp
                        <div class="card-left">
                            <div class="student-card">
                                <div class="student-main clearfix">
                                    @if($mhs1->foto_wisuda && file_exists(public_path('storage/graduation-photos/' . $mhs1->foto_wisuda)))
                                        <img src="{{ public_path('storage/graduation-photos/' . $mhs1->foto_wisuda) }}" 
                                             alt="{{ $mhs1->nama }}"
                                             class="student-photo">
                                    @else
                                        <div class="student-photo-placeholder">Foto</div>
                                    @endif
                                    
                                    <div class="student-data">
                                        <div class="data-line">
                                            <span class="data-label">NPM</span>
                                            <span class="data-sep">:</span>
                                            <span class="data-value">{{ $mhs1->npm }}</span>
                                        </div>
                                        <div class="data-line">
                                            <span class="data-label">Nama</span>
                                            <span class="data-sep">:</span>
                                            <span class="data-value">{{ $mhs1->nama }}</span>
                                        </div>
                                        <div class="data-line">
                                            <span class="data-label">Prodi</span>
                                            <span class="data-sep">:</span>
                                            <span class="data-value">{{ $mhs1->program_studi ?? '-' }}</span>
                                        </div>
                                        <div class="data-line">
                                            <span class="data-label">IPK</span>
                                            <span class="data-sep">:</span>
                                            <span class="data-value">{{ $mhs1->ipk ?? '-' }}</span>
                                        </div>
                                        <div class="data-line">
                                            <span class="data-label">Yudisium</span>
                                            <span class="data-sep">:</span>
                                            <span class="data-value">{{ $mhs1->yudisium ?? '-' }}</span>
                                        </div>
                                        <div class="data-line">
                                            <span class="data-label">Email</span>
                                            <span class="data-sep">:</span>
                                            <span class="data-value">{{ $mhs1->email ?? '-' }}</span>
                                        </div>
                                    </div>
                                </div>
                                
                                @if($mhs1->judul_skripsi)
                                    <div class="thesis-box">
                                        <div class="thesis-label">Judul Skripsi / Tugas Akhir</div>
                                        <div class="thesis-text">{{ $mhs1->judul_skripsi }}</div>
                                    </div>
                                @endif
                            </div>
                        </div>
                        
                        @if($idx2 < $endIdx)
                            @php $mhs2 = $allMahasiswa[$idx2]; @endphp
                            
                            <div class="card-right">
                                <div class="student-card">
                                    <div class="student-main clearfix">
                                        @if($mhs2->foto_wisuda && file_exists(public_path('storage/graduation-photos/' . $mhs2->foto_wisuda)))
                                            <img src="{{ public_path('storage/graduation-photos/' . $mhs2->foto_wisuda) }}" 
                                                 alt="{{ $mhs2->nama }}"
                                                 class="student-photo">
                                        @else
                                            <div class="student-photo-placeholder">Foto</div>
                                        @endif
                                        
                                        <div class="student-data">
                                            <div class="data-line">
                                                <span class="data-label">NPM</span>
                                                <span class="data-sep">:</span>
                                                <span class="data-value">{{ $mhs2->npm }}</span>
                                            </div>
                                            <div class="data-line">
                                                <span class="data-label">Nama</span>
                                                <span class="data-sep">:</span>
                                                <span class="data-value">{{ $mhs2->nama }}</span>
                                            </div>
                                            <div class="data-line">
                                                <span class="data-label">Prodi</span>
                                                <span class="data-sep">:</span>
                                                <span class="data-value">{{ $mhs2->program_studi ?? '-' }}</span>
                                            </div>
                                            <div class="data-line">
                                                <span class="data-label">IPK</span>
                                                <span class="data-sep">:</span>
                                                <span class="data-value">{{ $mhs2->ipk ?? '-' }}</span>
                                            </div>
                                            <div class="data-line">
                                                <span class="data-label">Yudisium</span>
                                                <span class="data-sep">:</span>
                                                <span class="data-value">{{ $mhs2->yudisium ?? '-' }}</span>
                                            </div>
                                            <div class="data-line">
                                                <span class="data-label">Email</span>
                                                <span class="data-sep">:</span>
                                                <span class="data-value">{{ $mhs2->email ?? '-' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    @if($mhs2->judul_skripsi)
                                        <div class="thesis-box">
                                            <div class="thesis-label">Judul Skripsi / Tugas Akhir</div>
                                            <div class="thesis-text">{{ $mhs2->judul_skripsi }}</div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                @endif
            @endfor

            <!-- Page Footer -->
            <div class="page-footer">
                {{ $event->name }} | Halaman {{ $page + 1 }} dari {{ $totalPages }}
            </div>
        </div>
    @endfor
</body>
</html>