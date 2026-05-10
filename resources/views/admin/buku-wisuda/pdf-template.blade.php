<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Buku Wisuda - {{ $event->name }}</title>
    <style>
        @page {
            margin: 2cm;
            size: A4 portrait;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12pt;
            line-height: 1.6;
            color: #000;
        }
        
        .cover {
            height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            page-break-after: always;
        }
        
        .cover h1 {
            font-size: 28pt;
            font-weight: bold;
            margin-bottom: 20px;
            text-transform: uppercase;
        }
        
        .cover h2 {
            font-size: 18pt;
            margin-bottom: 15px;
        }
        
        .cover .event-info {
            font-size: 14pt;
            margin-top: 30px;
        }
        
        .page-header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 2px solid #000;
        }
        
        .page-header h2 {
            font-size: 18pt;
            text-transform: uppercase;
        }
        
        .page-header p {
            font-size: 11pt;
            color: #333;
        }
        
        .student-entry {
            page-break-inside: avoid;
            margin-bottom: 30px;
            padding: 20px;
            border: 1px solid #ccc;
        }
        
        .student-header {
            display: flex;
            align-items: flex-start;
            gap: 20px;
        }
        
        .student-photo {
            width: 120px;
            height: 160px;
            object-fit: cover;
            border: 1px solid #999;
        }
        
        .student-photo-placeholder {
            width: 120px;
            height: 160px;
            border: 1px solid #999;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f5f5f5;
            font-size: 10pt;
            color: #666;
            text-align: center;
        }
        
        .student-info {
            flex: 1;
        }
        
        .student-info h3 {
            font-size: 16pt;
            font-weight: bold;
            margin-bottom: 10px;
            text-transform: uppercase;
        }
        
        .info-row {
            display: flex;
            margin-bottom: 5px;
            font-size: 11pt;
        }
        
        .info-label {
            width: 150px;
            font-weight: bold;
        }
        
        .info-value {
            flex: 1;
        }
        
        .thesis-title {
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #ddd;
        }
        
        .thesis-title h4 {
            font-size: 11pt;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .thesis-title p {
            font-size: 11pt;
            font-style: italic;
            text-align: justify;
        }
        
        .page-number {
            text-align: center;
            font-size: 10pt;
            margin-top: 20px;
            color: #666;
        }
    </style>
</head>
<body>
    <!-- Cover Page -->
    <div class="cover">
        <h1>Buku Wisuda</h1>
        <h2>{{ $event->name }}</h2>
        <div class="event-info">
            <p>{{ $event->date->format('l, d F Y') }}</p>
            <p>{{ $event->time->format('H:i') }} WIB</p>
            <p>{{ $event->location_name }}</p>
        </div>
    </div>

    <!-- Content Pages -->
    @foreach($mahasiswa as $index => $mhs)
        <div class="student-entry">
            <div class="student-header">
                @if($mhs->foto_wisuda && file_exists(public_path('storage/graduation-photos/' . $mhs->foto_wisuda)))
                    <img src="{{ public_path('storage/graduation-photos/' . $mhs->foto_wisuda) }}" 
                         alt="{{ $mhs->nama }}"
                         class="student-photo">
                @else
                    <div class="student-photo-placeholder">
                        Foto Tidak Tersedia
                    </div>
                @endif
                
                <div class="student-info">
                    <h3>{{ $mhs->nama }}</h3>
                    
                    <div class="info-row">
                        <span class="info-label">NPM:</span>
                        <span class="info-value">{{ $mhs->npm }}</span>
                    </div>
                    
                    <div class="info-row">
                        <span class="info-label">Program Studi:</span>
                        <span class="info-value">{{ $mhs->program_studi ?? '-' }}</span>
                    </div>
                    
                    <div class="info-row">
                        <span class="info-label">IPK:</span>
                        <span class="info-value">{{ $mhs->ipk ?? '-' }}</span>
                    </div>
                    
                    <div class="info-row">
                        <span class="info-label">Yudisium:</span>
                        <span class="info-value">{{ $mhs->yudisium ?? '-' }}</span>
                    </div>
                    
                    <div class="info-row">
                        <span class="info-label">Email:</span>
                        <span class="info-value">{{ $mhs->email ?? '-' }}</span>
                    </div>
                </div>
            </div>
            
            @if($mhs->judul_skripsi)
                <div class="thesis-title">
                    <h4>Judul Skripsi:</h4>
                    <p>{{ $mhs->judul_skripsi }}</p>
                </div>
            @endif
        </div>
        
        @if(($index + 1) % 2 === 0 && !$loop->last)
            <div style="page-break-after: always;"></div>
        @endif
    @endforeach
</body>
</html>