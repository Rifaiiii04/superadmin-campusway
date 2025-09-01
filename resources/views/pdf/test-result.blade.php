<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Hasil Tes TKAWEB</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #2563eb;
            margin: 0;
            font-size: 24px;
        }
        .student-info {
            background: #f8fafc;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .student-info table {
            width: 100%;
        }
        .student-info td {
            padding: 5px 10px;
        }
        .scores-section {
            margin-bottom: 25px;
        }
        .scores-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .scores-table th,
        .scores-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }
        .scores-table th {
            background: #f1f5f9;
            font-weight: bold;
        }
        .recommendations {
            background: #f0fdf4;
            padding: 15px;
            border-radius: 5px;
            border-left: 4px solid #22c55e;
        }
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 10px;
            color: #666;
        }
        .total-score {
            background: #dbeafe;
            padding: 15px;
            border-radius: 5px;
            text-align: center;
            margin: 20px 0;
        }
        .total-score h3 {
            margin: 0;
            color: #1d4ed8;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>HASIL TES TKAWEB</h1>
        <p>Test of Knowledge and Academic Web</p>
        <p>Tanggal: {{ date('d/m/Y H:i', strtotime($test_info['end_time'])) }}</p>
    </div>

    <div class="student-info">
        <h3>Informasi Siswa</h3>
        <table>
            <tr>
                <td><strong>Nama Lengkap:</strong></td>
                <td>{{ $student['nama_lengkap'] }}</td>
                <td><strong>NISN:</strong></td>
                <td>{{ $student['nisn'] }}</td>
            </tr>
            <tr>
                <td><strong>Nama Sekolah:</strong></td>
                <td>{{ $student['nama_sekolah'] }}</td>
                <td><strong>Kelas:</strong></td>
                <td>{{ $student['kelas'] }}</td>
            </tr>
        </table>
    </div>

    <div class="scores-section">
        <h3>Hasil Tes per Mata Pelajaran</h3>
        <table class="scores-table">
            <thead>
                <tr>
                    <th>Mata Pelajaran</th>
                    <th>Skor</th>
                    <th>Jawaban Benar</th>
                    <th>Total Soal</th>
                    <th>Persentase</th>
                </tr>
            </thead>
            <tbody>
                @foreach($scores as $score)
                <tr>
                    <td>{{ $score['subject'] }}</td>
                    <td>{{ $score['score'] }}</td>
                    <td>{{ $score['correct_answers'] }}</td>
                    <td>{{ $score['total_questions'] }}</td>
                    <td>{{ $score['percentage'] }}%</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="total-score">
        <h3>Total Skor: {{ $total_score }}</h3>
        <p>Rata-rata: {{ $average_score }}</p>
        <p>Durasi Tes: {{ $test_info['duration'] }} menit</p>
    </div>

    <div class="recommendations">
        <h3>Analisis & Rekomendasi</h3>
        
        @if(!empty($recommendations['strengths']))
        <p><strong>Kekuatan:</strong></p>
        <ul>
            @foreach($recommendations['strengths'] as $strength)
            <li>{{ $strength }}</li>
            @endforeach
        </ul>
        @endif

        @if(!empty($recommendations['weaknesses']))
        <p><strong>Area Perbaikan:</strong></p>
        <ul>
            @foreach($recommendations['weaknesses'] as $weakness)
            <li>{{ $weakness }}</li>
            @endforeach
        </ul>
        @endif

        <p><strong>Rekomendasi Jurusan:</strong></p>
        <ul>
            @foreach($recommendations['recommendations'] as $recommendation)
            <li>{{ $recommendation }}</li>
            @endforeach
        </ul>
    </div>

    <div class="footer">
        <p>Dokumen ini digenerate secara otomatis oleh sistem TKAWEB</p>
        <p>© {{ date('Y') }} TKAWEB - Test of Knowledge and Academic Web</p>
    </div>
</body>
</html>
