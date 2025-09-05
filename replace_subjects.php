<?php

$file = 'resources/js/Pages/SuperAdmin/MajorRecommendations.jsx';
$content = file_get_contents($file);

// New subjects array
$newSubjects = '    const availableSubjects = [
        // Mata Pelajaran Wajib (3 mata pelajaran)
        "Matematika Lanjutan",
        "Bahasa Indonesia Lanjutan", 
        "Bahasa Inggris Lanjutan",

        // Mata Pelajaran Pilihan (16 mata pelajaran)
        "Fisika",
        "Kimia", 
        "Biologi",
        "Ekonomi",
        "Sosiologi",
        "Geografi",
        "Sejarah",
        "Antropologi",
        "PPKn/Pendidikan Pancasila",
        "Bahasa Arab",
        "Bahasa Jerman",
        "Bahasa Prancis",
        "Bahasa Jepang",
        "Bahasa Korea",
        "Bahasa Mandarin",
        "Produk/Projek Kreatif dan Kewirausahaan",
    ];';

// Find and replace the availableSubjects array
$pattern = '/const availableSubjects = \[.*?\];/s';
$replacement = $newSubjects;

$newContent = preg_replace($pattern, $replacement, $content);

if ($newContent !== $content) {
    file_put_contents($file, $newContent);
    echo "✅ Successfully updated availableSubjects array\n";
} else {
    echo "❌ Failed to update availableSubjects array\n";
}
