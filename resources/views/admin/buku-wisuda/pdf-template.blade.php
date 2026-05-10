<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Buku Wisuda - {{ $event->name }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        @page {
            margin: 1.5cm;
            size: A4 portrait;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', 'Helvetica Neue', Arial, sans-serif;
            font-size: 10pt;
            line-height: 1.5;
            color: #2d3748;
        }

        /* Cover Page */
        .cover {
            height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            page-break-after: always;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2cm;
        }
        
        .cover-content {
            background: rgba(255, 255, 255, 0.95);
            padding: 3cm 2cm;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            color: #2d3748;
            max-width: 80%;
        }
        
        .cover h1 {
            font-size: 32pt;
            font-weight: 700;
            margin-bottom: 15px;
            text-transform: uppercase;
            letter-spacing: 3px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            background-clip: text;
            color: #667eea;
        }
        
        .cover h2 {
            font-size: 20pt;
            font-weight: 600;
            margin-bottom: 10px;
            color: #4a5568;
        }
        
        .cover-divider {
            width: 100px;
            height: 4px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            margin: 20px auto;
            border-radius: 2px;
        }
        
        .cover .event-info {
            font-size: 12pt;
            margin-top: 20px;
            color: #718096;
            font-weight: 400;
        }
        
        .cover .event-info p {
            margin: 5px 0;
        }
        
        /* Student Card */
        .student-card {
            page-break-inside: avoid;
            margin-bottom: 25px;
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            overflow: hidden;
            border: 1px solid #e2e8f0;
        }
        
        .student-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 12px 20px;
            font-weight: 600;
            font-size: 11pt;
        }
        
        .student-body {
            padding: 20px;
        }
        
        /* Section 1: Two Column Layout */
        .section-info {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }
        
        .photo-column {
            display: table-cell;
            width: 140px;
            vertical-align: top;
            padding-right: 20px;
        }
        
        .info-column {
            display: table-cell;
            vertical-align: top;
        }
        
        .student-photo {
            width: 120px;
            height: 160px;
            object-fit: cover;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }
        
        .photo-placeholder {
            width: 120px;
            height: 160px;
            border-radius: 12px;
            background: linear-gradient(135deg, #e2e8f0 0%, #cbd5e0 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 9pt;
            color: #a0aec0;
            text-align: center;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        
        /* Info Grid */
        .info-grid {
            display: table;
            width: 100%;
        }
        
        .info-row {
            display: table-row;
        }
        
        .info-label {
            display: table-cell;
            width: 120px;
            padding: 4px 0;
            font-weight: 600;
            color: #4a5568;
            font-size: 9pt;
        }
        
        .info-value {
            display: table-cell;
            padding: 4px 0;
            color: #2d3748;
            font-size: 10pt;
            font-weight: 500;
        }
        
        /* Section 2: Thesis Title */
        .section-thesis {
            background: linear-gradient(135deg, #f7fafc 0%, #edf2f7 100%);
            border-radius: 12px;
            padding: 16px 20px;
            border-left: 4px solid #667eea;
        }
        
        .thesis-label {
            font-size: 8pt;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #667eea;
            margin-bottom: 6px;
        }
        
        .thesis-title {
            font-size: 11pt;
            font-weight: 600;
            color: #2d3748;
            line-height: 1.6;
            font-style: italic;
        }
        
        /* Student Number Badge */
        .student-number {
            display: inline-block;
            background: rgba(255, 255, 255, 0.3);
            padding: 2px 10px;
            border-radius: 20px;
            font-size: 9pt;
            margin-right: 8px;
        }

        /* Page Break */
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
            <div class="event-info">
                <p>{{ $event->date->format('l, d F Y') }}</p>
                <p>Pukul {{ $event->time->format('H:i') }} WIB</p>
                <p>{{ $event->location_name }}</p>
                <p style="margin-top: 15px; font-size: 10pt; color: #a0aec0;">
                    Total Wisudawan: {{ $mahasiswa->count() }}
                </p>
            </div>
        </div>
    </div>

    <!-- Content Pages -->
    @foreach($mahasiswa as $index => $mhs)
        <div class="student-card">
            <div class="student-header">
                <span class="student-number">#{{ $index + 1 }}</span>
                {{ $mhs->nama }}
            </div>
            
            <div class="student-body">
                <!-- Section 1: Photo + Info -->
                <div class="section-info">
                    <!-- Photo Column -->
                    <div class="photo-column">
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
                    
                    <!-- Info Column -->
                    <div class="info-column">
                        <div class="info-grid">
                            <div class="info-row">
                                <span class="info-label">NPM</span>
                                <span class="info-value">{{ $mhs->npm }}</span>
                            </div>
                            
                            <div class="info-row">
                                <span class="info-label">Nama Lengkap</span>
                                <span class="info-value">{{ $mhs->nama }}</span>
                            </div>
                            
                            <div class="info-row">
                                <span class="info-label">Program Studi</span>
                                <span class="info-value">{{ $mhs->program_studi ?? 'Belum diisi' }}</span>
                            </div>
                            
                            <div class="info-row">
                                <span class="info-label">IPK</span>
                                <span class="info-value">{{ $mhs->ipk ?? '-' }}</span>
                            </div>
                            
                            <div class="info-row">
                                <span class="info-label">Yudisium</span>
                                <span class="info-value">{{ $mhs->yudisium ?? '-' }}</span>
                            </div>
                            
                            <div class="info-row">
                                <span class="info-label">Email</span>
                                <span class="info-value">{{ $mhs->email ?? '-' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Section 2: Thesis Title -->
                @if($mhs->judul_skripsi)
                    <div class="section-thesis">
                        <div class="thesis-label">Judul Skripsi / Tugas Akhir</div>
                        <div class="thesis-title">{{ $mhs->judul_skripsi }}</div>
                    </div>
                @endif
            </div>
        </div>
        
        @if(($index + 1) % 2 === 0 && !$loop->last)
            <div style="page-break-after: always;"></div>
        @endif
    @endforeach
</body>
</html>