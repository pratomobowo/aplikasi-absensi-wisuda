<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Buku Wisuda - {{ $event->name }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 12mm 10mm 14mm 10mm;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Helvetica, Arial, sans-serif;
            font-size: 8pt;
            line-height: 1.25;
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
            font-size: 28pt;
            font-weight: bold;
            color: #1e3a8a;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .cover-subtitle {
            font-size: 14pt;
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
            font-size: 12pt;
            color: #475569;
            margin-bottom: 6px;
        }

        .cover-info {
            font-size: 11pt;
            color: #64748b;
            margin-bottom: 4px;
        }

        .cover-stats {
            margin-top: 25px;
            padding: 10px 25px;
            background: #f1f5f9;
            border-radius: 15px;
            display: inline-block;
            font-size: 11pt;
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

        /* ===== PAGE HEADER (Repeated) ===== */
        .page-header {
            border-bottom: 2px solid #1e3a8a;
            padding: 6px 0 8px 0;
            margin-bottom: 8px;
            overflow: hidden;
            page-break-inside: avoid;
        }

        .page-header-left {
            float: left;
        }

        .page-header-right {
            float: right;
            text-align: right;
        }

        .page-header-title {
            font-size: 10pt;
            font-weight: bold;
            color: #1e3a8a;
            text-transform: uppercase;
        }

        .page-header-jurusan {
            font-size: 8pt;
            color: #64748b;
            font-weight: 600;
            margin-top: 2px;
        }

        .page-header-event {
            font-size: 8pt;
            color: #94a3b8;
        }

        /* ===== PAGE FOOTER (Repeated) ===== */
        .page-footer {
            border-top: 1px solid #e2e8f0;
            padding: 6px 0;
            font-size: 8pt;
            color: #94a3b8;
            text-align: center;
            margin-top: 8px;
            page-break-inside: avoid;
        }

        /* ===== STUDENT CARDS: 2 COL x 6 ROWS ===== */
        .cards-row {
            overflow: hidden;
            margin-bottom: 5px;
            height: 39mm;
        }

        .card-left {
            width: 49%;
            float: left;
            height: 100%;
        }

        .card-right {
            width: 49%;
            float: right;
            height: 100%;
        }

        .student-card {
            border: 1.5px solid #e2e8f0;
            border-radius: 8px;
            padding: 8px;
            background: white;
            height: 100%;
        }

        .student-main {
            overflow: hidden;
            margin-bottom: 4px;
            height: calc(100% - 45px);
        }

        .student-photo {
            width: 48px;
            height: 62px;
            float: left;
            margin-right: 6px;
            border-radius: 4px;
            border: 1px solid #e2e8f0;
            object-fit: cover;
        }

        .student-photo-placeholder {
            width: 48px;
            height: 62px;
            float: left;
            margin-right: 6px;
            border-radius: 4px;
            border: 1px solid #e2e8f0;
            background: #f8fafc;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 7pt;
            color: #94a3b8;
            text-align: center;
        }

        .student-data {
            overflow: hidden;
            height: 100%;
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

        /* Thesis - Fixed height */
        .thesis-box {
            background: #f0f7ff;
            border: 1px solid #dbeafe;
            border-radius: 4px;
            padding: 2px 6px;
            height: 38px;
            overflow: hidden;
        }

        .thesis-label {
            font-size: 6pt;
            font-weight: bold;
            color: #1e3a8a;
            text-transform: uppercase;
            margin-bottom: 1px;
        }

        .thesis-text {
            font-size: 6.5pt;
            color: #475569;
            font-style: italic;
            line-height: 1.2;
        }

        .thesis-empty {
            background: #f8fafc;
            border: 1px dashed #e2e8f0;
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
        $itemsPerPage = 12;
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
        @php
            $startIdx = $page * $itemsPerPage;
            $endIdx = min($startIdx + $itemsPerPage, $totalItems);
            
            // Get all unique jurusan in this page
            $pageJurusans = [];
            for($i = $startIdx; $i < $endIdx; $i++) {
                $jurusan = $allMahasiswa[$i]->_jurusan ?? '-';
                if(!in_array($jurusan, $pageJurusans)) {
                    $pageJurusans[] = $jurusan;
                }
            }
            
            // Format jurusan header text
            if(count($pageJurusans) == 1) {
                $jurusanHeader = $pageJurusans[0];
            } else {
                $jurusanHeader = implode(' - ', $pageJurusans);
            }
        @endphp
        
        <!-- Page Header -->
        <div class="page-header clearfix">
            <div class="page-header-left">
                <div class="page-header-title">Buku Wisuda</div>
                <div class="page-header-jurusan">{{ $jurusanHeader }}</div>
            </div>
            <div class="page-header-right">
                <div class="page-header-event">{{ $event->name }}</div>
            </div>
        </div>

        <!-- Students Grid: 6 rows x 2 columns = 12 per page -->
        @for($row = 0; $row < 6; $row++)
            @php
                $idx1 = $startIdx + ($row * 2);
                $idx2 = $idx1 + 1;
            @endphp
            
            @if($idx1 < $endIdx)
                <div class="cards-row clearfix">
                    <!-- Column 1 -->
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
                                </div>
                            </div>
                            
                            @if($mhs1->judul_skripsi)
                                <div class="thesis-box">
                                    <div class="thesis-label">Judul Skripsi / Tugas Akhir</div>
                                    <div class="thesis-text">{{ $mhs1->judul_skripsi }}</div>
                                </div>
                            @else
                                <div class="thesis-box thesis-empty">
                                    <div class="thesis-label">Judul Skripsi / Tugas Akhir</div>
                                    <div class="thesis-text">-</div>
                                </div>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Column 2 -->
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
                                    </div>
                                </div>
                                
                                @if($mhs2->judul_skripsi)
                                    <div class="thesis-box">
                                        <div class="thesis-label">Judul Skripsi / Tugas Akhir</div>
                                        <div class="thesis-text">{{ $mhs2->judul_skripsi }}</div>
                                    </div>
                                @else
                                    <div class="thesis-box thesis-empty">
                                        <div class="thesis-label">Judul Skripsi / Tugas Akhir</div>
                                        <div class="thesis-text">-</div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @else
                        <!-- Empty placeholder to maintain layout -->
                        <div class="card-right">
                            <div class="student-card" style="visibility: hidden;">
                                <div class="student-main clearfix">
                                    <div class="student-photo-placeholder">Foto</div>
                                    <div class="student-data">
                                        <div class="data-line"><span class="data-label">NPM</span></div>
                                        <div class="data-line"><span class="data-label">Nama</span></div>
                                        <div class="data-line"><span class="data-label">Prodi</span></div>
                                        <div class="data-line"><span class="data-label">IPK</span></div>
                                        <div class="data-line"><span class="data-label">Yudisium</span></div>
                                    </div>
                                </div>
                                <div class="thesis-box thesis-empty">
                                    <div class="thesis-label">Judul Skripsi / Tugas Akhir</div>
                                    <div class="thesis-text">-</div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            @else
                <!-- Empty row to maintain 6 rows per page -->
                <div class="cards-row clearfix" style="visibility: hidden;">
                    <div class="card-left">
                        <div class="student-card">
                            <div class="student-main clearfix">
                                <div class="student-photo-placeholder">Foto</div>
                                <div class="student-data">
                                    <div class="data-line"><span class="data-label">NPM</span></div>
                                    <div class="data-line"><span class="data-label">Nama</span></div>
                                    <div class="data-line"><span class="data-label">Prodi</span></div>
                                    <div class="data-line"><span class="data-label">IPK</span></div>
                                    <div class="data-line"><span class="data-label">Yudisium</span></div>
                                </div>
                            </div>
                            <div class="thesis-box thesis-empty">
                                <div class="thesis-label">Judul Skripsi / Tugas Akhir</div>
                                <div class="thesis-text">-</div>
                            </div>
                        </div>
                    </div>
                    <div class="card-right">
                        <div class="student-card">
                            <div class="student-main clearfix">
                                <div class="student-photo-placeholder">Foto</div>
                                <div class="student-data">
                                    <div class="data-line"><span class="data-label">NPM</span></div>
                                    <div class="data-line"><span class="data-label">Nama</span></div>
                                    <div class="data-line"><span class="data-label">Prodi</span></div>
                                    <div class="data-line"><span class="data-label">IPK</span></div>
                                    <div class="data-line"><span class="data-label">Yudisium</span></div>
                                </div>
                            </div>
                            <div class="thesis-box thesis-empty">
                                <div class="thesis-label">Judul Skripsi / Tugas Akhir</div>
                                <div class="thesis-text">-</div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        @endfor

        <!-- Page Footer -->
        <div class="page-footer">
            {{ $event->name }} | Halaman {{ $page + 1 }} dari {{ $totalPages }}
        </div>

        <!-- Page Break -->
        @if($page < $totalPages - 1)
            <div style="page-break-after: always;"></div>
        @endif
    @endfor
</body>
</html>