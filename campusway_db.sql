-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 21, 2025 at 09:12 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `campusway_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` datetime(6) DEFAULT NULL,
  `updated_at` datetime(6) DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `name`, `username`, `password`, `created_at`, `updated_at`, `remember_token`) VALUES
(1, NULL, 'campusway_superadmin', '$2y$12$aTcRzrrzixcjMAh2ODKQAurv6Sbn.0WWEA2LU7pXpQuB22AEYLu/6', '2025-09-01 11:51:41.307000', '2025-10-07 10:06:16.000000', NULL),
(2, 'Super Admin', 'admin', '$2y$12$F7jKFhapw4LRXdFv6p50bue11RbsFrmmG2ilv4Bhv/H9k2i7uOxYq', '2025-10-04 11:03:18.000000', '2025-10-05 03:22:11.000000', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `answers`
--

CREATE TABLE `answers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `student_id` bigint(20) UNSIGNED NOT NULL,
  `question_id` bigint(20) UNSIGNED NOT NULL,
  `answer_text` varchar(0) NOT NULL,
  `is_correct` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime(6) DEFAULT NULL,
  `updated_at` datetime(6) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` varchar(0) NOT NULL,
  `queue` varchar(0) NOT NULL,
  `payload` varchar(0) NOT NULL,
  `exception` varchar(0) NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `majors`
--

CREATE TABLE `majors` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `rumpun_ilmu` varchar(255) DEFAULT NULL,
  `career_prospects` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `major_recommendations`
--

CREATE TABLE `major_recommendations` (
  `id` int(10) UNSIGNED NOT NULL,
  `major_name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `required_subjects` text DEFAULT NULL,
  `preferred_subjects` text DEFAULT NULL,
  `career_prospects` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime(6) DEFAULT NULL,
  `updated_at` datetime(6) DEFAULT NULL,
  `kurikulum_merdeka_subjects` text DEFAULT NULL,
  `kurikulum_2013_ipa_subjects` text DEFAULT NULL,
  `kurikulum_2013_ips_subjects` text DEFAULT NULL,
  `kurikulum_2013_bahasa_subjects` text DEFAULT NULL,
  `optional_subjects` longtext DEFAULT NULL,
  `category` varchar(255) NOT NULL DEFAULT 'Saintek'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `major_recommendations`
--

INSERT INTO `major_recommendations` (`id`, `major_name`, `description`, `required_subjects`, `preferred_subjects`, `career_prospects`, `is_active`, `created_at`, `updated_at`, `kurikulum_merdeka_subjects`, `kurikulum_2013_ipa_subjects`, `kurikulum_2013_ips_subjects`, `kurikulum_2013_bahasa_subjects`, `optional_subjects`, `category`) VALUES
(1, 'Test Major - CSRF UPDATE 08:55:38', 'Test description - CSRF UPDATE', '[\"Matematika\",\"Fisika\"]', '[\"Kimia\",\"Biologi\",\"Fisika\"]', 'Test career prospects', 1, '2025-09-27 13:47:25.000000', '2025-10-10 05:30:11.000000', '[]', '[]', '[]', '[]', '[]', 'Ilmu Alam'),
(2, 'Sejarah', 'Prodi Sejarah menekankan pada pemahaman peristiwa masa lalu, analisis kritis sumber sejarah, serta bagaimana sejarah membentuk identitas bangsa dan dinamika dunia. Mahasiswa mempelajari metode penelitian sejarah, penulisan akademik, dan interpretasi dokumen atau artefak. Pengalaman belajar sering melibatkan kunjungan lapangan ke situs bersejarah, arsip, dan museum. Lulusan tidak hanya siap menjadi sejarawan, guru, atau peneliti, tetapi juga berkontribusi pada bidang kebudayaan, media, dan kebijakan publik yang memerlukan perspektif historis.', '[]', '[{\"id\":11,\"name\":\"Antropologi\",\"code\":\"ANT\",\"subject_type\":\"Pilihan\"},{\"id\":22,\"name\":\"Bahasa Inggris Lanjutan\",\"code\":\"BIG_L\",\"subject_type\":\"Pilihan\"}]', 'Sejarawan, Peneliti, Kurator Museum, Penulis', 1, '2025-09-27 13:47:25.000000', '2025-10-09 13:16:40.000000', '[\"Sejarah\"]', '[\"Sejarah Indonesia\"]', '[\"Sejarah Indonesia\"]', '[\"Sejarah Indonesia\"]', '[]', 'Humaniora'),
(3, 'Linguistik', 'Prodi Linguistik mempelajari struktur bahasa, fonologi, morfologi, sintaksis, semantik, dan pragmatik, serta peran bahasa dalam komunikasi sehari-hari dan kebudayaan. Mahasiswa diajak menganalisis fenomena bahasa secara ilmiah, termasuk bahasa daerah dan bahasa asing, menggunakan teori modern dan pendekatan teknologi. Proses pembelajaran mencakup penelitian lapangan, analisis teks, hingga penerapan linguistik dalam bidang penerjemahan, pendidikan, dan teknologi bahasa. Lulusan berpotensi berkarier sebagai peneliti bahasa, dosen, ahli penerjemahan, atau pengembang aplikasi berbasis linguistik.', '\"[\\\"Bahasa Indonesia Tingkat Lanjut\\\",\\\"Bahasa Inggris\\\"]\"', '[\"Bahasa Indonesia Tingkat Lanjut\",\"Bahasa Inggris\"]', 'Linguis, Penerjemah, Editor, Peneliti Bahasa', 1, '2025-09-27 13:47:25.000000', '2025-09-29 05:49:31.000000', '[\"Bahasa Indonesia Tingkat Lanjut\",\"Bahasa Inggris\"]', '[\"Bahasa Indonesia\",\"Bahasa Inggris\"]', '[\"Bahasa Indonesia\",\"Bahasa Inggris\"]', '[\"Bahasa Indonesia\",\"Bahasa Inggris\"]', NULL, 'Humaniora'),
(4, 'Susastra atau Sastra', 'Prodi Susastra mengajarkan mahasiswa untuk memahami, menganalisis, dan mengapresiasi karya sastra baik klasik maupun modern, dari dalam negeri maupun luar negeri. Fokusnya tidak hanya pada isi cerita, tetapi juga konteks sosial, budaya, dan filsafat yang melatarbelakanginya. Mahasiswa berlatih menulis esai kritis, melakukan kajian sastra komparatif, hingga menciptakan karya sastra baru. Lingkungan belajar mendukung diskusi kreatif, penelitian teks, dan eksplorasi lintas budaya. Lulusan dapat menjadi penulis, kritikus sastra, editor, peneliti, atau pekerja di industri kreatif dan penerbitan.', '\"[\\\"Bahasa Indonesia Tingkat Lanjut\\\"]\"', '[\"Bahasa Indonesia Tingkat Lanjut\",\"Bahasa Asing yang Relevan\"]', 'Sastrawan, Kritikus Sastra, Editor, Penulis', 1, '2025-09-27 13:47:25.000000', '2025-09-29 05:49:31.000000', '[\"Bahasa Indonesia Tingkat Lanjut\",\"Bahasa Asing yang Relevan\"]', '[\"Bahasa Indonesia\",\"Bahasa Asing yang Relevan\"]', '[\"Bahasa Indonesia\",\"Bahasa Asing yang Relevan\"]', '[\"Bahasa Indonesia\",\"Bahasa Asing yang Relevan\"]', NULL, 'Humaniora'),
(5, 'Filsafat', 'Prodi Filsafat mengembangkan kemampuan berpikir kritis, analitis, dan reflektif melalui kajian terhadap gagasan besar sepanjang sejarah manusia. Mahasiswa mempelajari tokoh-tokoh dan aliran filsafat, etika, logika, metafisika, hingga filsafat ilmu. Pendekatan pembelajaran lebih banyak melalui diskusi, debat intelektual, serta penulisan argumentatif yang melatih mahasiswa menyusun ide dengan logis. Lulusan prodi ini diharapkan menjadi pemikir yang mampu berkontribusi dalam bidang akademik, pendidikan, penulisan, maupun pekerjaan yang membutuhkan analisis mendalam seperti kebijakan publik dan konsultan etika.', '[\"Bahasa Indonesia\",\"Matematika\",\"Bahasa Inggris\"]', '[{\"id\":17,\"name\":\"Bahasa Korea\",\"code\":\"BKO\",\"subject_type\":\"Pilihan\"},{\"id\":4,\"name\":\"Fisika\",\"code\":\"FIS\",\"subject_type\":\"Pilihan\"}]', 'Filsuf, Peneliti, Dosen, Konsultan', 1, '2025-09-27 13:47:25.000000', '2025-10-10 06:05:23.000000', '[\"Sosiologi\",{\"id\":12,\"name\":\"PPKn\",\"code\":\"PPKN\",\"subject_type\":\"Pilihan\"}]', '[\"Sejarah Indonesia\"]', '[\"Sosiologi\"]', '[\"Antropologi\",{\"id\":8,\"name\":\"Sosiologi\",\"code\":\"SOS\",\"subject_type\":\"Pilihan\"}]', '[{\"id\":17,\"name\":\"Bahasa Korea\",\"code\":\"BKO\",\"subject_type\":\"Pilihan\"},{\"id\":4,\"name\":\"Fisika\",\"code\":\"FIS\",\"subject_type\":\"Pilihan\"}]', 'Humaniora'),
(6, 'Sosial', 'Prodi Sosial berfokus pada pemahaman struktur, dinamika, dan peran masyarakat dalam kehidupan sehari-hari. Mahasiswa mempelajari teori sosiologi, antropologi, serta fenomena sosial yang terjadi di berbagai lapisan masyarakat. Proses pembelajaran menekankan pada riset lapangan, analisis kasus nyata, serta diskusi kritis untuk membangun kemampuan memahami masalah sosial dari berbagai perspektif. Lulusan prodi ini dapat berkarier sebagai peneliti sosial, konsultan kebijakan, pekerja sosial, maupun berkontribusi di lembaga pemerintah dan organisasi non-pemerintah yang menangani isu masyarakat.', '\"[\\\"Sosiologi\\\"]\"', '[\"Sosiologi\",\"Antropologi\"]', 'Sosiolog, Peneliti Sosial, Konsultan, Aktivis', 1, '2025-09-27 13:47:25.000000', '2025-09-29 05:49:31.000000', '[\"Sosiologi\"]', '[\"Sejarah Indonesia\"]', '[\"Sosiologi\"]', '[\"Antropologi\"]', NULL, 'Ilmu Sosial'),
(7, 'Ekonomi', 'Prodi Ekonomi membekali mahasiswa dengan pemahaman tentang teori ekonomi mikro dan makro, kebijakan fiskal, moneter, serta dinamika pasar dalam skala lokal maupun global. Mahasiswa juga dilatih dalam penggunaan alat analisis ekonomi, statistik, dan pemodelan untuk membaca data dan merumuskan solusi terhadap permasalahan ekonomi. Pengalaman belajar mencakup simulasi kebijakan, studi kasus industri, hingga riset empiris. Lulusan prodi ini diharapkan mampu berkontribusi sebagai ekonom, analis keuangan, konsultan bisnis, atau pengambil kebijakan di sektor publik dan swasta.', '\"[\\\"Ekonomi\\\",\\\"Matematika\\\"]\"', '[\"Ekonomi\",\"Matematika\"]', 'Ekonom, Analis Keuangan, Konsultan, Bankir', 1, '2025-09-27 13:47:25.000000', '2025-09-29 05:49:31.000000', '[\"Ekonomi\",\"Matematika\"]', '[\"Matematika\"]', '[\"Ekonomi\",\"Matematika\"]', '[\"Matematika\"]', NULL, 'Ilmu Sosial'),
(8, 'Pertahanan', 'Prodi Pertahanan mempelajari konsep keamanan nasional, strategi pertahanan, dan manajemen konflik dalam konteks lokal maupun internasional. Mahasiswa diajak memahami aspek politik, hukum, serta teknologi yang mendukung pertahanan negara. Pembelajaran menggabungkan teori, studi kasus, serta praktik analisis strategi militer dan kebijakan keamanan. Lulusan prodi ini dipersiapkan untuk berkarier di institusi pertahanan, lembaga pemerintah, maupun organisasi yang bergerak di bidang keamanan, diplomasi, serta hubungan internasional.', '\"[\\\"Pendidikan Pancasila\\\"]\"', '[\"PPKn\",\"Sejarah Indonesia\"]', 'TNI, Polri, Analis Keamanan, Konsultan Pertahanan', 1, '2025-09-27 13:47:25.000000', '2025-09-29 05:49:31.000000', '[\"Pendidikan Pancasila\"]', '[\"PPKn\"]', '[\"PPKn\"]', '[\"PPKn\"]', NULL, 'Ilmu Sosial'),
(9, 'Psikologi', 'Prodi Psikologi fokus pada studi tentang perilaku, pikiran, dan proses mental manusia. Mahasiswa mempelajari teori-teori psikologi, metode penelitian, serta aplikasi psikologi dalam berbagai bidang seperti pendidikan, klinis, industri, dan organisasi. Proses pembelajaran melibatkan eksperimen, observasi, serta praktik konseling untuk memahami manusia secara utuh. Lulusan prodi ini memiliki peluang berkarier sebagai psikolog, konselor, peneliti, pengembang sumber daya manusia, maupun bekerja di bidang kesehatan mental dan kesejahteraan masyarakat.', '\"[\\\"Sosiologi\\\",\\\"Matematika\\\"]\"', '[\"Sosiologi\",\"Matematika\"]', 'Psikolog, Konselor, Peneliti, HRD', 1, '2025-09-27 13:47:25.000000', '2025-09-29 05:49:31.000000', '[\"Sosiologi\",\"Matematika\"]', '[\"Matematika\"]', '[\"Sosiologi\"]', '[\"Matematika\"]', NULL, 'Ilmu Sosial'),
(10, 'Kimia', 'Prodi Kimia mempelajari struktur, sifat, reaksi, dan aplikasi senyawa kimia yang membentuk kehidupan dan teknologi modern. Mahasiswa mendapatkan dasar teori kimia anorganik, organik, fisik, dan biokimia, serta pengalaman praktikum laboratorium yang intensif. Pembelajaran juga menekankan pada riset eksperimental, analisis instrumen, dan penerapan kimia dalam industri farmasi, energi, pangan, hingga lingkungan. Lulusan prodi ini memiliki peluang berkarier sebagai peneliti, analis laboratorium, pengembang produk, atau ahli kimia di berbagai sektor industri.', '\"[\\\"Kimia\\\"]\"', '[\"Kimia\",\"Matematika\"]', 'Ahli Kimia, Peneliti, Quality Control, Konsultan', 1, '2025-09-27 13:47:25.000000', '2025-09-29 05:49:31.000000', '[\"Kimia\"]', '[\"Kimia\"]', '[\"Kimia\"]', '[\"Kimia\"]', NULL, 'Ilmu Alam'),
(11, 'Ilmu atau Sains Kebumian', 'Prodi Ilmu atau Sains Kebumian fokus pada kajian geologi, geofisika, dan dinamika bumi untuk memahami proses alam seperti gempa bumi, gunung berapi, serta struktur batuan dan mineral. Mahasiswa dilatih menggunakan metode survei lapangan, pemetaan geologi, serta teknologi analisis data kebumian. Pengalaman belajar mencakup penelitian langsung di lapangan, pemodelan komputer, dan analisis laboratorium. Lulusan dapat berkarier sebagai ahli geologi, konsultan tambang, peneliti kebencanaan, maupun profesional di bidang eksplorasi energi dan lingkungan.', '\"[\\\"Fisika\\\",\\\"Matematika Tingkat Lanjut\\\"]\"', '[\"Fisika\",\"Matematika Tingkat Lanjut\"]', NULL, 1, '2025-09-27 13:47:25.000000', '2025-09-29 05:49:31.000000', '[\"Fisika\",\"Matematika Tingkat Lanjut\"]', '[\"Fisika\",\"Matematika\"]', '[\"Fisika\",\"Geografi\"]', '[\"Fisika\",\"Matematika\"]', NULL, 'Ilmu Alam'),
(12, 'Ilmu atau Sains Kelautan', 'Prodi Ilmu atau Sains Kelautan mempelajari ekosistem laut, biota, serta pemanfaatan sumber daya kelautan secara berkelanjutan. Mahasiswa akan terlibat dalam studi biologi laut, oseanografi, serta teknologi eksplorasi laut. Kegiatan belajar banyak dilakukan melalui penelitian lapangan di pantai, laut, dan laboratorium kelautan. Prodi ini menekankan pentingnya konservasi dan inovasi pemanfaatan laut untuk pangan, energi, maupun pariwisata. Lulusan berpotensi menjadi ahli kelautan, peneliti lingkungan laut, atau pengelola sumber daya perikanan dan pariwisata bahari.', '\"[\\\"Biologi\\\"]\"', '[\"Biologi\",\"Geografi\"]', NULL, 1, '2025-09-27 13:47:25.000000', '2025-09-29 05:49:31.000000', '[\"Biologi\"]', '[\"Biologi\"]', '[\"Biologi\",\"Geografi\"]', '[\"Biologi\"]', NULL, 'Ilmu Alam'),
(13, 'Biologi', 'Prodi Biologi mempelajari kehidupan mulai dari organisme mikroskopis hingga ekosistem yang kompleks. Mahasiswa mendalami topik genetika, ekologi, mikrobiologi, zoologi, dan botani, serta berlatih melakukan penelitian ilmiah di laboratorium maupun alam. Proses belajar menekankan keterampilan observasi, eksperimen, dan analisis data biologi. Lulusan dapat berkarier di bidang pendidikan, penelitian, bioteknologi, konservasi lingkungan, maupun industri kesehatan dan pangan.', '\"[\\\"Biologi\\\"]\"', '[\"Biologi\",\"Kimia\"]', 'Biolog, Peneliti, Konservasionis, Dosen', 1, '2025-09-27 13:47:25.000000', '2025-09-29 05:49:31.000000', '[\"Biologi\"]', '[\"Biologi\"]', '[\"Biologi\"]', '[\"Biologi\"]', NULL, 'Ilmu Alam'),
(14, 'Biofisika', 'Prodi Biofisika menggabungkan ilmu biologi dan fisika untuk memahami fenomena kehidupan pada tingkat molekuler hingga sistemik. Mahasiswa mempelajari bioenergetika, mekanisme molekul, serta penggunaan alat fisika dalam penelitian biologi. Proses pembelajaran mencakup eksperimen laboratorium, pemodelan komputer, dan analisis data kuantitatif. Lulusan dapat berkarier sebagai peneliti di bidang kesehatan, bioteknologi, bioinformatika, serta industri yang menggabungkan teknologi dengan ilmu hayati.', '\"[\\\"Fisika\\\"]\"', '[\"Fisika\",\"Matematika\"]', NULL, 1, '2025-09-27 13:47:25.000000', '2025-09-29 05:49:31.000000', '[\"Fisika\"]', '[\"Fisika\"]', '[\"Fisika\"]', '[\"Fisika\"]', NULL, 'Ilmu Alam'),
(15, 'Fisika', 'Prodi Fisika mempelajari hukum-hukum dasar alam semesta, mulai dari partikel subatom hingga kosmos. Mahasiswa mendalami mekanika, elektromagnetisme, termodinamika, fisika kuantum, dan fisika material, serta mengembangkan keterampilan pemecahan masalah berbasis matematis. Praktikum laboratorium dan riset menjadi bagian penting dalam pembelajaran. Lulusan dapat berkontribusi dalam penelitian, pendidikan, teknologi energi, instrumentasi, dan industri berbasis teknologi tinggi.', '\"[\\\"Fisika\\\"]\"', '[\"Fisika\",\"Matematika\"]', 'Fisikawan, Peneliti, Engineer, Dosen', 1, '2025-09-27 13:47:25.000000', '2025-09-29 05:49:31.000000', '[\"Fisika\"]', '[\"Fisika\"]', '[\"Fisika\"]', '[\"Fisika\"]', NULL, 'Ilmu Alam'),
(16, 'Astronomi', 'Prodi Astronomi berfokus pada studi tentang benda-benda langit, struktur galaksi, evolusi bintang, dan kosmologi. Mahasiswa mempelajari fisika astronomi, penggunaan teleskop, serta pemodelan komputer untuk memahami fenomena alam semesta. Belajar sering melibatkan observasi langsung di observatorium dan analisis data astronomi. Lulusan dapat berkarier sebagai peneliti astronomi, pengajar, atau bekerja di pusat sains dan teknologi antariksa.', '\"[\\\"Fisika\\\",\\\"Matematika Tingkat Lanjut\\\"]\"', '[\"Fisika\",\"Matematika Tingkat Lanjut\"]', NULL, 1, '2025-09-27 13:47:25.000000', '2025-09-29 05:49:31.000000', '[\"Fisika\",\"Matematika Tingkat Lanjut\"]', '[\"Fisika\",\"Matematika\"]', '[\"Fisika\",\"Matematika\"]', '[\"Fisika\",\"Matematika\"]', NULL, 'Ilmu Alam'),
(17, 'Komputer', 'Prodi Komputer mengajarkan dasar ilmu komputer, algoritma, pemrograman, jaringan, dan kecerdasan buatan. Mahasiswa dilatih untuk merancang sistem perangkat lunak maupun perangkat keras, serta memahami teori komputasi dan penerapannya dalam kehidupan sehari-hari. Proses pembelajaran banyak berbasis proyek untuk menciptakan aplikasi atau solusi teknologi. Lulusan berpeluang menjadi software engineer, data scientist, ahli keamanan siber, maupun peneliti teknologi informasi.', '\"[\\\"Matematika Tingkat Lanjut\\\"]\"', '[\"Matematika Tingkat Lanjut\",\"Fisika\"]', NULL, 1, '2025-09-27 13:47:25.000000', '2025-09-29 05:49:31.000000', '[\"Matematika Tingkat Lanjut\"]', '[\"Matematika\"]', '[\"Matematika\"]', '[\"Matematika\"]', NULL, 'Ilmu Formal'),
(18, 'Logika', 'Prodi Logika mempelajari prinsip-prinsip penalaran formal, logika simbolik, teori himpunan, dan filsafat matematika. Mahasiswa mengembangkan keterampilan berpikir analitis, kritis, dan sistematis untuk menyelesaikan masalah kompleks. Pembelajaran melibatkan latihan penalaran, pemodelan formal, serta penerapan logika dalam ilmu komputer, hukum, dan filsafat. Lulusan dapat berkarier sebagai peneliti, akademisi, atau profesional di bidang teknologi dan analisis data.', '\"[\\\"Matematika Tingkat Lanjut\\\"]\"', '[\"Matematika Tingkat Lanjut\",\"Bahasa Inggris\"]', NULL, 1, '2025-09-27 13:47:25.000000', '2025-09-29 05:49:32.000000', '[\"Matematika Tingkat Lanjut\"]', '[\"Matematika\"]', '[\"Matematika\"]', '[\"Matematika\"]', NULL, 'Ilmu Formal'),
(19, 'Matematika', 'Prodi Matematika membekali mahasiswa dengan pemahaman teori bilangan, aljabar, kalkulus, geometri, analisis, serta statistik. Mahasiswa dilatih untuk berpikir abstrak, memecahkan masalah, dan menggunakan model matematis dalam berbagai bidang. Proses belajar menggabungkan kuliah teori, latihan soal, penelitian, dan aplikasi nyata. Lulusan dapat berkarier sebagai peneliti, pengajar, analis data, aktuaria, atau profesional di bidang teknologi dan keuangan.', '\"[\\\"Matematika Tingkat Lanjut\\\"]\"', '[\"Matematika Tingkat Lanjut\",\"Fisika\"]', NULL, 1, '2025-09-27 13:47:25.000000', '2025-09-29 05:49:32.000000', '[\"Matematika Tingkat Lanjut\"]', '[\"Matematika\"]', '[\"Matematika\"]', '[\"Matematika\"]', NULL, 'Ilmu Formal'),
(20, 'Ilmu dan Sains Pertanian', 'Prodi Ilmu dan Sains Pertanian fokus pada produksi tanaman, teknologi budidaya, dan pengelolaan sumber daya alam untuk mendukung ketahanan pangan. Mahasiswa mempelajari agronomi, tanah, hama, penyakit tanaman, serta teknologi pertanian modern. Praktik lapangan dan riset menjadi bagian penting dalam proses belajar. Lulusan dapat bekerja sebagai peneliti, penyuluh pertanian, pengusaha agribisnis, atau ahli di bidang ketahanan pangan dan teknologi pertanian.', '\"[\\\"Biologi\\\"]\"', '[\"Biologi\",\"Kimia\"]', NULL, 1, '2025-09-27 13:47:25.000000', '2025-09-29 05:49:32.000000', '[\"Biologi\"]', '[\"Biologi\"]', '[\"Biologi\"]', '[\"Biologi\"]', NULL, 'Ilmu Terapan'),
(21, 'Peternakan', 'Prodi Peternakan membekali mahasiswa dengan ilmu tentang pemeliharaan, perawatan, dan pengembangan hewan ternak untuk menghasilkan pangan dan produk turunan. Mahasiswa mempelajari genetika ternak, nutrisi, kesehatan hewan, serta teknologi peternakan. Pembelajaran mencakup laboratorium, praktik lapangan, dan kerja sama dengan industri peternakan. Lulusan dapat bekerja sebagai manajer peternakan, konsultan agribisnis, peneliti, atau wirausahawan di bidang pangan hewani.', '\"[\\\"Biologi\\\"]\"', '[\"Biologi\",\"Kimia\"]', NULL, 1, '2025-09-27 13:47:25.000000', '2025-09-29 05:49:32.000000', '[\"Biologi\"]', '[\"Biologi\"]', '[\"Biologi\"]', '[\"Biologi\"]', NULL, 'Ilmu Terapan'),
(22, 'Ilmu atau Sains Perikanan', 'Prodi Ilmu atau Sains Perikanan berfokus pada pengelolaan sumber daya perairan, budidaya ikan, dan teknologi perikanan. Mahasiswa mempelajari biologi perairan, konservasi, serta teknologi penangkapan dan pengolahan hasil perikanan. Proses belajar mencakup penelitian lapangan, laboratorium, serta praktik di kawasan pesisir. Lulusan dapat berkarier sebagai peneliti perikanan, pengelola akuakultur, atau profesional di sektor industri perikanan berkelanjutan.', '\"[\\\"Biologi\\\"]\"', '[\"Biologi\",\"Kimia\"]', NULL, 1, '2025-09-27 13:47:25.000000', '2025-09-29 05:49:32.000000', '[\"Biologi\"]', '[\"Biologi\"]', '[\"Biologi\"]', '[\"Biologi\"]', NULL, 'Ilmu Terapan'),
(23, 'Arsitektur', 'Prodi Arsitektur mengajarkan perencanaan, perancangan, dan pembangunan ruang serta bangunan yang fungsional, estetis, dan berkelanjutan. Mahasiswa mempelajari teori arsitektur, desain bangunan, teknologi konstruksi, dan perencanaan kota. Proses belajar berbasis studio desain, proyek nyata, serta penggunaan perangkat lunak desain modern. Lulusan dapat menjadi arsitek, perencana kota, konsultan desain, atau pengembang properti.', '\"[\\\"Matematika\\\",\\\"Fisika\\\"]\"', '[\"Matematika\",\"Fisika\"]', NULL, 1, '2025-09-27 13:47:25.000000', '2025-09-29 05:49:32.000000', '[\"Matematika\",\"Fisika\"]', '[\"Matematika\",\"Fisika\"]', '[\"Matematika\",\"Fisika\"]', '[\"Matematika\",\"Fisika\"]', NULL, 'Ilmu Terapan'),
(24, 'Perencanaan Wilayah', 'Prodi Perencanaan Wilayah fokus pada pengelolaan ruang dan wilayah untuk menciptakan lingkungan yang tertata, produktif, dan berkelanjutan. Mahasiswa mempelajari teori tata ruang, ekonomi wilayah, transportasi, serta analisis kebijakan pembangunan. Proses belajar melibatkan simulasi perencanaan, penelitian lapangan, dan penggunaan teknologi informasi geografis. Lulusan dapat bekerja sebagai perencana kota, konsultan pembangunan, atau pejabat di lembaga perencanaan pemerintah.', '\"[\\\"Ekonomi\\\",\\\"Matematika\\\"]\"', '[\"Ekonomi\",\"Matematika\"]', NULL, 1, '2025-09-27 13:47:25.000000', '2025-09-29 05:49:32.000000', '[\"Ekonomi\",\"Matematika\"]', '[\"Matematika\"]', '[\"Ekonomi\",\"Matematika\"]', '[\"Matematika\"]', NULL, 'Ilmu Terapan'),
(25, 'Desain', 'Prodi Desain mengajarkan keterampilan menciptakan karya visual, produk, atau ruang yang fungsional sekaligus estetis. Mahasiswa belajar teori desain, prinsip komunikasi visual, teknologi digital, serta riset pengguna. Proses pembelajaran berbasis studio, proyek nyata, dan kolaborasi dengan industri kreatif. Lulusan dapat menjadi desainer grafis, desainer produk, ilustrator, atau profesional di bidang desain digital dan industri kreatif.', '\"[\\\"Seni Budaya\\\",\\\"Matematika\\\"]\"', '[\"Seni Budaya\",\"Matematika\"]', NULL, 1, '2025-09-27 13:47:25.000000', '2025-09-29 05:49:32.000000', '[\"Seni Budaya\",\"Matematika\"]', '[\"Seni Budaya\",\"Matematika\"]', '[\"Seni Budaya\",\"Matematika\"]', '[\"Seni Budaya\",\"Matematika\"]', NULL, 'Ilmu Terapan'),
(26, 'Ilmu atau Sains Akuntansi', 'Prodi Ilmu atau Sains Akuntansi membekali mahasiswa dengan keterampilan mencatat, menganalisis, dan melaporkan informasi keuangan. Mahasiswa mempelajari teori akuntansi, audit, perpajakan, dan sistem informasi akuntansi. Proses belajar mencakup simulasi pencatatan keuangan, penggunaan software akuntansi, dan studi kasus perusahaan. Lulusan dapat bekerja sebagai akuntan publik, auditor, konsultan pajak, atau analis keuangan.', '\"[\\\"Ekonomi\\\"]\"', '[\"Ekonomi\",\"Matematika\"]', NULL, 1, '2025-09-27 13:47:25.000000', '2025-09-29 05:49:32.000000', '[\"Ekonomi\"]', '[\"Fisika\",\"Kimia\",\"Matematika\"]', '[\"Fisika\",\"Kimia\"]', '[\"Fisika\",\"Kimia\",\"Matematika\"]', NULL, 'Ilmu Sosial'),
(27, 'Ilmu atau Sains Manajemen', 'Prodi Ilmu atau Sains Manajemen fokus pada perencanaan, pengorganisasian, pengendalian, dan kepemimpinan dalam organisasi. Mahasiswa mempelajari teori manajemen, pemasaran, sumber daya manusia, keuangan, dan operasional. Proses pembelajaran melibatkan studi kasus, simulasi bisnis, serta proyek kolaboratif. Lulusan dapat berkarier sebagai manajer, konsultan bisnis, entrepreneur, atau pemimpin organisasi di sektor publik dan swasta.', '\"[\\\"Ekonomi\\\"]\"', '[\"Ekonomi\",\"Matematika\"]', NULL, 1, '2025-09-27 13:47:25.000000', '2025-09-29 05:49:32.000000', '[\"Ekonomi\"]', '[\"Fisika\",\"Kimia\",\"Matematika\"]', '[\"Fisika\",\"Kimia\"]', '[\"Fisika\",\"Kimia\",\"Matematika\"]', NULL, 'Ilmu Sosial'),
(28, 'Logistik', 'Prodi Logistik mengajarkan manajemen rantai pasok, transportasi, distribusi, dan pergudangan untuk memastikan efisiensi arus barang dan jasa. Mahasiswa mempelajari teori logistik, teknologi informasi logistik, serta praktik operasional. Proses belajar mencakup studi kasus industri, simulasi rantai pasok, dan kerja sama dengan perusahaan logistik. Lulusan dapat menjadi manajer logistik, analis supply chain, atau konsultan manajemen distribusi.', '\"[\\\"Ekonomi\\\"]\"', '[\"Ekonomi\",\"Matematika\"]', NULL, 1, '2025-09-27 13:47:25.000000', '2025-09-29 05:49:32.000000', '[\"Ekonomi\"]', '[\"Fisika\",\"Kimia\",\"Matematika\"]', '[\"Fisika\",\"Kimia\"]', '[\"Fisika\",\"Kimia\",\"Matematika\"]', NULL, 'Ilmu Sosial'),
(29, 'Administrasi Bisnis', 'Prodi Administrasi Bisnis membekali mahasiswa dengan pengetahuan administrasi, manajemen perkantoran, dan sistem informasi bisnis. Mahasiswa belajar tentang keuangan, pemasaran, sumber daya manusia, serta manajemen operasional. Proses pembelajaran menekankan keterampilan praktis, simulasi bisnis, dan penggunaan teknologi administrasi modern. Lulusan dapat bekerja sebagai administrator, staf manajemen, konsultan bisnis, atau entrepreneur.', '\"[\\\"Ekonomi\\\"]\"', '[\"Ekonomi\",\"Matematika\"]', NULL, 1, '2025-09-27 13:47:25.000000', '2025-09-29 05:49:32.000000', '[\"Ekonomi\"]', '[\"Fisika\",\"Kimia\",\"Matematika\"]', '[\"Fisika\",\"Kimia\"]', '[\"Fisika\",\"Kimia\",\"Matematika\"]', NULL, 'Ilmu Terapan'),
(30, 'Bisnis', 'Prodi Bisnis mempelajari strategi pengembangan usaha, manajemen pemasaran, keuangan, serta inovasi produk dan layanan. Mahasiswa dilatih untuk berpikir kreatif, mengambil keputusan strategis, dan memahami dinamika pasar. Proses belajar berbasis proyek, studi kasus, serta kerja sama dengan dunia usaha. Lulusan dapat berkarier sebagai wirausahawan, manajer bisnis, konsultan, atau pengembang startup inovatif.', '\"[\\\"Ekonomi\\\"]\"', '[\"Ekonomi\",\"Matematika\"]', NULL, 1, '2025-09-27 13:47:25.000000', '2025-09-29 05:49:32.000000', '[\"Ekonomi\"]', '[\"Fisika\",\"Kimia\",\"Matematika\"]', '[\"Fisika\",\"Kimia\"]', '[\"Fisika\",\"Kimia\",\"Matematika\"]', NULL, 'Ilmu Sosial'),
(31, 'Ilmu atau Sains Komunikasi', 'Prodi Ilmu atau Sains Komunikasi mempelajari teori dan praktik komunikasi, baik interpersonal, organisasi, maupun massa. Mahasiswa belajar tentang media, jurnalistik, hubungan masyarakat, periklanan, hingga komunikasi digital. Proses pembelajaran melibatkan proyek kreatif, produksi media, riset audiens, dan praktik di lapangan. Lulusan dapat berkarier sebagai jurnalis, public relations, content creator, atau konsultan komunikasi di berbagai sektor.', '\"[\\\"Sosiologi\\\",\\\"Antropologi\\\"]\"', '[\"Sosiologi\",\"Antropologi\"]', NULL, 1, '2025-09-27 13:47:25.000000', '2025-09-29 05:49:32.000000', '[\"Sosiologi\",\"Antropologi\"]', '[\"Fisika\",\"Kimia\",\"Matematika\"]', '[\"Fisika\",\"Kimia\"]', '[\"Fisika\",\"Kimia\",\"Matematika\"]', NULL, 'Ilmu Sosial'),
(32, 'Pendidikan', 'Prodi Pendidikan membekali mahasiswa dengan teori pedagogi, kurikulum, psikologi pendidikan, serta strategi pengajaran yang efektif. Mahasiswa dilatih dalam microteaching, praktik mengajar, serta penggunaan teknologi pendidikan. Prodi ini menekankan pembentukan guru dan pendidik yang profesional, inovatif, dan berkarakter. Lulusan siap menjadi guru, pengembang kurikulum, konselor pendidikan, atau praktisi di lembaga pelatihan.', '\"[\\\"Sosiologi\\\",\\\"Antropologi\\\"]\"', '[\"Bahasa Indonesia\",\"Matematika\"]', NULL, 1, '2025-09-27 13:47:25.000000', '2025-09-29 05:49:32.000000', '[\"Sosiologi\",\"Antropologi\"]', '[\"Fisika\",\"Kimia\",\"Matematika\"]', '[\"Fisika\",\"Kimia\"]', '[\"Fisika\",\"Kimia\",\"Matematika\"]', NULL, 'Ilmu Sosial'),
(33, 'Teknik rekayasa', 'Prodi Teknik atau Rekayasa fokus pada penerapan prinsip ilmiah dan teknologi untuk merancang, membangun, dan mengelola sistem atau produk. Mahasiswa mempelajari matematika, fisika, dan teknologi sesuai bidang teknik yang dipilih, seperti teknik sipil, mesin, elektro, atau industri. Proses pembelajaran mencakup praktikum, proyek rekayasa, dan kerja sama dengan industri. Lulusan dapat berkarier sebagai insinyur, konsultan teknik, peneliti, maupun pengembang teknologi.', '\"[\\\"Fisika\\\",\\\"Kimia\\\",\\\"Matematika Tingkat Lanjut\\\"]\"', '[\"Fisika\",\"Matematika Tingkat Lanjut\"]', NULL, 1, '2025-09-27 13:47:25.000000', '2025-09-29 05:49:32.000000', '[\"Fisika\",\"Kimia\",\"Matematika Tingkat Lanjut\"]', '[\"Fisika\",\"Kimia\",\"Matematika\"]', '[\"Fisika\",\"Kimia\",\"Matematika\"]', '[\"Fisika\",\"Kimia\",\"Matematika\"]', NULL, 'Ilmu Terapan'),
(34, 'Ilmu atau Sains Lingkungan', 'Prodi Ilmu atau Sains Lingkungan mempelajari interaksi manusia dengan alam serta cara mengelola lingkungan secara berkelanjutan. Mahasiswa mempelajari ekologi, pencemaran, konservasi, serta teknologi pengelolaan lingkungan. Proses belajar mencakup riset lapangan, analisis laboratorium, dan simulasi kebijakan lingkungan. Lulusan dapat bekerja sebagai konsultan lingkungan, peneliti, analis kebijakan, atau aktivis di bidang keberlanjutan.', '\"[\\\"Biologi\\\"]\"', '[\"Biologi\",\"Kimia\"]', NULL, 1, '2025-09-27 13:47:25.000000', '2025-09-29 05:49:32.000000', '[\"Biologi\"]', '[\"Biologi\"]', '[\"Biologi\"]', '[\"Biologi\"]', NULL, 'Ilmu Terapan'),
(35, 'Kehutanan', 'Prodi Kehutanan fokus pada pengelolaan hutan, konservasi, dan pemanfaatan sumber daya hutan secara lestari. Mahasiswa mempelajari ekologi hutan, manajemen hutan, teknologi hasil hutan, serta kebijakan kehutanan. Kegiatan belajar melibatkan praktik lapangan, penelitian, dan kerja sama dengan industri kehutanan. Lulusan dapat menjadi rimbawan, peneliti kehutanan, konsultan konservasi, atau pengelola kawasan hutan.', '\"[\\\"Biologi\\\"]\"', '[\"Biologi\",\"Geografi\"]', NULL, 1, '2025-09-27 13:47:25.000000', '2025-09-29 05:49:32.000000', '[\"Biologi\"]', '[\"Biologi\"]', '[\"Biologi\"]', '[\"Biologi\"]', NULL, 'Ilmu Terapan'),
(36, 'Ilmu atau Sains Kedokteran', 'Prodi Ilmu atau Sains Kedokteran membekali mahasiswa dengan pengetahuan anatomi, fisiologi, patologi, dan praktik klinis untuk memahami serta menangani kesehatan manusia. Mahasiswa belajar melalui kuliah, praktikum laboratorium, serta praktik klinik di rumah sakit pendidikan. Proses belajar menekankan keterampilan medis, etika, dan empati. Lulusan dapat berkarier sebagai dokter umum, dokter spesialis, peneliti medis, atau akademisi.', '\"[\\\"Biologi\\\",\\\"Kimia\\\"]\"', '[\"Biologi\",\"Kimia\"]', 'Dokter, Spesialis, Peneliti Medis, Dosen', 1, '2025-09-27 13:47:25.000000', '2025-09-29 05:49:32.000000', '[\"Biologi\",\"Kimia\"]', '[\"Biologi\",\"Kimia\"]', '[\"Biologi\",\"Kimia\"]', '[\"Biologi\",\"Kimia\"]', NULL, 'Ilmu Terapan'),
(37, 'Ilmu atau Sains Kedokteran Gigi', 'Prodi Ilmu atau Sains Kedokteran Gigi berfokus pada kesehatan mulut dan gigi, termasuk pencegahan, diagnosis, serta perawatan penyakit gigi. Mahasiswa belajar anatomi mulut, radiologi, prostodonti, hingga bedah mulut. Pembelajaran melibatkan praktik laboratorium, klinik, dan penelitian. Lulusan dapat berkarier sebagai dokter gigi, peneliti kesehatan gigi, atau pendidik kedokteran gigi.', '\"[\\\"Biologi\\\",\\\"Kimia\\\"]\"', '[\"Biologi\",\"Kimia\"]', NULL, 1, '2025-09-27 13:47:25.000000', '2025-09-29 05:49:32.000000', '[\"Biologi\",\"Kimia\"]', '[\"Biologi\",\"Kimia\"]', '[\"Biologi\",\"Kimia\"]', '[\"Biologi\",\"Kimia\"]', NULL, 'Ilmu Terapan'),
(38, 'Ilmu atau Sains Veteriner', 'Prodi Ilmu atau Sains Veteriner mempelajari kesehatan hewan, pencegahan penyakit, serta keamanan pangan asal hewan. Mahasiswa belajar anatomi, fisiologi hewan, patologi, farmakologi, dan bedah hewan. Proses pembelajaran mencakup praktik klinik hewan, laboratorium, dan riset kesehatan hewan. Lulusan dapat menjadi dokter hewan, peneliti veteriner, konsultan peternakan, atau pegawai lembaga kesehatan hewan.', '\"[\\\"Biologi\\\",\\\"Kimia\\\"]\"', '[\"Biologi\",\"Kimia\"]', NULL, 1, '2025-09-27 13:47:25.000000', '2025-09-29 05:49:32.000000', '[\"Biologi\",\"Kimia\"]', '[\"Biologi\",\"Kimia\"]', '[\"Biologi\",\"Kimia\"]', '[\"Biologi\",\"Kimia\"]', NULL, 'Ilmu Terapan'),
(39, 'Ilmu Farmasi', 'Prodi Ilmu Farmasi mempelajari obat-obatan, mulai dari penemuan, formulasi, produksi, hingga penggunaannya secara aman. Mahasiswa belajar kimia farmasi, farmakologi, farmakognosi, dan teknologi farmasi. Proses belajar mencakup penelitian di laboratorium, praktik di apotek, serta kerja sama dengan industri farmasi. Lulusan dapat berkarier sebagai apoteker, peneliti farmasi, konsultan regulasi obat, atau wirausahawan di bidang kesehatan.', '\"[\\\"Biologi\\\",\\\"Kimia\\\"]\"', '[\"Biologi\",\"Kimia\"]', 'Apoteker, Peneliti Farmasi, Quality Control, Dosen', 1, '2025-09-27 13:47:25.000000', '2025-09-29 05:49:32.000000', '[\"Biologi\",\"Kimia\"]', '[\"Biologi\",\"Kimia\"]', '[\"Biologi\",\"Kimia\"]', '[\"Biologi\",\"Kimia\"]', NULL, 'Ilmu Terapan'),
(40, 'Ilmu atau Sains Gizi', 'Prodi Ilmu atau Sains Gizi membekali mahasiswa dengan pemahaman nutrisi, dietetik, dan hubungan makanan dengan kesehatan manusia. Mahasiswa mempelajari biokimia gizi, nutrisi klinis, serta gizi masyarakat. Proses pembelajaran mencakup praktik laboratorium, penelitian lapangan, serta konseling gizi. Lulusan dapat berkarier sebagai ahli gizi klinis, konsultan nutrisi, peneliti gizi, atau praktisi kesehatan masyarakat.', '\"[\\\"Biologi\\\",\\\"Kimia\\\"]\"', '[\"Biologi\",\"Kimia\"]', NULL, 1, '2025-09-27 13:47:25.000000', '2025-09-29 05:49:32.000000', '[\"Biologi\",\"Kimia\"]', '[\"Biologi\",\"Kimia\"]', '[\"Biologi\",\"Kimia\"]', '[\"Biologi\",\"Kimia\"]', NULL, 'Ilmu Terapan'),
(41, 'Kesehatan Masyarakat', 'Prodi Kesehatan Masyarakat mempelajari strategi pencegahan penyakit, promosi kesehatan, serta kebijakan kesehatan masyarakat. Mahasiswa belajar epidemiologi, manajemen kesehatan, biostatistik, dan kesehatan lingkungan. Proses pembelajaran melibatkan penelitian lapangan, analisis data kesehatan, dan praktik kerja di institusi kesehatan. Lulusan dapat bekerja sebagai tenaga kesehatan masyarakat, peneliti, konsultan kesehatan, atau pejabat di lembaga kesehatan.', '\"[\\\"Biologi\\\"]\"', '[\"Biologi\",\"Sosiologi\"]', NULL, 1, '2025-09-27 13:47:25.000000', '2025-09-29 05:49:32.000000', '[\"Biologi\"]', '[\"Biologi\"]', '[\"Sosiologi\",\"Biologi\"]', '[\"Biologi\"]', NULL, 'Ilmu Terapan'),
(42, 'Kebidanan', 'Prodi Kebidanan membekali mahasiswa dengan ilmu dan keterampilan dalam pelayanan kesehatan ibu, anak, serta reproduksi. Mahasiswa mempelajari anatomi, fisiologi, kehamilan, persalinan, dan neonatus. Proses belajar mencakup praktik klinik, simulasi persalinan, dan penelitian di bidang kebidanan. Lulusan dapat berkarier sebagai bidan, konselor kesehatan reproduksi, atau praktisi di fasilitas kesehatan.', '\"[\\\"Biologi\\\"]\"', '[\"Biologi\",\"Kimia\"]', NULL, 1, '2025-09-27 13:47:25.000000', '2025-09-29 05:49:32.000000', '[\"Biologi\"]', '[\"Biologi\"]', '[\"Biologi\"]', '[\"Biologi\"]', NULL, 'Ilmu Terapan'),
(43, 'Keperawatan', 'Prodi Keperawatan mempelajari konsep perawatan kesehatan holistik untuk pasien di berbagai kondisi. Mahasiswa mempelajari ilmu dasar keperawatan, gawat darurat, keperawatan komunitas, dan manajemen pelayanan kesehatan. Proses pembelajaran melibatkan praktik klinik, simulasi perawatan, dan penelitian. Lulusan dapat bekerja sebagai perawat klinis, pendidik keperawatan, peneliti, atau manajer pelayanan kesehatan.', '\"[\\\"Biologi\\\"]\"', '[\"Biologi\",\"Kimia\"]', NULL, 1, '2025-09-27 13:47:25.000000', '2025-09-29 05:49:32.000000', '[\"Biologi\"]', '[\"Biologi\"]', '[\"Biologi\"]', '[\"Biologi\"]', NULL, 'Ilmu Terapan'),
(44, 'Kesehatan', 'Prodi Kesehatan mempelajari aspek umum kesehatan manusia, termasuk promosi kesehatan, pencegahan penyakit, dan layanan dasar kesehatan. Mahasiswa belajar dasar ilmu kesehatan, kebijakan kesehatan, serta teknologi pendukung layanan kesehatan. Proses belajar mencakup penelitian, praktik lapangan, dan kerja sama dengan institusi kesehatan. Lulusan dapat berkarier sebagai tenaga kesehatan, analis kebijakan kesehatan, atau pekerja di lembaga pelayanan kesehatan.', '\"[\\\"Biologi\\\"]\"', '[\"Kimia\",\"Matematika\"]', NULL, 1, '2025-09-27 13:47:25.000000', '2025-09-29 05:49:32.000000', '[\"Biologi\"]', '[\"Biologi\"]', '[\"Biologi\"]', '[\"Biologi\"]', NULL, 'Ilmu Terapan'),
(45, 'Ilmu atau Sains Informasi', 'Prodi Ilmu atau Sains Informasi berfokus pada pengelolaan, analisis, dan pemanfaatan informasi menggunakan teknologi. Mahasiswa belajar sistem informasi, basis data, analisis data, serta teknologi informasi terkini. Proses belajar berbasis proyek, penelitian, dan praktik di laboratorium komputer. Lulusan dapat menjadi analis sistem, data scientist, konsultan TI, atau pengembang solusi berbasis informasi.', '\"[\\\"Matematika Tingkat Lanjut\\\"]\"', '[\"Matematika Tingkat Lanjut\",\"Fisika\"]', NULL, 1, '2025-09-27 13:47:25.000000', '2025-09-29 05:49:32.000000', '[\"Matematika Tingkat Lanjut\"]', '[\"Matematika\"]', '[\"Matematika\"]', '[\"Matematika\"]', NULL, 'Ilmu Terapan'),
(46, 'Hukum', 'Prodi Hukum mempelajari teori dan praktik hukum, mulai dari hukum perdata, pidana, tata negara, hingga hukum internasional. Mahasiswa dilatih untuk berpikir kritis, menganalisis kasus, dan memahami prosedur hukum. Proses belajar mencakup simulasi peradilan, riset hukum, dan praktik kerja di lembaga hukum. Lulusan dapat berkarier sebagai advokat, jaksa, hakim, notaris, konsultan hukum, atau akademisi.', '\"[\\\"Sosiologi\\\",\\\"Pendidikan Pancasila\\\"]\"', '[\"Sosiologi\",\"PPKn\"]', NULL, 1, '2025-09-27 13:47:25.000000', '2025-09-29 05:49:32.000000', '[\"Sosiologi\",\"Pendidikan Pancasila\"]', '[\"PPKn\"]', '[\"PPKn\",\"Sosiologi\"]', '[\"PPKn\",\"Antropologi\"]', NULL, 'Ilmu Sosial'),
(47, 'Ilmu atau Sains Militer', 'Prodi Ilmu atau Sains Militer fokus pada strategi pertahanan, manajemen sumber daya militer, serta teknologi pertahanan. Mahasiswa mempelajari sejarah militer, geopolitik, kepemimpinan, dan taktik militer modern. Proses belajar mencakup simulasi strategi, riset pertahanan, serta kerja sama dengan institusi militer. Lulusan dapat berkarier sebagai perwira militer, peneliti pertahanan, atau konsultan strategi keamanan.', '\"[\\\"Sosiologi\\\"]\"', '[\"Sosiologi\",\"PPKn\"]', NULL, 1, '2025-09-27 13:47:25.000000', '2025-09-29 05:49:32.000000', '[\"Sosiologi\"]', '[\"PPKn\"]', '[\"Sosiologi\"]', '[\"Sosiologi\"]', NULL, 'Ilmu Sosial'),
(48, 'Urusan Publik', 'Prodi Urusan Publik mempelajari administrasi, kebijakan, dan tata kelola sektor publik. Mahasiswa belajar teori pemerintahan, kebijakan publik, manajemen birokrasi, serta pelayanan masyarakat. Proses pembelajaran mencakup studi kasus, simulasi kebijakan, dan kerja sama dengan lembaga pemerintahan. Lulusan dapat berkarier sebagai birokrat, analis kebijakan, konsultan publik, atau pengelola lembaga non-pemerintah.', '\"[\\\"Sosiologi\\\"]\"', '[\"Sosiologi\",\"PPKn\"]', NULL, 1, '2025-09-27 13:47:25.000000', '2025-09-29 05:49:32.000000', '[\"Sosiologi\"]', '[\"PPKn\"]', '[\"Sosiologi\"]', '[\"Sosiologi\"]', NULL, 'Ilmu Sosial'),
(49, 'Ilmu atau Sains Keolahragaan', 'Prodi Ilmu atau Sains Keolahragaan mempelajari teori dan praktik olahraga, fisiologi, serta manajemen kegiatan olahraga. Mahasiswa belajar ilmu gerak tubuh, gizi olahraga, dan kepelatihan. Proses belajar mencakup praktik olahraga, penelitian, dan pengelolaan event olahraga. Lulusan dapat bekerja sebagai pelatih, instruktur kebugaran, manajer olahraga, atau peneliti bidang keolahragaan.', '\"[\\\"PJOK\\\",\\\"Biologi\\\"]\"', '[\"PJOK\",\"Biologi\"]', NULL, 1, '2025-09-27 13:47:25.000000', '2025-09-29 05:49:32.000000', '[\"PJOK\",\"Biologi\"]', '[\"PJOK\",\"Biologi\"]', '[\"PJOK\"]', '[\"PJOK\"]', NULL, 'Ilmu Terapan'),
(50, 'Pariwisata', 'Prodi Pariwisata mempelajari manajemen destinasi, perhotelan, perjalanan, serta pemasaran pariwisata. Mahasiswa belajar teori pariwisata, budaya, dan strategi pelayanan. Proses belajar mencakup praktik lapangan, proyek pariwisata, serta kerja sama dengan industri perhotelan dan perjalanan. Lulusan dapat berkarier sebagai manajer pariwisata, konsultan destinasi, pemandu wisata, atau entrepreneur pariwisata.', '\"[\\\"Ekonomi\\\",\\\"Bahasa Inggris Tingkat Lanjut\\\"]\"', '[\"Ekonomi\",\"Bahasa Inggris\"]', NULL, 1, '2025-09-27 13:47:25.000000', '2025-09-29 05:49:32.000000', '[\"Ekonomi\",\"Bahasa Inggris Tingkat Lanjut\",\"Bahasa Asing Lainnya\"]', '[\"Bahasa Inggris\",\"Ekonomi\"]', '[\"Ekonomi\",\"Bahasa Inggris\"]', '[\"Bahasa Sastra Inggris\",\"Bahasa Asing Lainnya\"]', NULL, 'Ilmu Terapan'),
(51, 'Transportasi', 'Prodi Transportasi berfokus pada perencanaan, manajemen, dan teknologi transportasi darat, laut, maupun udara. Mahasiswa belajar rekayasa transportasi, logistik, kebijakan transportasi, serta teknologi kendaraan. Proses pembelajaran mencakup simulasi sistem transportasi, penelitian, dan kerja sama dengan industri transportasi. Lulusan dapat menjadi perencana transportasi, manajer operasional, atau konsultan transportasi.', '\"[\\\"Matematika Tingkat Lanjut\\\"]\"', '[\"Matematika Tingkat Lanjut\",\"Fisika\"]', NULL, 1, '2025-09-27 13:47:25.000000', '2025-09-29 05:49:32.000000', '[\"Matematika Tingkat Lanjut\"]', '[\"Matematika\"]', '[\"Matematika\"]', '[\"Matematika\"]', NULL, 'Ilmu Terapan'),
(52, 'Bioteknologi, Biokewirausahaan, Bioinformatika', 'Prodi Bioteknologi, Biokewirausahaan, atau Bioinformatika menggabungkan ilmu biologi dengan teknologi dan bisnis. Mahasiswa mempelajari teknik bioteknologi, analisis bioinformatika, serta strategi kewirausahaan berbasis sains. Proses belajar mencakup penelitian laboratorium, proyek inovasi, dan pengembangan produk berbasis biologi. Lulusan dapat berkarier sebagai peneliti, pengembang produk bioteknologi, entrepreneur, atau konsultan bioindustri.', '\"[\\\"Biologi\\\",\\\"Matematika\\\"]\"', '[\"Biologi\",\"Matematika\"]', NULL, 1, '2025-09-27 13:47:25.000000', '2025-09-29 05:49:32.000000', '[\"Biologi\",\"Matematika\"]', '[\"Biologi\",\"Matematika\"]', '[\"Biologi\",\"Matematika\"]', '[\"Biologi\",\"Matematika\"]', NULL, 'Ilmu Terapan'),
(53, 'Geografi, Geografi Lingkungan, Sains Informasi Geografi', 'Prodi Geografi, Lingkungan, atau Sains Informasi Geografi mempelajari fenomena bumi, penggunaan lahan, serta teknologi informasi geografis. Mahasiswa belajar pemetaan, sistem informasi geografis (SIG), serta analisis spasial. Proses pembelajaran mencakup penelitian lapangan, pemodelan komputer, dan praktik menggunakan perangkat SIG. Lulusan dapat menjadi ahli geografi, analis spasial, konsultan lingkungan, atau perencana wilayah.', '\"[\\\"Geografi\\\",\\\"Matematika\\\"]\"', '[\"Geografi\",\"Matematika\"]', NULL, 1, '2025-09-27 13:47:25.000000', '2025-09-29 05:49:32.000000', '[\"Geografi\",\"Matematika\"]', '[\"Fisika\",\"Matematika\"]', '[\"Geografi\",\"Matematika\"]', '[\"Matematika\"]', NULL, 'Ilmu Terapan'),
(54, 'Informatika Medis atau Informatika Kesehatan', 'Prodi Informatika Medis atau Kesehatan menggabungkan teknologi informasi dengan bidang kesehatan. Mahasiswa mempelajari sistem informasi kesehatan, rekam medis elektronik, analisis data kesehatan, serta teknologi pendukung pelayanan medis. Proses belajar mencakup proyek TI kesehatan, penelitian, dan praktik di fasilitas kesehatan. Lulusan dapat berkarier sebagai analis sistem kesehatan, konsultan TI medis, atau peneliti di bidang teknologi kesehatan.', '\"[\\\"Biologi\\\",\\\"Matematika Tingkat Lanjut\\\"]\"', '[\"Biologi\",\"Matematika Tingkat Lanjut\"]', NULL, 1, '2025-09-27 13:47:25.000000', '2025-09-29 05:49:32.000000', '[\"Biologi\",\"Matematika Tingkat Lanjut\"]', '[\"Biologi\",\"Matematika\"]', '[\"Biologi\",\"Matematika\"]', '[\"Biologi\",\"Matematika\"]', NULL, 'Ilmu Terapan'),
(55, 'Konservasi Biologi, Konservasi Hewan Liar, Konservasi Hewan Liar dan Hutan, Konservasi Hutan, Konservasi Sumber Daya Alam', 'Prodi Konservasi Biologi, Hewan Liar, Hutan, atau Sumber Daya Alam berfokus pada pelestarian keanekaragaman hayati dan sumber daya alam. Mahasiswa mempelajari ekologi konservasi, manajemen kawasan lindung, serta teknologi konservasi. Proses belajar mencakup penelitian lapangan, program konservasi, dan kerja sama dengan lembaga lingkungan. Lulusan dapat berkarier sebagai konservasionis, peneliti, atau konsultan lingkungan.', '\"[\\\"Biologi\\\"]\"', '[\"Biologi\",\"Geografi\"]', NULL, 1, '2025-09-27 13:47:25.000000', '2025-09-29 05:49:32.000000', '[\"Biologi\"]', '[\"Biologi\"]', '[\"Biologi\"]', '[\"Biologi\"]', NULL, 'Ilmu Terapan'),
(56, 'Teknologi Pangan, Teknologi Hasil Pertanian/Peternakan/Perikanan', 'Prodi Teknologi Pangan, Hasil Pertanian, Peternakan, atau Perikanan mempelajari pengolahan, keamanan, dan inovasi produk pangan. Mahasiswa mempelajari teknologi pengawetan, rekayasa pangan, dan manajemen mutu. Proses belajar mencakup praktikum laboratorium, proyek inovasi produk, serta kerja sama dengan industri pangan. Lulusan dapat bekerja sebagai teknolog pangan, peneliti, konsultan industri, atau entrepreneur di sektor pangan.', '\"[\\\"Kimia\\\",\\\"Biologi\\\"]\"', '[\"Kimia\",\"Biologi\"]', NULL, 1, '2025-09-27 13:47:25.000000', '2025-09-29 05:49:32.000000', '[\"Kimia\",\"Biologi\"]', '[\"Kimia\",\"Biologi\"]', '[\"Kimia\",\"Biologi\"]', '[\"Kimia\",\"Biologi\"]', NULL, 'Ilmu Terapan'),
(57, 'Sains Data', 'Prodi Sains Data mempelajari pengumpulan, pengolahan, analisis, dan interpretasi data dalam jumlah besar. Mahasiswa belajar statistik, machine learning, data mining, serta pemrograman. Proses pembelajaran berbasis proyek dengan data nyata, riset, dan aplikasi industri. Lulusan dapat menjadi data scientist, analis data, konsultan bisnis berbasis data, atau peneliti.', '\"[\\\"Matematika Tingkat Lanjut\\\"]\"', '[\"Matematika Tingkat Lanjut\",\"Komputer\"]', NULL, 1, '2025-09-27 13:47:25.000000', '2025-09-29 05:49:32.000000', '[\"Matematika Tingkat Lanjut\"]', '[\"Matematika\"]', '[\"Matematika\"]', '[\"Matematika\"]', NULL, 'Ilmu Terapan'),
(58, 'Sains Perkopian', 'Prodi Sains Perkopian mempelajari seluruh rantai nilai kopi, mulai dari budidaya, pengolahan pasca panen, hingga bisnis kopi. Mahasiswa belajar ilmu pertanian, teknologi pangan, manajemen bisnis, serta budaya kopi. Proses belajar mencakup praktik lapangan di kebun kopi, laboratorium, serta proyek kewirausahaan. Lulusan dapat berkarier sebagai ahli kopi, peneliti, pengusaha kopi, atau konsultan industri kopi.', '\"[\\\"Biologi\\\"]\"', '[\"Biologi\",\"Kimia\"]', NULL, 1, '2025-09-27 13:47:25.000000', '2025-09-29 05:49:32.000000', '[\"Biologi\"]', '[\"Biologi\"]', '[\"Biologi\"]', '[\"Biologi\"]', NULL, 'Ilmu Terapan'),
(59, 'Studi Humanitas', 'Prodi Studi Humanitas mempelajari manusia dari perspektif budaya, sosial, dan sejarah untuk memahami nilai, identitas, serta peran manusia dalam peradaban. Mahasiswa belajar antropologi, sosiologi, filsafat, dan kajian budaya. Proses belajar menekankan analisis kritis, penelitian, serta diskusi interdisipliner. Lulusan dapat berkarier sebagai peneliti humaniora, pendidik, konsultan budaya, atau pekerja di organisasi yang berfokus pada isu kemanusiaan.', '\"[\\\"Antropologi\\\",\\\"Sosiologi\\\"]\"', '[\"Antropologi\",\"Sosiologi\"]', NULL, 1, '2025-09-27 13:47:25.000000', '2025-09-29 05:49:32.000000', '[\"Antropologi\",\"Sosiologi\"]', '[\"Antropologi\",\"Sosiologi\"]', '[\"Antropologi\",\"Sosiologi\"]', '[\"Antropologi\",\"Sosiologi\"]', NULL, 'Humaniora'),
(62, 'teknik informatika', 'tes', '[]', '[{\"id\":11,\"name\":\"Antropologi\",\"code\":\"ANT\",\"subject_type\":\"Pilihan\"},{\"id\":13,\"name\":\"Bahasa Arab\",\"code\":\"BAR\",\"subject_type\":\"Pilihan\"}]', 'apa aja', 1, '2025-10-09 07:03:20.000000', '2025-10-09 07:03:20.000000', '[]', '[{\"id\":11,\"name\":\"Antropologi\",\"code\":\"ANT\",\"subject_type\":\"Pilihan\"}]', '[]', '[{\"id\":11,\"name\":\"Antropologi\",\"code\":\"ANT\",\"subject_type\":\"Pilihan\"}]', '[]', 'Ilmu Alam'),
(63, 'Test Checkbox Major', 'Test description for checkbox functionality', '[\"Test Subject\"]', '[\"Test Optional Subject\"]', NULL, 1, '2025-10-09 13:24:02.000000', '2025-10-09 13:24:02.000000', NULL, NULL, NULL, NULL, NULL, 'Test Category');

-- --------------------------------------------------------

--
-- Table structure for table `major_subject_mappings`
--

CREATE TABLE `major_subject_mappings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `major_id` bigint(20) UNSIGNED NOT NULL,
  `subject_id` bigint(20) UNSIGNED NOT NULL,
  `education_level` varchar(255) NOT NULL,
  `mapping_type` varchar(255) NOT NULL,
  `priority` int(11) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime(6) DEFAULT NULL,
  `updated_at` datetime(6) DEFAULT NULL,
  `subject_type` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2014_10_12_100000_create_password_reset_tokens_table', 1),
(3, '2019_08_19_000000_create_failed_jobs_table', 1),
(4, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(5, '2025_08_31_014748_create_schools_table', 2),
(6, '2025_08_31_015834_create_admins_table', 3),
(7, '2025_08_31_030000_create_students_table', 4),
(8, '2025_08_31_030100_create_questions_table', 5),
(9, '2025_08_31_030200_create_question_options_table', 6),
(10, '2025_01_01_000000_add_remember_token_to_admins_table', 7),
(11, '2025_01_01_000001_create_test_results_table', 8),
(12, '2025_01_01_000002_create_test_answers_table', 9),
(13, '2025_01_01_000003_add_status_to_students_table', 10),
(14, '2025_01_01_000004_create_subjects_table', 11),
(15, '2025_08_31_030500_create_results_table', 12),
(16, '2025_08_31_030501_create_recommendations_table', 13),
(17, '2025_01_01_000005_create_major_recommendations_table', 14),
(18, '2025_08_31_030300_create_student_subjects_table', 15),
(19, '2025_08_31_030400_create_answers_table', 15),
(20, '2025_09_01_100152_add_score_columns_to_major_recommendations_table', 16),
(21, '2025_09_01_101141_remove_score_columns_from_major_recommendations_table', 17),
(22, '2025_09_01_101246_add_curriculum_columns_to_major_recommendations_table', 18),
(23, '2025_09_01_120217_add_password_to_students_table', 19),
(24, '2025_09_01_130803_create_student_choices_table', 20),
(25, '2024_01_01_000000_add_performance_indexes', 21),
(26, '2025_09_04_062704_add_school_level_to_schools_table', 22),
(27, '2025_09_04_080914_create_tka_schedules_table', 23),
(28, '2025_09_05_145103_create_program_studi_table', 24),
(29, '2025_09_05_144431_create_program_studi_subjects_table', 25),
(30, '2025_09_12_025157_add_subject_type_to_major_subject_mappings_table', 26),
(31, '2025_09_14_000000_create_school_classes_table', 27),
(32, '2025_08_31_015835_add_remember_token_to_admins_table', 28),
(33, '2025_08_31_030101_add_status_to_students_table', 28),
(34, '2025_09_02_074655_add_password_to_schools_table', 28),
(35, '2025_09_02_114041_add_category_to_major_recommendations_table', 28),
(36, '2025_09_02_114244_add_parent_phone_to_students_table', 28),
(37, '2025_09_04_042443_add_education_level_to_subjects_table', 28),
(38, '2025_09_05_145040_create_rumpun_ilmu_table', 29),
(39, '2025_09_11_000000_create_major_subject_mappings_table', 30),
(40, '2025_12_31_000000_add_performance_indexes', 31),
(41, '2025_01_15_000000_fix_schools_password_field', 32),
(42, '2025_10_09_140216_fix_tka_schedules_table_structure', 33),
(43, '2025_10_17_160009_create_majors_table', 34),
(44, '2025_10_03_151600_add_name_column_to_admins_table', 35);

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` datetime(6) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` datetime(6) DEFAULT NULL,
  `expires_at` datetime(6) DEFAULT NULL,
  `created_at` datetime(6) DEFAULT NULL,
  `updated_at` datetime(6) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `personal_access_tokens`
--

INSERT INTO `personal_access_tokens` (`id`, `tokenable_type`, `tokenable_id`, `name`, `token`, `abilities`, `last_used_at`, `expires_at`, `created_at`, `updated_at`) VALUES
(1, 'App\\Models\\School', 6, 'test-token', 'f8859d165a51c343795585f3b0e336f8952efb7c031eefc00aa7ed83d0e92939', '[\"*\"]', NULL, NULL, '2025-10-17 15:14:30.000000', '2025-10-17 15:14:30.000000'),
(2, 'App\\Models\\School', 6, 'school-token', '4bec202efa60a1d110438480ba1e18aa5c9d0022056d9b3d24e05707da746d9a', '[\"*\"]', NULL, NULL, '2025-10-17 15:15:23.000000', '2025-10-17 15:15:23.000000'),
(3, 'App\\Models\\School', 6, 'school-token', 'e093f76b8733df27aaac82d01d4c2ae0081d9335226df0ce7d395b1a23b2029b', '[\"*\"]', '2025-10-17 22:14:51.000000', NULL, '2025-10-17 15:19:28.000000', '2025-10-17 22:14:51.000000'),
(4, 'App\\Models\\School', 6, 'school-token', '3716a45fa45db1e5680548fadb43b1ca6020578cda63baace13931d88bc98ec6', '[\"*\"]', '2025-10-17 15:53:58.000000', NULL, '2025-10-17 15:53:42.000000', '2025-10-17 15:53:58.000000'),
(5, 'App\\Models\\School', 6, 'school-token', '2babcdb62ba16767b605e7577bb5019a7d34f69711ff849e5f3f2f1e4485ab1e', '[\"*\"]', '2025-10-17 22:47:13.000000', NULL, '2025-10-17 22:40:00.000000', '2025-10-17 22:47:13.000000'),
(6, 'App\\Models\\School', 6, 'school-token', '82951f5714e5cd0dcb5617587de22aba4b744b51de2097d961d2b05669ad7798', '[\"*\"]', '2025-10-18 03:08:18.000000', NULL, '2025-10-18 02:36:58.000000', '2025-10-18 03:08:18.000000'),
(7, 'App\\Models\\School', 6, 'school-token', '9377e9fd9c2e4caf152b88aaab96e2d83bf6e0f7d6a3100a446ba91539b23380', '[\"*\"]', '2025-10-19 13:05:29.000000', NULL, '2025-10-18 11:55:44.000000', '2025-10-19 13:05:29.000000'),
(8, 'App\\Models\\School', 8, 'school-token', '8ba8253b2d753586dcaf240d44af39d8bc49a8fd3d313b703c200a7f9194966d', '[\"*\"]', '2025-10-18 12:39:27.000000', NULL, '2025-10-18 12:00:56.000000', '2025-10-18 12:39:27.000000'),
(9, 'App\\Models\\School', 6, 'test-token', '90d78961690edb36072ca171ebe6aaaa6106dad18b94ba039b05b011597c00d7', '[\"*\"]', '2025-10-19 13:17:48.000000', NULL, '2025-10-19 13:17:06.000000', '2025-10-19 13:17:48.000000'),
(10, 'App\\Models\\School', 20, 'school-token', '0b1721367673b822a91bb0ba3caf7743094e701985648eccb98aefde50f7b078', '[\"*\"]', NULL, NULL, '2025-10-21 06:34:45.000000', '2025-10-21 06:34:45.000000'),
(11, 'App\\Models\\School', 20, 'school-token', '215d7292feb75e52207f62fb1b039e60f174e3055458ee7e659606fbc5e51491', '[\"*\"]', NULL, NULL, '2025-10-21 06:37:29.000000', '2025-10-21 06:37:29.000000'),
(12, 'App\\Models\\School', 20, 'school-token', '2bc020aa5334526e28c277b2d4503262c1505cdd24bfb8e3a5253624f5e6be2c', '[\"*\"]', NULL, NULL, '2025-10-21 06:40:20.000000', '2025-10-21 06:40:20.000000'),
(13, 'App\\Models\\School', 20, 'school-token', '2f1719599b71bfcb355208deaf4f019746766bb6b1b963aa05b3609d8167689a', '[\"*\"]', NULL, NULL, '2025-10-21 07:00:17.000000', '2025-10-21 07:00:17.000000'),
(14, 'App\\Models\\School', 20, 'school-token', '92ea9cdf5a11bc2832b8314f48fdb9d54bcbd75f93dc45e218200eefc0103cb0', '[\"*\"]', NULL, NULL, '2025-10-21 07:04:52.000000', '2025-10-21 07:04:52.000000'),
(15, 'App\\Models\\School', 20, 'school-token', 'ee7c4c472ebdad3114d647d13b247d4906978e68dfe28c9ad7dec8b901565e2e', '[\"*\"]', NULL, NULL, '2025-10-21 07:06:13.000000', '2025-10-21 07:06:13.000000'),
(16, 'App\\Models\\School', 20, 'school-token', '5dd8d590f2320266d56af76ada5ed6fb3375501f91e3d9ddbd62e0d85478a246', '[\"*\"]', NULL, NULL, '2025-10-21 07:09:04.000000', '2025-10-21 07:09:04.000000'),
(17, 'App\\Models\\School', 21, 'school-token', '5bc2e735a5ef1ea4b679f4393fa4896fd01608c7ab18f1c5fcb18713ed48613e', '[\"*\"]', NULL, NULL, '2025-10-21 07:09:05.000000', '2025-10-21 07:09:05.000000'),
(18, 'App\\Models\\School', 23, 'school-token', '5d4e3899833fe029b78f2644c0fae86ab6506465ef14a4fe05d07e54a2842ec2', '[\"*\"]', NULL, NULL, '2025-10-21 07:09:06.000000', '2025-10-21 07:09:06.000000');

-- --------------------------------------------------------

--
-- Table structure for table `program_studi`
--

CREATE TABLE `program_studi` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `rumpun_ilmu_id` bigint(20) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime(6) DEFAULT NULL,
  `updated_at` datetime(6) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `program_studi`
--

INSERT INTO `program_studi` (`id`, `name`, `description`, `rumpun_ilmu_id`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Seni', NULL, 1, 1, '2025-09-27 13:31:19.000000', '2025-09-27 13:31:19.000000'),
(2, 'Sejarah', NULL, 1, 1, '2025-09-27 13:31:19.000000', '2025-09-27 13:31:19.000000'),
(3, 'Linguistik', NULL, 1, 1, '2025-09-27 13:31:19.000000', '2025-09-27 13:31:19.000000'),
(4, 'Sastra', NULL, 1, 1, '2025-09-27 13:25:24.000000', '2025-09-27 13:25:24.000000'),
(5, 'Filsafat', NULL, 1, 1, '2025-09-27 13:31:19.000000', '2025-09-27 13:31:19.000000'),
(6, 'Ekonomi', NULL, 2, 1, '2025-09-27 13:31:19.000000', '2025-09-27 13:31:19.000000'),
(7, 'Psikologi', NULL, 2, 1, '2025-09-27 13:31:19.000000', '2025-09-27 13:31:19.000000'),
(8, 'Sosiologi', NULL, 2, 1, '2025-09-27 13:25:24.000000', '2025-09-27 13:25:24.000000'),
(9, 'Kimia', NULL, 3, 1, '2025-09-27 13:31:19.000000', '2025-09-27 13:31:19.000000'),
(10, 'Fisika', NULL, 3, 1, '2025-09-27 13:31:19.000000', '2025-09-27 13:31:19.000000'),
(11, 'Biologi', NULL, 3, 1, '2025-09-27 13:31:19.000000', '2025-09-27 13:31:19.000000'),
(12, 'Matematika', NULL, 4, 1, '2025-09-27 13:31:19.000000', '2025-09-27 13:31:19.000000'),
(13, 'Komputer', NULL, 4, 1, '2025-09-27 13:31:19.000000', '2025-09-27 13:31:19.000000'),
(14, 'Kedokteran', NULL, 6, 1, '2025-09-27 13:25:24.000000', '2025-09-27 13:25:24.000000'),
(15, 'Farmasi', NULL, 6, 1, '2025-09-27 13:25:24.000000', '2025-09-27 13:25:24.000000'),
(16, 'Manajemen', NULL, 5, 1, '2025-09-27 13:25:24.000000', '2025-09-27 13:25:24.000000'),
(17, 'Teknik Sipil', NULL, 5, 1, '2025-09-27 13:25:24.000000', '2025-09-27 13:25:24.000000'),
(18, 'Susastra atau Sastra', NULL, 1, 1, '2025-09-27 13:31:19.000000', '2025-09-27 13:31:19.000000'),
(19, 'Sosial', NULL, 2, 1, '2025-09-27 13:31:19.000000', '2025-09-27 13:31:19.000000'),
(20, 'Pertahanan', NULL, 2, 1, '2025-09-27 13:31:19.000000', '2025-09-27 13:31:19.000000'),
(21, 'Ilmu atau Sains Kebumian', NULL, 3, 1, '2025-09-27 13:31:19.000000', '2025-09-27 13:31:19.000000'),
(22, 'Ilmu atau Sains Kelautan', NULL, 3, 1, '2025-09-27 13:31:19.000000', '2025-09-27 13:31:19.000000'),
(23, 'Biofisika', NULL, 3, 1, '2025-09-27 13:31:19.000000', '2025-09-27 13:31:19.000000'),
(24, 'Astronomi', NULL, 3, 1, '2025-09-27 13:31:19.000000', '2025-09-27 13:31:19.000000'),
(25, 'Logika', NULL, 4, 1, '2025-09-27 13:31:19.000000', '2025-09-27 13:31:19.000000'),
(26, 'Ilmu dan Sains Pertanian', NULL, 5, 1, '2025-09-27 13:31:19.000000', '2025-09-27 13:31:19.000000'),
(27, 'Peternakan', NULL, 5, 1, '2025-09-27 13:31:19.000000', '2025-09-27 13:31:19.000000'),
(28, 'Ilmu atau Sains Perikanan', NULL, 5, 1, '2025-09-27 13:31:19.000000', '2025-09-27 13:31:19.000000'),
(29, 'Arsitektur', NULL, 5, 1, '2025-09-27 13:31:19.000000', '2025-09-27 13:31:19.000000'),
(30, 'Perencanaan Wilayah', NULL, 5, 1, '2025-09-27 13:31:19.000000', '2025-09-27 13:31:19.000000'),
(31, 'Desain', NULL, 5, 1, '2025-09-27 13:31:19.000000', '2025-09-27 13:31:19.000000'),
(32, 'Ilmu atau Sains Akuntansi', NULL, 5, 1, '2025-09-27 13:31:19.000000', '2025-09-27 13:31:19.000000'),
(33, 'Ilmu atau Sains Manajemen', NULL, 5, 1, '2025-09-27 13:31:19.000000', '2025-09-27 13:31:19.000000'),
(34, 'Logistik', NULL, 5, 1, '2025-09-27 13:31:19.000000', '2025-09-27 13:31:19.000000'),
(35, 'Administrasi Bisnis', NULL, 5, 1, '2025-09-27 13:31:19.000000', '2025-09-27 13:31:19.000000'),
(36, 'Bisnis', NULL, 5, 1, '2025-09-27 13:31:19.000000', '2025-09-27 13:31:19.000000'),
(37, 'Ilmu atau Sains Komunikasi', NULL, 5, 1, '2025-09-27 13:31:19.000000', '2025-09-27 13:31:19.000000'),
(38, 'Pendidikan', NULL, 5, 1, '2025-09-27 13:31:19.000000', '2025-09-27 13:31:19.000000'),
(39, 'Teknik rekayasa', NULL, 5, 1, '2025-09-27 13:31:19.000000', '2025-09-27 13:31:19.000000'),
(40, 'Ilmu atau Sains Lingkungan', NULL, 7, 1, '2025-09-27 13:31:19.000000', '2025-09-27 13:31:19.000000'),
(41, 'Kehutanan', NULL, 7, 1, '2025-09-27 13:31:19.000000', '2025-09-27 13:31:19.000000'),
(42, 'Ilmu atau Sains Kedokteran', NULL, 6, 1, '2025-09-27 13:31:19.000000', '2025-09-27 13:31:19.000000'),
(43, 'Ilmu atau Sains Kedokteran Gigi', NULL, 6, 1, '2025-09-27 13:31:19.000000', '2025-09-27 13:31:19.000000'),
(44, 'Ilmu atau Sains Veteriner', NULL, 6, 1, '2025-09-27 13:31:19.000000', '2025-09-27 13:31:19.000000'),
(45, 'Ilmu Farmasi', NULL, 6, 1, '2025-09-27 13:31:19.000000', '2025-09-27 13:31:19.000000'),
(46, 'Ilmu atau Sains Gizi', NULL, 6, 1, '2025-09-27 13:31:19.000000', '2025-09-27 13:31:19.000000'),
(47, 'Kesehatan Masyarakat', NULL, 6, 1, '2025-09-27 13:31:19.000000', '2025-09-27 13:31:19.000000'),
(48, 'Kebidanan', NULL, 6, 1, '2025-09-27 13:31:19.000000', '2025-09-27 13:31:19.000000'),
(49, 'Keperawatan', NULL, 6, 1, '2025-09-27 13:31:19.000000', '2025-09-27 13:31:19.000000'),
(50, 'Kesehatan', NULL, 6, 1, '2025-09-27 13:31:19.000000', '2025-09-27 13:31:19.000000'),
(51, 'Ilmu atau Sains Informasi', NULL, 8, 1, '2025-09-27 13:31:19.000000', '2025-09-27 13:31:19.000000'),
(52, 'Hukum', NULL, 2, 1, '2025-09-27 13:31:19.000000', '2025-09-27 13:31:19.000000'),
(53, 'Ilmu atau Sains Militer', NULL, 2, 1, '2025-09-27 13:31:19.000000', '2025-09-27 13:31:19.000000'),
(54, 'Urusan Publik', NULL, 2, 1, '2025-09-27 13:31:19.000000', '2025-09-27 13:31:19.000000'),
(55, 'Ilmu atau Sains Keolahragaan', NULL, 6, 1, '2025-09-27 13:31:19.000000', '2025-09-27 13:31:19.000000'),
(56, 'Pariwisata', NULL, 7, 1, '2025-09-27 13:31:19.000000', '2025-09-27 13:31:19.000000'),
(57, 'Transportasi', NULL, 8, 1, '2025-09-27 13:31:19.000000', '2025-09-27 13:31:19.000000'),
(58, 'Bioteknologi, Biokewirausahaan, Bioinformatika', NULL, 7, 1, '2025-09-27 13:31:19.000000', '2025-09-27 13:31:19.000000'),
(59, 'Geografi, Geografi Lingkungan, Sains Informasi Geografi', NULL, 7, 1, '2025-09-27 13:31:19.000000', '2025-09-27 13:31:19.000000'),
(60, 'Informatika Medis atau Informatika Kesehatan', NULL, 8, 1, '2025-09-27 13:31:19.000000', '2025-09-27 13:31:19.000000'),
(61, 'Konservasi Biologi, Konservasi Hewan Liar, Konservasi Hewan Liar dan Hutan, Konservasi Hutan, Konservasi Sumber Daya Alam', NULL, 7, 1, '2025-09-27 13:31:19.000000', '2025-09-27 13:31:19.000000'),
(62, 'Teknologi Pangan, Teknologi Hasil Pertanian/Peternakan/Perikanan', NULL, 7, 1, '2025-09-27 13:31:19.000000', '2025-09-27 13:31:19.000000'),
(63, 'Sains Data', NULL, 8, 1, '2025-09-27 13:31:19.000000', '2025-09-27 13:31:19.000000'),
(64, 'Sains Perkopian', NULL, 7, 1, '2025-09-27 13:31:19.000000', '2025-09-27 13:31:19.000000'),
(65, 'Studi Humanitas', NULL, 1, 1, '2025-09-27 13:31:19.000000', '2025-09-27 13:31:19.000000');

-- --------------------------------------------------------

--
-- Table structure for table `program_studi_subjects`
--

CREATE TABLE `program_studi_subjects` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `program_studi_id` bigint(20) UNSIGNED NOT NULL,
  `subject_id` bigint(20) UNSIGNED NOT NULL,
  `kurikulum_type` varchar(255) NOT NULL,
  `is_required` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime(6) DEFAULT NULL,
  `updated_at` datetime(6) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `questions`
--

CREATE TABLE `questions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `subject` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  `content` varchar(0) NOT NULL,
  `media_url` varchar(255) DEFAULT NULL,
  `created_at` datetime(6) DEFAULT NULL,
  `updated_at` datetime(6) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `question_options`
--

CREATE TABLE `question_options` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `question_id` bigint(20) UNSIGNED NOT NULL,
  `option_text` varchar(0) NOT NULL,
  `is_correct` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime(6) DEFAULT NULL,
  `updated_at` datetime(6) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `recommendations`
--

CREATE TABLE `recommendations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `student_id` bigint(20) UNSIGNED NOT NULL,
  `major` varchar(255) NOT NULL,
  `description` varchar(0) DEFAULT NULL,
  `confidence_score` decimal(5,2) NOT NULL DEFAULT 0.00,
  `created_at` datetime(6) DEFAULT NULL,
  `updated_at` datetime(6) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `results`
--

CREATE TABLE `results` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `student_id` bigint(20) UNSIGNED NOT NULL,
  `subject` varchar(255) NOT NULL,
  `score` decimal(5,2) NOT NULL,
  `created_at` datetime(6) DEFAULT NULL,
  `updated_at` datetime(6) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `rumpun_ilmu`
--

CREATE TABLE `rumpun_ilmu` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` varchar(0) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime(6) DEFAULT NULL,
  `updated_at` datetime(6) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `rumpun_ilmu`
--

INSERT INTO `rumpun_ilmu` (`id`, `name`, `description`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Humaniora', NULL, 1, '2025-09-27 13:31:19.000000', '2025-09-27 13:31:19.000000'),
(2, 'Ilmu Sosial', NULL, 1, '2025-09-27 13:31:19.000000', '2025-09-27 13:31:19.000000'),
(3, 'Ilmu Alam', NULL, 1, '2025-09-27 13:31:19.000000', '2025-09-27 13:31:19.000000'),
(4, 'Ilmu Formal', NULL, 1, '2025-09-27 13:31:19.000000', '2025-09-27 13:31:19.000000'),
(5, 'Ilmu Terapan', NULL, 1, '2025-09-27 13:31:19.000000', '2025-09-27 13:31:19.000000'),
(6, 'Ilmu Kesehatan', NULL, 1, '2025-09-27 13:31:19.000000', '2025-09-27 13:31:19.000000'),
(7, 'Ilmu Lingkungan', NULL, 1, '2025-09-27 13:31:19.000000', '2025-09-27 13:31:19.000000'),
(8, 'Ilmu Teknologi', NULL, 1, '2025-09-27 13:31:19.000000', '2025-09-27 13:31:19.000000');

-- --------------------------------------------------------

--
-- Table structure for table `schools`
--

CREATE TABLE `schools` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `npsn` varchar(8) NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` datetime(6) DEFAULT NULL,
  `updated_at` datetime(6) DEFAULT NULL,
  `school_level` varchar(255) NOT NULL DEFAULT 'SMA/MA',
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `schools`
--

INSERT INTO `schools` (`id`, `npsn`, `name`, `created_at`, `updated_at`, `school_level`, `password`) VALUES
(6, '11223345', 'SMA Negeri 3 Surabaya', '2025-09-01 07:19:29.997000', '2025-10-21 06:32:23.000000', 'SMA/MA', '$2y$12$GUO0OzSoVyF/FikUM3SHnOhp/7O72eZjkP4CWoQj1cU6hTPB0fvC2'),
(7, '99999999', 'SMA Test School', '2025-09-02 09:42:18.657000', '2025-09-02 09:42:18.657000', 'SMA/MA', '$2y$12$4MwOvqaNZEEoSRiIQkyA.eSUUi6hpyhZcWtoE2jIDDr828WqNeftW'),
(8, '44556677', 'SMK Negeri 1 Karawang', '2025-09-03 03:31:14.070000', '2025-10-03 06:54:18.000000', 'SMA/MA', '44556677'),
(9, '98765432', 'SMA Negeri 1 Cihuy', '2025-09-09 00:00:45.933000', '2025-09-09 00:00:45.933000', 'SMA/MA', '$2y$12$fCEM8Oo9IDz01Lvw9VOY.eDrJKTpQEhNoyBEE8h40Bw1PFuN0gER.'),
(10, '48936746', 'SMA Negeri 1 Solo', '2025-09-09 00:03:26.447000', '2025-09-09 00:03:26.447000', 'SMA/MA', '$2y$12$.M7qI4qP3ZHwoq0vEJ9ZH.Qk1aBmEZns29nZV/KM4gVfDPd5zpX2i'),
(11, '26374859', 'SMA Negeri 2 Purwakarta', '2025-09-09 00:03:26.670000', '2025-09-09 00:03:26.670000', 'SMA/MA', '$2y$12$NsO1mYXa/DOR70HcUGWNxunSaUMzLcNZE7zJeVudPbUcL2pPYE9NS'),
(12, '29485763', 'SMA Negeri 2 Klari', '2025-09-09 00:03:26.907000', '2025-09-09 00:03:26.907000', 'SMA/MA', '$2y$12$Ay/dw0VcDuyTuTezkpK.jOl.OcMZmuGXOE.YLK/5Sz3JwLjlnN8ee'),
(13, '73849372', 'SMA Negeri 1 Yogyakarta', '2025-09-09 03:37:39.630000', '2025-10-12 13:38:50.000000', 'SMA/MA', '$2y$12$fFi9Gy8IlkopZLLtDZqSoOWSDGqFYnW0TtRObdhesV/T1VtgXbgPG'),
(19, '180905', 'SMK PEAK', '2025-10-13 06:02:44.000000', '2025-10-13 06:02:44.000000', 'SMA/MA', '$2y$12$HU2SvRcSptwmiX44roJdQufFEigAAEDCpX95MUfuKZvQxg.lSnZfm'),
(20, '12345678', 'SMA Negeri 1 Jakarta', '2025-10-21 06:34:37.000000', '2025-10-21 06:34:37.000000', 'SMA/MA', '$2y$12$wCun7lopMUD72bjuUNQGGO0iHrO8PlbevmJQZwohQ5SWbew7aMClm'),
(21, '87654321', 'SMA Negeri 2 Bandung', '2025-10-21 06:34:37.000000', '2025-10-21 06:34:37.000000', 'SMA/MA', '$2y$12$SU8JlXuXLm113Mw4uKDsZ.bKpykr4Eur5XZoFu53uQeJsnoONnOpK'),
(22, '11223344', 'SMA Negeri 3 Surabaya', '2025-10-21 06:34:37.000000', '2025-10-21 06:34:37.000000', 'SMA/MA', '$2y$12$DEKtgn51PLHvJ9ddNgCCx.UO9Y1tvSMxachAGdpUefBoW0K28IbeK'),
(23, '11111111', 'SMA Negeri 1 Medan', '2025-10-21 07:06:28.000000', '2025-10-21 07:06:28.000000', 'SMA/MA', '$2y$12$ZreoWHe1X01ub4qZ5JGHhuGPTm6hz9TbZ8t1/jW9jH7HfZNclXctK'),
(24, '22222222', 'SMA Negeri 1 Palembang', '2025-10-21 07:06:29.000000', '2025-10-21 07:06:29.000000', 'SMA/MA', '$2y$12$RFJ.XTqkPiTL5NgSIWR6F.ByfccTxrynkFeOZF7vb/XB9dlNGNsSu'),
(25, '33333333', 'SMA Negeri 1 Makassar', '2025-10-21 07:06:30.000000', '2025-10-21 07:06:30.000000', 'SMA/MA', '$2y$12$viHPSXbKX.HTLNtkNTIo9eS2d5WWXsytmR0l2oOO9ZV0iZxKfhBRG');

-- --------------------------------------------------------

--
-- Table structure for table `school_classes`
--

CREATE TABLE `school_classes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `school_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `level` varchar(255) NOT NULL,
  `program` varchar(255) NOT NULL,
  `class_number` int(11) NOT NULL,
  `description` varchar(0) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime(6) DEFAULT NULL,
  `updated_at` datetime(6) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nisn` varchar(10) NOT NULL,
  `name` varchar(255) NOT NULL,
  `school_id` bigint(20) UNSIGNED NOT NULL,
  `kelas` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `created_at` datetime(6) DEFAULT NULL,
  `updated_at` datetime(6) DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'registered',
  `password` varchar(255) DEFAULT NULL,
  `parent_phone` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`id`, `nisn`, `name`, `school_id`, `kelas`, `email`, `phone`, `created_at`, `updated_at`, `status`, `password`, `parent_phone`) VALUES
(13, '0987654321', 'Testing 2', 4, 'XII TKRO 2', 'tes2@gmail.com', '098765432111', '2025-09-03 06:11:49.477000', '2025-09-29 04:14:45.000000', 'active', '$2y$12$e3KFgyi2emKjYKB0tin1su2pVEzFAQDf2mBbm0Kc.HXYeWgxczVUm', '098765432111'),
(14, '2619760401', 'Testing 3', 8, 'XII TKJ 1', 'dafit123@gmail.com', '0812131311323', '2025-09-04 02:10:16.087000', '2025-09-29 04:14:45.000000', 'active', '$2y$12$/SV7Xx6aJo7uYDBLZYs8P.BUl9Fz2xzAs7NnnyDrEvhNJ7lEaoECC', '08219831230'),
(15, '1234567890', 'Test Student Add', 4, 'X TKJ 1', 'test@example.com', '081234567890', '2025-09-06 02:11:10.103000', '2025-09-29 04:14:45.000000', 'active', '$2y$12$7IGqGC3U/jUA63Qpf4rTXuhg.0IGrb3alSsccV0Zj1fQddFW1rvTG', '081234567891'),
(16, '1111111111', 'Test Student 1', 4, 'X TKJ 1', 'test1@example.com', '081234567890', '2025-09-06 02:12:10.300000', '2025-09-29 04:14:45.000000', 'active', '$2y$12$ZphoclB/Ah0F9SXk6ZBCSeKUjNIpRX/XYMNlzHUaflywAl7P0JTAO', '081234567891'),
(17, '6666666666', 'Curl Test Student', 4, 'X TKJ 1', 'curltest@example.com', '081234567890', '2025-09-06 02:14:47.667000', '2025-09-29 04:14:46.000000', 'active', '$2y$12$I//vTkUpyDpdrx9xAQ7H6.vG4L.5Whw9dnrJdGtGPYKy2v5z5sV4O', '081234567891'),
(18, '2233445566', 'Testing web', 4, 'XII TKJ 2', 'web@gmail.com', '0812311231', '2025-09-06 02:20:15.280000', '2025-09-29 04:14:46.000000', 'active', '$2y$12$ORqOmpzz.z6aOt4gZBYYRu.x0lTf1XTLnwpa7GMrE.cDdxEgEdD1q', '0831231233'),
(19, '2233445577', 'Testing cui', 8, 'XII TKJ 2', 'cui@gmail.com', '0812388131321', '2025-09-06 02:22:21.713000', '2025-09-29 04:14:46.000000', 'active', '$2y$12$ThJg72MDEECnoKTLaVWvKOgddszJzBUPjz9uQYI3xKSWFvIG6XZ6O', '08123817313'),
(20, '5555555555', 'Faisal', 12, 'XI TKJ 3', 'sal123@gmail.com', '0812713834', '2025-09-09 03:39:33.800000', '2025-09-29 04:14:46.000000', 'active', '$2y$12$amt.K4Z795bNec6PJfcz4ezCMEFDsYSGpYNvSDc6PDWeiPA26jrAy', '08139371341'),
(21, '8130130910', 'Raihan  Yaskur', 8, 'XII TKRO 2', 'han123@email.com', '098013013310', '2025-09-11 06:34:15.143000', '2025-09-29 05:21:06.000000', 'active', '$2y$12$EKtqiioXZyASYNo6MuuMAOfMLD4eJLOGaPd03R96JkoBQJZ4hb2WW', '081283103113'),
(22, '8209120392', 'Testing', 4, 'XI TKJ 3', 'hijauloka@gmail.com', '0812131311323', '2025-09-11 06:57:44.410000', '2025-09-29 04:14:47.000000', 'active', '$2y$12$EX/io0pZCys2IdUWVjtNf.MccWHqi8iQw8uLQVLV9h7wWJc3bi25q', '0813711133113'),
(23, '7839120122', 'Perdi Pratama', 8, 'XII TKRO 2', 'dafit123@gmail.com', '081123123134', '2025-09-11 06:59:43.800000', '2025-09-29 04:14:47.000000', 'active', '$2y$12$UGtkhWv/bhD0CDrbluRS9uMb8xZYTkiLQaAu7NHfBLJh7FGkXOoAi', '0831231233'),
(25, '2222222222', 'Test Student 2', 4, 'XII TKJ 3', 'test2@example.com', '081234567891', '2025-09-13 02:21:22.520000', '2025-09-29 04:14:47.000000', 'active', '$2y$12$YoO4NHmO1DOcp.ET3G2kfuX5jsAcucbG/GzyzaDkPYLstMccM5BBK', '081234567892'),
(26, '3333333333', 'Test Student 3', 4, 'XII TKRO 3', 'test3@example.com', '081234567892', '2025-09-13 02:21:22.803000', '2025-09-29 04:14:48.000000', 'active', '$2y$12$2hN1EzlftsG/iSggcJi9f.GOdYmWFre5DDLvCLDUCBj/OYYDxzkDy', '081234567893'),
(27, '1314141414', 'teesssss', 8, 'XII TKJ 2', 'admin456@gmail.com', '081380630988', '2025-09-13 18:33:49.387000', '2025-09-29 04:14:48.000000', 'active', '$2y$12$GyXgmLxEgMDGj9B10PUm7OTme9S.rk.hCTqwLayfu90..5WEQUG9O', '081380630988'),
(29, '5432167890', 'Raihan yasykur', 8, 'XII TKJ 2', 'da@dad', '08218821821821', '2025-09-16 03:27:07.593000', '2025-09-29 05:20:48.000000', 'active', '$2y$12$VfOBWpFScNVVkJvMbBIRseVXFxS8Vxuv3.0wwrjwr2oPoCJukGorS', NULL),
(30, '1298567892', 'Siti Nurhaliza', 8, 'XII TMI 2', 'siti.nurhaliza@example.com', '081234567892', '2025-09-23 03:52:48.143000', '2025-10-18 12:29:46.000000', 'active', '$2y$12$WReRari2yHriLndF9dZLdO0URrXuSmJYuO9xTUoBoepANQ6AKa0ue', '081234567893'),
(31, '1204567891', 'Siti Nurhaliza', 8, 'XII TKJ 1', 'siti.nurhaliza@example.com', '081234567892', '2025-09-23 03:52:48.377000', '2025-09-29 04:14:48.000000', 'active', '$2y$12$YBQgcDNXLsazKcTc6376je8XYlMzGqYjf.O.Xmg/hk5onTNCV.4U6', '081234567890'),
(32, '1381238102', 'dandajdnasd', 8, 'XII TKJ 1', 'adad@ga.co', '083113713331', '2025-09-23 04:04:25.753000', '2025-09-29 04:14:49.000000', 'active', '$2y$12$g3.DTxAMszr4Urm5zn6DluH.oUXR4Lwx6gqx/gkoWXo3WOAG9DQGm', '083183831133'),
(35, '1534567890', 'Ahmad Rizki Pratama', 8, 'XII TMI 2', 'ahmad.rizki@example.com', NULL, '2025-09-30 03:18:37.000000', '2025-09-30 03:18:37.000000', 'active', '$2y$12$mkWMTJ1YxgkxdfU8Hdavhepo0IjqcNUhpTp9q9Im3RFkvsxrGAPWW', NULL),
(36, '1298567891', 'Siti Nurhaliza', 8, 'XII TMI 2', 'siti.nurhaliza@example.com', NULL, '2025-09-30 03:18:37.000000', '2025-09-30 03:18:37.000000', 'active', '$2y$12$QONvv2K2UkgT2cKkCq.dGOB6XBIUWN6NbYw5BzFVrbpdbpk9q0UNy', NULL),
(37, '1234563292', 'Budi Santoso', 8, 'XII TMI 2', 'budi.santoso@example.com', NULL, '2025-09-30 03:18:37.000000', '2025-09-30 03:18:37.000000', 'active', '$2y$12$Iu3ej2gsEnnDYBMqKw.1vuxv2SLCrCEN/vQnD2Vb9MU2AXYA31Iea', NULL),
(38, '1230967892', 'Dewi Kartika', 8, 'XII TMI 2', 'dewi.kartika@example.com', NULL, '2025-09-30 03:18:38.000000', '2025-09-30 03:18:38.000000', 'active', '$2y$12$0abHV8mNcyV0coS50YIY3OxcHOhIkl8cMXv4jkSQbJNrgYvcb0g42', NULL),
(39, '1234497121', 'Defta', 8, 'XII TKJ 2', 'de@de.co', '082918398310', '2025-09-30 03:23:19.000000', '2025-09-30 03:23:19.000000', 'active', '$2y$12$SYuM9s1duGCec4m0/3TMlObjxWLND6pxIjT.5JqMv//ABjlhvTe0y', '081973179913'),
(41, '9876543210', 'Ahmad Rizki', 6, 'XII IPA 1', 'ahmad.rizki@example.com', '081234567890', '2025-10-11 11:57:30.000000', '2025-10-11 11:57:30.000000', 'active', '$2y$12$WzNT0A9uUOxE8YGGat.Exe6Cl0ibqDprGMpGC9m//o/sIS5Btm76a', '081234567891'),
(42, '9876543211', 'Siti Nurhaliza', 6, 'XII IPA 2', 'siti.nurhaliza@example.com', '081234567892', '2025-10-11 11:57:30.000000', '2025-10-11 11:57:30.000000', 'active', '$2y$12$IR/DeIAKz5/G5WtQidMJM.0JnsoNTywLDpz3mWFLsjAEKVoVLA0hm', '081234567893'),
(43, '9876543212', 'Budi Santoso', 6, 'XII IPS 1', 'budi.santoso@example.com', '081234567894', '2025-10-11 11:57:30.000000', '2025-10-11 11:57:30.000000', 'active', '$2y$12$SZf5iMcXy3qXtUA2tYjFVu1wFVVETlYMybfIj3qYtfm2jxPiNVPZG', '081234567895'),
(46, '9999999999', 'Test Student New', 6, 'XII-3', 'testnew@example.com', '08123456789', '2025-10-19 13:17:37.000000', '2025-10-19 13:17:37.000000', 'active', '$2y$12$xSNjFB0pSvVvlBQ2fF77N.XtFYo4Hck9WFFlD9HhHu2ZGhoKBNV/6', '08123456788');

-- --------------------------------------------------------

--
-- Table structure for table `student_choices`
--

CREATE TABLE `student_choices` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `student_id` bigint(20) UNSIGNED NOT NULL,
  `major_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` datetime(6) DEFAULT NULL,
  `updated_at` datetime(6) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `student_choices`
--

INSERT INTO `student_choices` (`id`, `student_id`, `major_id`, `created_at`, `updated_at`) VALUES
(12, 13, 40, '2025-09-06 03:49:25.573000', '2025-09-16 03:38:30.937000'),
(13, 14, 34, '2025-09-06 03:49:25.573000', '2025-09-06 03:49:25.573000'),
(14, 15, 31, '2025-09-06 03:49:25.573000', '2025-09-06 03:49:25.573000'),
(15, 16, 22, '2025-09-06 03:49:25.573000', '2025-09-06 03:49:25.573000'),
(16, 17, 29, '2025-09-06 03:49:25.573000', '2025-09-06 03:49:25.573000'),
(17, 19, 28, '2025-09-07 03:17:18.307000', '2025-09-07 03:17:18.307000'),
(18, 20, 22, '2025-09-09 03:40:10.400000', '2025-09-09 09:35:08.907000'),
(20, 24, 52, '2025-09-11 10:12:57.087000', '2025-09-11 10:12:57.087000'),
(22, 29, 26, '2025-09-16 03:30:01.493000', '2025-09-16 03:30:01.493000'),
(23, 30, 29, '2025-09-23 03:55:06.847000', '2025-09-23 03:55:06.847000'),
(24, 32, 29, '2025-09-23 04:04:48.317000', '2025-09-23 04:04:48.317000'),
(26, 39, 16, '2025-09-30 03:24:31.000000', '2025-09-30 03:24:31.000000');

-- --------------------------------------------------------

--
-- Table structure for table `student_subjects`
--

CREATE TABLE `student_subjects` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `student_id` bigint(20) UNSIGNED NOT NULL,
  `subject_name` varchar(255) NOT NULL,
  `created_at` datetime(6) DEFAULT NULL,
  `updated_at` datetime(6) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `subjects`
--

CREATE TABLE `subjects` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `code` varchar(255) NOT NULL,
  `description` varchar(0) DEFAULT NULL,
  `is_required` tinyint(1) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime(6) DEFAULT NULL,
  `updated_at` datetime(6) DEFAULT NULL,
  `education_level` varchar(10) DEFAULT 'Umum',
  `subject_type` varchar(50) DEFAULT 'Pilihan',
  `subject_number` int(11) DEFAULT NULL,
  `type` varchar(255) NOT NULL DEFAULT 'pilihan'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `subjects`
--

INSERT INTO `subjects` (`id`, `name`, `code`, `description`, `is_required`, `is_active`, `created_at`, `updated_at`, `education_level`, `subject_type`, `subject_number`, `type`) VALUES
(1, 'Bahasa Indonesia', 'BIN', NULL, 1, 1, '2025-09-27 13:31:19.000000', '2025-09-27 13:31:19.000000', 'Umum', 'Wajib', 0, 'pilihan'),
(2, 'Matematika', 'MTK', NULL, 1, 1, '2025-09-27 13:31:19.000000', '2025-09-27 13:31:19.000000', 'Umum', 'Wajib', 0, 'pilihan'),
(3, 'Bahasa Inggris', 'BIG', NULL, 1, 1, '2025-09-27 13:31:19.000000', '2025-09-27 13:31:19.000000', 'Umum', 'Wajib', 0, 'pilihan'),
(4, 'Fisika', 'FIS', NULL, 0, 1, '2025-09-27 13:31:19.000000', '2025-09-27 13:31:19.000000', 'SMA/MA', 'Pilihan', 4, 'pilihan'),
(5, 'Kimia', 'KIM', NULL, 0, 1, '2025-09-27 13:31:19.000000', '2025-09-27 13:31:19.000000', 'SMA/MA', 'Pilihan', 5, 'pilihan'),
(6, 'Biologi', 'BIO', NULL, 0, 1, '2025-09-27 13:31:19.000000', '2025-09-27 13:31:19.000000', 'SMA/MA', 'Pilihan', 6, 'pilihan'),
(7, 'Ekonomi', 'EKO', NULL, 0, 1, '2025-09-27 13:31:19.000000', '2025-09-27 13:31:19.000000', 'SMA/MA', 'Pilihan', 7, 'pilihan'),
(8, 'Sosiologi', 'SOS', NULL, 0, 1, '2025-09-27 13:31:19.000000', '2025-09-27 13:31:19.000000', 'SMA/MA', 'Pilihan', 8, 'pilihan'),
(9, 'Geografi', 'GEO', NULL, 0, 1, '2025-09-27 13:31:19.000000', '2025-09-27 13:31:19.000000', 'SMA/MA', 'Pilihan', 9, 'pilihan'),
(10, 'Sejarah', 'SEJ', NULL, 0, 1, '2025-09-27 13:31:19.000000', '2025-09-27 13:31:19.000000', 'SMA/MA', 'Pilihan', 10, 'pilihan'),
(11, 'Antropologi', 'ANT', NULL, 0, 1, '2025-09-27 13:31:19.000000', '2025-09-27 13:31:19.000000', 'SMA/MA', 'Pilihan', 11, 'pilihan'),
(12, 'PPKn', 'PPKN', NULL, 0, 1, '2025-09-27 13:31:19.000000', '2025-09-27 13:31:19.000000', 'SMA/MA', 'Pilihan', 12, 'pilihan'),
(13, 'Bahasa Arab', 'BAR', NULL, 0, 1, '2025-09-27 13:31:19.000000', '2025-09-27 13:31:19.000000', 'SMA/MA', 'Pilihan', 13, 'pilihan'),
(14, 'Bahasa Jerman', 'BJE', NULL, 0, 1, '2025-09-27 13:31:19.000000', '2025-09-27 13:31:19.000000', 'SMA/MA', 'Pilihan', 14, 'pilihan'),
(15, 'Bahasa Prancis', 'BPR', NULL, 0, 1, '2025-09-27 13:31:19.000000', '2025-09-27 13:31:19.000000', 'SMA/MA', 'Pilihan', 15, 'pilihan'),
(16, 'Bahasa Jepang', 'BJP', NULL, 0, 1, '2025-09-27 13:31:19.000000', '2025-09-27 13:31:19.000000', 'SMA/MA', 'Pilihan', 16, 'pilihan'),
(17, 'Bahasa Korea', 'BKO', NULL, 0, 1, '2025-09-27 13:31:19.000000', '2025-09-27 13:31:19.000000', 'SMA/MA', 'Pilihan', 17, 'pilihan'),
(18, 'Bahasa Mandarin', 'BMA', NULL, 0, 1, '2025-09-27 13:31:19.000000', '2025-09-27 13:31:19.000000', 'SMA/MA', 'Pilihan', 18, 'pilihan'),
(19, 'Produk Kreatif dan Kewirausahaan', 'PKK', NULL, 0, 1, '2025-09-27 13:31:19.000000', '2025-09-27 13:31:19.000000', 'SMK/MAK', 'Produk_Kreatif_Kewirausahaan', 19, 'pilihan'),
(20, 'Matematika Lanjutan', 'MTK_L', NULL, 0, 1, '2025-09-27 13:31:19.000000', '2025-09-27 13:31:19.000000', 'SMA/MA', 'Pilihan', 1, 'pilihan'),
(21, 'Bahasa Indonesia Lanjutan', 'BIN_L', NULL, 0, 1, '2025-09-27 13:31:19.000000', '2025-09-27 13:31:19.000000', 'SMA/MA', 'Pilihan', 2, 'pilihan'),
(22, 'Bahasa Inggris Lanjutan', 'BIG_L', NULL, 0, 1, '2025-09-27 13:31:19.000000', '2025-09-27 13:31:19.000000', 'SMA/MA', 'Pilihan', 3, 'pilihan');

-- --------------------------------------------------------

--
-- Table structure for table `test_answers`
--

CREATE TABLE `test_answers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `test_result_id` bigint(20) UNSIGNED NOT NULL,
  `question_id` bigint(20) UNSIGNED NOT NULL,
  `selected_option_id` bigint(20) UNSIGNED NOT NULL,
  `answered_at` datetime(6) NOT NULL,
  `created_at` datetime(6) DEFAULT NULL,
  `updated_at` datetime(6) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `test_results`
--

CREATE TABLE `test_results` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `student_id` bigint(20) UNSIGNED NOT NULL,
  `subjects` varchar(0) NOT NULL,
  `start_time` datetime(6) NOT NULL,
  `end_time` datetime(6) DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'ongoing',
  `scores` varchar(0) DEFAULT NULL,
  `total_score` decimal(5,2) DEFAULT NULL,
  `recommendations` varchar(0) DEFAULT NULL,
  `created_at` datetime(6) DEFAULT NULL,
  `updated_at` datetime(6) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tka_schedules`
--

CREATE TABLE `tka_schedules` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `start_date` datetime(6) NOT NULL,
  `end_date` datetime(6) NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'scheduled',
  `type` varchar(255) NOT NULL DEFAULT 'regular',
  `instructions` text DEFAULT NULL,
  `target_schools` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`target_schools`)),
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_by` varchar(255) DEFAULT NULL,
  `created_at` datetime(6) DEFAULT NULL,
  `updated_at` datetime(6) DEFAULT NULL,
  `gelombang` varchar(255) DEFAULT NULL,
  `hari_pelaksanaan` varchar(255) DEFAULT NULL,
  `exam_venue` varchar(255) DEFAULT NULL,
  `exam_room` varchar(255) DEFAULT NULL,
  `contact_person` varchar(255) DEFAULT NULL,
  `contact_phone` varchar(255) DEFAULT NULL,
  `requirements` text DEFAULT NULL,
  `materials_needed` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` datetime(6) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` datetime(6) DEFAULT NULL,
  `updated_at` datetime(6) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `admins_username_unique` (`username`);

--
-- Indexes for table `answers`
--
ALTER TABLE `answers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `answers_student_id_foreign` (`student_id`),
  ADD KEY `answers_question_id_foreign` (`question_id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `majors`
--
ALTER TABLE `majors`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `major_recommendations`
--
ALTER TABLE `major_recommendations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `major_recommendations_is_active_index` (`is_active`),
  ADD KEY `idx_major_recommendations_category` (`category`),
  ADD KEY `idx_major_recommendations_is_active` (`is_active`),
  ADD KEY `idx_major_recommendations_category_is_active` (`category`,`is_active`),
  ADD KEY `major_recommendations_category_index` (`category`);

--
-- Indexes for table `major_subject_mappings`
--
ALTER TABLE `major_subject_mappings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `major_subject_mappings_major_id_foreign` (`major_id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indexes for table `program_studi`
--
ALTER TABLE `program_studi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `program_studi_rumpun_ilmu_id_foreign` (`rumpun_ilmu_id`);

--
-- Indexes for table `program_studi_subjects`
--
ALTER TABLE `program_studi_subjects`
  ADD PRIMARY KEY (`id`),
  ADD KEY `program_studi_subjects_program_studi_id_kurikulum_type_index` (`program_studi_id`,`kurikulum_type`),
  ADD KEY `program_studi_subjects_subject_id_kurikulum_type_index` (`subject_id`,`kurikulum_type`);

--
-- Indexes for table `questions`
--
ALTER TABLE `questions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_questions_subject` (`subject`),
  ADD KEY `idx_questions_type` (`type`),
  ADD KEY `idx_questions_created_at` (`created_at`),
  ADD KEY `idx_questions_subject_type` (`subject`,`type`),
  ADD KEY `questions_subject_index` (`subject`),
  ADD KEY `questions_type_index` (`type`),
  ADD KEY `questions_created_at_index` (`created_at`),
  ADD KEY `questions_subject_type_index` (`subject`,`type`);

--
-- Indexes for table `question_options`
--
ALTER TABLE `question_options`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_question_options_question_id` (`question_id`),
  ADD KEY `idx_question_options_is_correct` (`is_correct`),
  ADD KEY `idx_question_options_question_id_is_correct` (`question_id`,`is_correct`),
  ADD KEY `question_options_question_id_index` (`question_id`),
  ADD KEY `question_options_is_correct_index` (`is_correct`),
  ADD KEY `question_options_question_id_is_correct_index` (`question_id`,`is_correct`);

--
-- Indexes for table `recommendations`
--
ALTER TABLE `recommendations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `recommendations_student_id_foreign` (`student_id`);

--
-- Indexes for table `results`
--
ALTER TABLE `results`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_results_student_id` (`student_id`),
  ADD KEY `idx_results_created_at` (`created_at`),
  ADD KEY `idx_results_student_id_created_at` (`student_id`,`created_at`),
  ADD KEY `results_student_id_index` (`student_id`),
  ADD KEY `results_created_at_index` (`created_at`),
  ADD KEY `results_student_id_created_at_index` (`student_id`,`created_at`);

--
-- Indexes for table `rumpun_ilmu`
--
ALTER TABLE `rumpun_ilmu`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `schools`
--
ALTER TABLE `schools`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `schools_npsn_unique` (`npsn`),
  ADD KEY `idx_schools_npsn` (`npsn`),
  ADD KEY `idx_schools_name` (`name`),
  ADD KEY `idx_schools_created_at` (`created_at`),
  ADD KEY `schools_npsn_index` (`npsn`),
  ADD KEY `schools_name_index` (`name`),
  ADD KEY `schools_created_at_index` (`created_at`);

--
-- Indexes for table `school_classes`
--
ALTER TABLE `school_classes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `school_classes_school_id_name_unique` (`school_id`,`name`),
  ADD KEY `school_classes_school_id_name_index` (`school_id`,`name`),
  ADD KEY `school_classes_school_id_level_program_index` (`school_id`,`level`,`program`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `students_nisn_unique` (`nisn`),
  ADD KEY `idx_students_nisn` (`nisn`),
  ADD KEY `idx_students_school_id` (`school_id`),
  ADD KEY `idx_students_kelas` (`kelas`),
  ADD KEY `idx_students_created_at` (`created_at`),
  ADD KEY `idx_students_school_id_kelas` (`school_id`,`kelas`),
  ADD KEY `students_nisn_index` (`nisn`),
  ADD KEY `students_school_id_index` (`school_id`),
  ADD KEY `students_kelas_index` (`kelas`),
  ADD KEY `students_created_at_index` (`created_at`),
  ADD KEY `students_school_id_kelas_index` (`school_id`,`kelas`);

--
-- Indexes for table `student_choices`
--
ALTER TABLE `student_choices`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `student_choices_student_id_unique` (`student_id`),
  ADD KEY `idx_student_choices_student_id` (`student_id`),
  ADD KEY `idx_student_choices_major_id` (`major_id`),
  ADD KEY `idx_student_choices_created_at` (`created_at`),
  ADD KEY `idx_student_choices_student_id_major_id` (`student_id`,`major_id`),
  ADD KEY `student_choices_student_id_index` (`student_id`),
  ADD KEY `student_choices_major_id_index` (`major_id`),
  ADD KEY `student_choices_created_at_index` (`created_at`),
  ADD KEY `student_choices_student_id_major_id_index` (`student_id`,`major_id`);

--
-- Indexes for table `student_subjects`
--
ALTER TABLE `student_subjects`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_subjects_student_id_foreign` (`student_id`);

--
-- Indexes for table `subjects`
--
ALTER TABLE `subjects`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `test_answers`
--
ALTER TABLE `test_answers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `test_answers_test_result_id_question_id_unique` (`test_result_id`,`question_id`),
  ADD KEY `test_answers_test_result_id_question_id_index` (`test_result_id`,`question_id`),
  ADD KEY `test_answers_question_id_foreign` (`question_id`),
  ADD KEY `test_answers_selected_option_id_foreign` (`selected_option_id`);

--
-- Indexes for table `test_results`
--
ALTER TABLE `test_results`
  ADD PRIMARY KEY (`id`),
  ADD KEY `test_results_student_id_status_index` (`student_id`,`status`),
  ADD KEY `test_results_start_time_index` (`start_time`);

--
-- Indexes for table `tka_schedules`
--
ALTER TABLE `tka_schedules`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tka_schedules_start_date_end_date_index` (`start_date`,`end_date`),
  ADD KEY `tka_schedules_status_is_active_index` (`status`,`is_active`),
  ADD KEY `tka_schedules_type_index` (`type`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `answers`
--
ALTER TABLE `answers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `majors`
--
ALTER TABLE `majors`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `major_recommendations`
--
ALTER TABLE `major_recommendations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=65;

--
-- AUTO_INCREMENT for table `major_subject_mappings`
--
ALTER TABLE `major_subject_mappings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `program_studi`
--
ALTER TABLE `program_studi`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=66;

--
-- AUTO_INCREMENT for table `program_studi_subjects`
--
ALTER TABLE `program_studi_subjects`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `questions`
--
ALTER TABLE `questions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `question_options`
--
ALTER TABLE `question_options`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `recommendations`
--
ALTER TABLE `recommendations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `results`
--
ALTER TABLE `results`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `rumpun_ilmu`
--
ALTER TABLE `rumpun_ilmu`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `schools`
--
ALTER TABLE `schools`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `school_classes`
--
ALTER TABLE `school_classes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT for table `student_choices`
--
ALTER TABLE `student_choices`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `student_subjects`
--
ALTER TABLE `student_subjects`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `subjects`
--
ALTER TABLE `subjects`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `test_answers`
--
ALTER TABLE `test_answers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `test_results`
--
ALTER TABLE `test_results`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tka_schedules`
--
ALTER TABLE `tka_schedules`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
