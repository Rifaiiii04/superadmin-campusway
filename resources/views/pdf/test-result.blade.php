<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil Tes - {{ $student->name }}</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f8f9fa;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .header {
            text-align: center;
            border-bottom: 3px solid #3b82f6;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #1e40af;
            margin: 0;
            font-size: 28px;
        }
        .header p {
            color: #6b7280;
            margin: 5px 0 0 0;
            font-size: 16px;
        }
        .student-info {
            background-color: #f1f5f9;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 25px;
        }
        .student-info h2 {
            color: #1e293b;
            margin: 0 0 15px 0;
            font-size: 20px;
        }
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }
        .info-item {
            display: flex;
            align-items: center;
        }
        .info-label {
            font-weight: bold;
            color: #475569;
            min-width: 120px;
        }
        .info-value {
            color: #1e293b;
        }
        .scores-section {
            margin-bottom: 25px;
        }
        .scores-section h2 {
            color: #1e293b;
            margin: 0 0 15px 0;
            font-size: 20px;
        }
        .score-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid #e2e8f0;
        }
        .score-item:last-child {
            border-bottom: none;
        }
        .subject-name {
            font-weight: 600;
            color: #1e293b;
        }
        .score-value {
            font-weight: bold;
            color: #059669;
        }
        .total-score {
            background-color: #dbeafe;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            margin-bottom: 25px;
        }
        .total-score h3 {
            color: #1e40af;
            margin: 0 0 10px 0;
            font-size: 22px;
        }
        .total-score .score {
            font-size: 32px;
            font-weight: bold;
            color: #059669;
        }
        .analysis-section {
            margin-bottom: 25px;
        }
        .analysis-section h2 {
            color: #1e293b;
            margin: 0 0 15px 0;
            font-size: 20px;
        }
        .strengths, .weaknesses {
            margin-bottom: 15px;
        }
        .strengths h4, .weaknesses h4 {
            color: #059669;
            margin: 0 0 8px 0;
            font-size: 16px;
        }
        .weaknesses h4 {
            color: #dc2626;
        }
        .subject-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }
        .subject-tag {
            background-color: #d1fae5;
            color: #065f46;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 500;
        }
        .weakness-tag {
            background-color: #fee2e2;
            color: #991b1b;
        }
        .recommendations-section {
            margin-bottom: 25px;
        }
        .recommendations-section h2 {
            color: #1e293b;
            margin: 0 0 15px 0;
            font-size: 20px;
        }
        .recommendation-item {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 15px;
        }
        .recommendation-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }
        .major-name {
            font-size: 18px;
            font-weight: bold;
            color: #1e40af;
        }
        .confidence-score {
            background-color: #3b82f6;
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
        }
        .major-description {
            color: #475569;
            margin-bottom: 15px;
            line-height: 1.5;
        }
        .career-prospects {
            background-color: #f0f9ff;
            border-left: 4px solid #0ea5e9;
            padding: 12px;
            margin-bottom: 15px;
        }
        .career-prospects h5 {
            color: #0c4a6e;
            margin: 0 0 8px 0;
            font-size: 14px;
        }
        .career-prospects p {
            color: #0369a1;
            margin: 0;
            font-size: 14px;
        }
        .requirements {
            background-color: #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 12px;
        }
        .requirements h5 {
            color: #92400e;
            margin: 0 0 8px 0;
            font-size: 14px;
        }
        .requirements-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            font-size: 14px;
        }
        .requirements-item {
            color: #92400e;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
            color: #6b7280;
            font-size: 14px;
        }
        .logo {
            width: 80px;
            height: 80px;
            background-color: #3b82f6;
            border-radius: 50%;
            margin: 0 auto 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 24px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">TKA</div>
            <h1>HASIL TES AKADEMIK</h1>
            <p>TKAWEB - Sistem Tes Akademik Online</p>
        </div>

        <div class="student-info">
            <h2>Informasi Siswa</h2>
            <div class="info-grid">
                <div class="info-item">
                    <span class="info-label">Nama Lengkap:</span>
                    <span class="info-value">{{ $student->name }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">NISN:</span>
                    <span class="info-value">{{ $student->nisn }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Sekolah:</span>
                    <span class="info-value">{{ $student->school->name }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Kelas:</span>
                    <span class="info-value">{{ $student->kelas }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Tanggal Tes:</span>
                    <span class="info-value">{{ \Carbon\Carbon::parse($testResult->start_time)->format('d/m/Y') }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Durasi:</span>
                    <span class="info-value">{{ $testResult->start_time->diffInMinutes($testResult->end_time) }} menit</span>
                </div>
            </div>
        </div>

        <div class="scores-section">
            <h2>Skor per Mata Pelajaran</h2>
            @foreach($scores as $score)
            <div class="score-item">
                <span class="subject-name">{{ $score['subject'] }}</span>
                <span class="score-value">{{ $score['score'] }}/{{ $score['total_questions'] }} ({{ $score['percentage'] }}%)</span>
            </div>
            @endforeach
        </div>

        <div class="total-score">
            <h3>Total Skor</h3>
            <div class="score">{{ $testResult->total_score }}/100</div>
        </div>

        <div class="analysis-section">
            <h2>Analisis Kekuatan & Kelemahan</h2>
            
            @if(!empty($recommendations['strengths']))
            <div class="strengths">
                <h4>✅ Kekuatan:</h4>
                <div class="subject-tags">
                    @foreach($recommendations['strengths'] as $strength)
                    <span class="subject-tag">{{ $strength }}</span>
                    @endforeach
                </div>
            </div>
            @endif

            @if(!empty($recommendations['weaknesses']))
            <div class="weaknesses">
                <h4>⚠️ Kelemahan:</h4>
                <div class="subject-tags">
                    @foreach($recommendations['weaknesses'] as $weakness)
                    <span class="subject-tag weakness-tag">{{ $weakness }}</span>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        <div class="recommendations-section">
            <h2>Rekomendasi Jurusan</h2>
            
            @if(isset($recommendations['recommendations']) && is_array($recommendations['recommendations']))
                @foreach($recommendations['recommendations'] as $recommendation)
                <div class="recommendation-item">
                    <div class="recommendation-header">
                        <span class="major-name">{{ $recommendation['major'] }}</span>
                        @if(isset($recommendation['confidence_score']) && $recommendation['confidence_score'] > 0)
                        <span class="confidence-score">{{ $recommendation['confidence_score'] }}%</span>
                        @endif
                    </div>
                    
                    @if(isset($recommendation['description']))
                    <div class="major-description">
                        {{ $recommendation['description'] }}
                    </div>
                    @endif

                    @if(isset($recommendation['career_prospects']))
                    <div class="career-prospects">
                        <h5>Prospek Karir:</h5>
                        <p>{{ $recommendation['career_prospects'] }}</p>
                    </div>
                    @endif

                    @if(isset($recommendation['requirements']) && !empty($recommendation['requirements']))
                    <div class="requirements">
                        <h5>Kriteria Masuk:</h5>
                        <div class="requirements-grid">
                            @if(isset($recommendation['requirements']['min_score']))
                            <div class="requirements-item">
                                <strong>Skor Min:</strong> {{ $recommendation['requirements']['min_score'] }}
                            </div>
                            @endif
                            @if(isset($recommendation['requirements']['max_score']))
                            <div class="requirements-item">
                                <strong>Skor Max:</strong> {{ $recommendation['requirements']['max_score'] }}
                            </div>
                            @endif
                            @if(isset($recommendation['requirements']['required_subjects']) && !empty($recommendation['requirements']['required_subjects']))
                            <div class="requirements-item">
                                <strong>Mata Pelajaran Wajib:</strong><br>
                                {{ implode(', ', $recommendation['requirements']['required_subjects']) }}
                            </div>
                            @endif
                            @if(isset($recommendation['requirements']['preferred_subjects']) && !empty($recommendation['requirements']['preferred_subjects']))
                            <div class="requirements-item">
                                <strong>Mata Pelajaran Preferensi:</strong><br>
                                {{ implode(', ', $recommendation['requirements']['preferred_subjects']) }}
                            </div>
                            @endif
                        </div>
                    </div>
                    @endif
                </div>
                @endforeach
            @else
                <div class="recommendation-item">
                    <div class="major-name">Konsultasi Guru BK</div>
                    <div class="major-description">
                        Konsultasi dengan guru BK untuk pemilihan jurusan yang sesuai dengan minat dan bakat Anda.
                    </div>
                </div>
            @endif
        </div>

        <div class="footer">
            <p>Dokumen ini digenerate secara otomatis oleh sistem TKAWEB</p>
            <p>Tanggal: {{ \Carbon\Carbon::now()->format('d/m/Y H:i:s') }}</p>
        </div>
    </div>
</body>
</html>
