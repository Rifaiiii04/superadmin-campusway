<?php

// Add major descriptions to database
echo "Adding Major Descriptions to Database...\n\n";

try {
    // Load Laravel
    require_once 'vendor/autoload.php';
    $app = require_once 'bootstrap/app.php';
    $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
    
    // Major descriptions data
    $majorDescriptions = [
        [
            'no' => 1,
            'deskripsi' => 'Prodi Seni membekali mahasiswa dengan kemampuan artistik, pemahaman estetika, serta keterampilan teknis dalam berbagai cabang seni seperti musik, tari, teater, dan seni rupa. Selama perkuliahan, mahasiswa didorong untuk mengekspresikan kreativitas melalui karya nyata, sekaligus memahami peran seni dalam budaya dan sejarah masyarakat. Proses belajar menggabungkan teori, praktik langsung, serta pameran atau pertunjukan sehingga mahasiswa terbiasa menghadapi audiens. Lulusan diharapkan mampu menjadi seniman profesional, pengajar seni, kurator, atau pengembang industri kreatif yang relevan dengan kebutuhan zaman.'
        ],
        [
            'no' => 2,
            'deskripsi' => 'Prodi Sejarah menekankan pada pemahaman peristiwa masa lalu, analisis kritis sumber sejarah, serta bagaimana sejarah membentuk identitas bangsa dan dinamika dunia. Mahasiswa mempelajari metode penelitian sejarah, penulisan akademik, dan interpretasi dokumen atau artefak. Pengalaman belajar sering melibatkan kunjungan lapangan ke situs bersejarah, arsip, dan museum. Lulusan tidak hanya siap menjadi sejarawan, guru, atau peneliti, tetapi juga berkontribusi pada bidang kebudayaan, media, dan kebijakan publik yang memerlukan perspektif historis.'
        ],
        [
            'no' => 3,
            'deskripsi' => 'Prodi Linguistik mempelajari struktur bahasa, fonologi, morfologi, sintaksis, semantik, dan pragmatik, serta peran bahasa dalam komunikasi sehari-hari dan kebudayaan. Mahasiswa diajak menganalisis fenomena bahasa secara ilmiah, termasuk bahasa daerah dan bahasa asing, menggunakan teori modern dan pendekatan teknologi. Proses pembelajaran mencakup penelitian lapangan, analisis teks, hingga penerapan linguistik dalam bidang penerjemahan, pendidikan, dan teknologi bahasa. Lulusan berpotensi berkarier sebagai peneliti bahasa, dosen, ahli penerjemahan, atau pengembang aplikasi berbasis linguistik.'
        ],
        [
            'no' => 4,
            'deskripsi' => 'Prodi Susastra mengajarkan mahasiswa untuk memahami, menganalisis, dan mengapresiasi karya sastra baik klasik maupun modern, dari dalam negeri maupun luar negeri. Fokusnya tidak hanya pada isi cerita, tetapi juga konteks sosial, budaya, dan filsafat yang melatarbelakanginya. Mahasiswa berlatih menulis esai kritis, melakukan kajian sastra komparatif, hingga menciptakan karya sastra baru. Lingkungan belajar mendukung diskusi kreatif, penelitian teks, dan eksplorasi lintas budaya. Lulusan dapat menjadi penulis, kritikus sastra, editor, peneliti, atau pekerja di industri kreatif dan penerbitan.'
        ],
        [
            'no' => 5,
            'deskripsi' => 'Prodi Filsafat mengembangkan kemampuan berpikir kritis, analitis, dan reflektif melalui kajian terhadap gagasan besar sepanjang sejarah manusia. Mahasiswa mempelajari tokoh-tokoh dan aliran filsafat, etika, logika, metafisika, hingga filsafat ilmu. Pendekatan pembelajaran lebih banyak melalui diskusi, debat intelektual, serta penulisan argumentatif yang melatih mahasiswa menyusun ide dengan logis. Lulusan prodi ini diharapkan menjadi pemikir yang mampu berkontribusi dalam bidang akademik, pendidikan, penulisan, maupun pekerjaan yang membutuhkan analisis mendalam seperti kebijakan publik dan konsultan etika.'
        ],
        [
            'no' => 6,
            'deskripsi' => 'Prodi Sosial berfokus pada pemahaman struktur, dinamika, dan peran masyarakat dalam kehidupan sehari-hari. Mahasiswa mempelajari teori sosiologi, antropologi, serta fenomena sosial yang terjadi di berbagai lapisan masyarakat. Proses pembelajaran menekankan pada riset lapangan, analisis kasus nyata, serta diskusi kritis untuk membangun kemampuan memahami masalah sosial dari berbagai perspektif. Lulusan prodi ini dapat berkarier sebagai peneliti sosial, konsultan kebijakan, pekerja sosial, maupun berkontribusi di lembaga pemerintah dan organisasi non-pemerintah yang menangani isu masyarakat.'
        ],
        [
            'no' => 7,
            'deskripsi' => 'Prodi Ekonomi membekali mahasiswa dengan pemahaman tentang teori ekonomi mikro dan makro, kebijakan fiskal, moneter, serta dinamika pasar dalam skala lokal maupun global. Mahasiswa juga dilatih dalam penggunaan alat analisis ekonomi, statistik, dan pemodelan untuk membaca data dan merumuskan solusi terhadap permasalahan ekonomi. Pengalaman belajar mencakup simulasi kebijakan, studi kasus industri, hingga riset empiris. Lulusan prodi ini diharapkan mampu berkontribusi sebagai ekonom, analis keuangan, konsultan bisnis, atau pengambil kebijakan di sektor publik dan swasta.'
        ],
        [
            'no' => 8,
            'deskripsi' => 'Prodi Pertahanan mempelajari konsep keamanan nasional, strategi pertahanan, dan manajemen konflik dalam konteks lokal maupun internasional. Mahasiswa diajak memahami aspek politik, hukum, serta teknologi yang mendukung pertahanan negara. Pembelajaran menggabungkan teori, studi kasus, serta praktik analisis strategi militer dan kebijakan keamanan. Lulusan prodi ini dipersiapkan untuk berkarier di institusi pertahanan, lembaga pemerintah, maupun organisasi yang bergerak di bidang keamanan, diplomasi, serta hubungan internasional.'
        ],
        [
            'no' => 9,
            'deskripsi' => 'Prodi Psikologi fokus pada studi tentang perilaku, pikiran, dan proses mental manusia. Mahasiswa mempelajari teori-teori psikologi, metode penelitian, serta aplikasi psikologi dalam berbagai bidang seperti pendidikan, klinis, industri, dan organisasi. Proses pembelajaran melibatkan eksperimen, observasi, serta praktik konseling untuk memahami manusia secara utuh. Lulusan prodi ini memiliki peluang berkarier sebagai psikolog, konselor, peneliti, pengembang sumber daya manusia, maupun bekerja di bidang kesehatan mental dan kesejahteraan masyarakat.'
        ],
        [
            'no' => 10,
            'deskripsi' => 'Prodi Kimia mempelajari struktur, sifat, reaksi, dan aplikasi senyawa kimia yang membentuk kehidupan dan teknologi modern. Mahasiswa mendapatkan dasar teori kimia anorganik, organik, fisik, dan biokimia, serta pengalaman praktikum laboratorium yang intensif. Pembelajaran juga menekankan pada riset eksperimental, analisis instrumen, dan penerapan kimia dalam industri farmasi, energi, pangan, hingga lingkungan. Lulusan prodi ini memiliki peluang berkarier sebagai peneliti, analis laboratorium, pengembang produk, atau ahli kimia di berbagai sektor industri.'
        ],
        [
            'no' => 11,
            'deskripsi' => 'Prodi Ilmu atau Sains Kebumian fokus pada kajian geologi, geofisika, dan dinamika bumi untuk memahami proses alam seperti gempa bumi, gunung berapi, serta struktur batuan dan mineral. Mahasiswa dilatih menggunakan metode survei lapangan, pemetaan geologi, serta teknologi analisis data kebumian. Pengalaman belajar mencakup penelitian langsung di lapangan, pemodelan komputer, dan analisis laboratorium. Lulusan dapat berkarier sebagai ahli geologi, konsultan tambang, peneliti kebencanaan, maupun profesional di bidang eksplorasi energi dan lingkungan.'
        ],
        [
            'no' => 12,
            'deskripsi' => 'Prodi Ilmu atau Sains Kelautan mempelajari ekosistem laut, biota, serta pemanfaatan sumber daya kelautan secara berkelanjutan. Mahasiswa akan terlibat dalam studi biologi laut, oseanografi, serta teknologi eksplorasi laut. Kegiatan belajar banyak dilakukan melalui penelitian lapangan di pantai, laut, dan laboratorium kelautan. Prodi ini menekankan pentingnya konservasi dan inovasi pemanfaatan laut untuk pangan, energi, maupun pariwisata. Lulusan berpotensi menjadi ahli kelautan, peneliti lingkungan laut, atau pengelola sumber daya perikanan dan pariwisata bahari.'
        ],
        [
            'no' => 13,
            'deskripsi' => 'Prodi Biologi mempelajari kehidupan mulai dari organisme mikroskopis hingga ekosistem yang kompleks. Mahasiswa mendalami topik genetika, ekologi, mikrobiologi, zoologi, dan botani, serta berlatih melakukan penelitian ilmiah di laboratorium maupun alam. Proses belajar menekankan keterampilan observasi, eksperimen, dan analisis data biologi. Lulusan dapat berkarier di bidang pendidikan, penelitian, bioteknologi, konservasi lingkungan, maupun industri kesehatan dan pangan.'
        ],
        [
            'no' => 14,
            'deskripsi' => 'Prodi Biofisika menggabungkan ilmu biologi dan fisika untuk memahami fenomena kehidupan pada tingkat molekuler hingga sistemik. Mahasiswa mempelajari bioenergetika, mekanisme molekul, serta penggunaan alat fisika dalam penelitian biologi. Proses pembelajaran mencakup eksperimen laboratorium, pemodelan komputer, dan analisis data kuantitatif. Lulusan dapat berkarier sebagai peneliti di bidang kesehatan, bioteknologi, bioinformatika, serta industri yang menggabungkan teknologi dengan ilmu hayati.'
        ],
        [
            'no' => 15,
            'deskripsi' => 'Prodi Fisika mempelajari hukum-hukum dasar alam semesta, mulai dari partikel subatom hingga kosmos. Mahasiswa mendalami mekanika, elektromagnetisme, termodinamika, fisika kuantum, dan fisika material, serta mengembangkan keterampilan pemecahan masalah berbasis matematis. Praktikum laboratorium dan riset menjadi bagian penting dalam pembelajaran. Lulusan dapat berkontribusi dalam penelitian, pendidikan, teknologi energi, instrumentasi, dan industri berbasis teknologi tinggi.'
        ],
        [
            'no' => 16,
            'deskripsi' => 'Prodi Astronomi berfokus pada studi tentang benda-benda langit, struktur galaksi, evolusi bintang, dan kosmologi. Mahasiswa mempelajari fisika astronomi, penggunaan teleskop, serta pemodelan komputer untuk memahami fenomena alam semesta. Belajar sering melibatkan observasi langsung di observatorium dan analisis data astronomi. Lulusan dapat berkarier sebagai peneliti astronomi, pengajar, atau bekerja di pusat sains dan teknologi antariksa.'
        ],
        [
            'no' => 17,
            'deskripsi' => 'Prodi Komputer mengajarkan dasar ilmu komputer, algoritma, pemrograman, jaringan, dan kecerdasan buatan. Mahasiswa dilatih untuk merancang sistem perangkat lunak maupun perangkat keras, serta memahami teori komputasi dan penerapannya dalam kehidupan sehari-hari. Proses pembelajaran banyak berbasis proyek untuk menciptakan aplikasi atau solusi teknologi. Lulusan berpeluang menjadi software engineer, data scientist, ahli keamanan siber, maupun peneliti teknologi informasi.'
        ],
        [
            'no' => 18,
            'deskripsi' => 'Prodi Logika mempelajari prinsip-prinsip penalaran formal, logika simbolik, teori himpunan, dan filsafat matematika. Mahasiswa mengembangkan keterampilan berpikir analitis, kritis, dan sistematis untuk menyelesaikan masalah kompleks. Pembelajaran melibatkan latihan penalaran, pemodelan formal, serta penerapan logika dalam ilmu komputer, hukum, dan filsafat. Lulusan dapat berkarier sebagai peneliti, akademisi, atau profesional di bidang teknologi dan analisis data.'
        ],
        [
            'no' => 19,
            'deskripsi' => 'Prodi Matematika membekali mahasiswa dengan pemahaman teori bilangan, aljabar, kalkulus, geometri, analisis, serta statistik. Mahasiswa dilatih untuk berpikir abstrak, memecahkan masalah, dan menggunakan model matematis dalam berbagai bidang. Proses belajar menggabungkan kuliah teori, latihan soal, penelitian, dan aplikasi nyata. Lulusan dapat berkarier sebagai peneliti, pengajar, analis data, aktuaria, atau profesional di bidang teknologi dan keuangan.'
        ],
        [
            'no' => 20,
            'deskripsi' => 'Prodi Ilmu dan Sains Pertanian fokus pada produksi tanaman, teknologi budidaya, dan pengelolaan sumber daya alam untuk mendukung ketahanan pangan. Mahasiswa mempelajari agronomi, tanah, hama, penyakit tanaman, serta teknologi pertanian modern. Praktik lapangan dan riset menjadi bagian penting dalam proses belajar. Lulusan dapat bekerja sebagai peneliti, penyuluh pertanian, pengusaha agribisnis, atau ahli di bidang ketahanan pangan dan teknologi pertanian.'
        ],
        [
            'no' => 21,
            'deskripsi' => 'Prodi Peternakan membekali mahasiswa dengan ilmu tentang pemeliharaan, perawatan, dan pengembangan hewan ternak untuk menghasilkan pangan dan produk turunan. Mahasiswa mempelajari genetika ternak, nutrisi, kesehatan hewan, serta teknologi peternakan. Pembelajaran mencakup laboratorium, praktik lapangan, dan kerja sama dengan industri peternakan. Lulusan dapat bekerja sebagai manajer peternakan, konsultan agribisnis, peneliti, atau wirausahawan di bidang pangan hewani.'
        ],
        [
            'no' => 22,
            'deskripsi' => 'Prodi Ilmu atau Sains Perikanan berfokus pada pengelolaan sumber daya perairan, budidaya ikan, dan teknologi perikanan. Mahasiswa mempelajari biologi perairan, konservasi, serta teknologi penangkapan dan pengolahan hasil perikanan. Proses belajar mencakup penelitian lapangan, laboratorium, serta praktik di kawasan pesisir. Lulusan dapat berkarier sebagai peneliti perikanan, pengelola akuakultur, atau profesional di sektor industri perikanan berkelanjutan.'
        ],
        [
            'no' => 23,
            'deskripsi' => 'Prodi Arsitektur mengajarkan perencanaan, perancangan, dan pembangunan ruang serta bangunan yang fungsional, estetis, dan berkelanjutan. Mahasiswa mempelajari teori arsitektur, desain bangunan, teknologi konstruksi, dan perencanaan kota. Proses belajar berbasis studio desain, proyek nyata, serta penggunaan perangkat lunak desain modern. Lulusan dapat menjadi arsitek, perencana kota, konsultan desain, atau pengembang properti.'
        ],
        [
            'no' => 24,
            'deskripsi' => 'Prodi Perencanaan Wilayah fokus pada pengelolaan ruang dan wilayah untuk menciptakan lingkungan yang tertata, produktif, dan berkelanjutan. Mahasiswa mempelajari teori tata ruang, ekonomi wilayah, transportasi, serta analisis kebijakan pembangunan. Proses belajar melibatkan simulasi perencanaan, penelitian lapangan, dan penggunaan teknologi informasi geografis. Lulusan dapat bekerja sebagai perencana kota, konsultan pembangunan, atau pejabat di lembaga perencanaan pemerintah.'
        ],
        [
            'no' => 25,
            'deskripsi' => 'Prodi Desain mengajarkan keterampilan menciptakan karya visual, produk, atau ruang yang fungsional sekaligus estetis. Mahasiswa belajar teori desain, prinsip komunikasi visual, teknologi digital, serta riset pengguna. Proses pembelajaran berbasis studio, proyek nyata, dan kolaborasi dengan industri kreatif. Lulusan dapat menjadi desainer grafis, desainer produk, ilustrator, atau profesional di bidang desain digital dan industri kreatif.'
        ],
        [
            'no' => 26,
            'deskripsi' => 'Prodi Ilmu atau Sains Akuntansi membekali mahasiswa dengan keterampilan mencatat, menganalisis, dan melaporkan informasi keuangan. Mahasiswa mempelajari teori akuntansi, audit, perpajakan, dan sistem informasi akuntansi. Proses belajar mencakup simulasi pencatatan keuangan, penggunaan software akuntansi, dan studi kasus perusahaan. Lulusan dapat bekerja sebagai akuntan publik, auditor, konsultan pajak, atau analis keuangan.'
        ],
        [
            'no' => 27,
            'deskripsi' => 'Prodi Ilmu atau Sains Manajemen fokus pada perencanaan, pengorganisasian, pengendalian, dan kepemimpinan dalam organisasi. Mahasiswa mempelajari teori manajemen, pemasaran, sumber daya manusia, keuangan, dan operasional. Proses pembelajaran melibatkan studi kasus, simulasi bisnis, serta proyek kolaboratif. Lulusan dapat berkarier sebagai manajer, konsultan bisnis, entrepreneur, atau pemimpin organisasi di sektor publik dan swasta.'
        ],
        [
            'no' => 28,
            'deskripsi' => 'Prodi Logistik mengajarkan manajemen rantai pasok, transportasi, distribusi, dan pergudangan untuk memastikan efisiensi arus barang dan jasa. Mahasiswa mempelajari teori logistik, teknologi informasi logistik, serta praktik operasional. Proses belajar mencakup studi kasus industri, simulasi rantai pasok, dan kerja sama dengan perusahaan logistik. Lulusan dapat menjadi manajer logistik, analis supply chain, atau konsultan manajemen distribusi.'
        ],
        [
            'no' => 29,
            'deskripsi' => 'Prodi Administrasi Bisnis membekali mahasiswa dengan pengetahuan administrasi, manajemen perkantoran, dan sistem informasi bisnis. Mahasiswa belajar tentang keuangan, pemasaran, sumber daya manusia, serta manajemen operasional. Proses pembelajaran menekankan keterampilan praktis, simulasi bisnis, dan penggunaan teknologi administrasi modern. Lulusan dapat bekerja sebagai administrator, staf manajemen, konsultan bisnis, atau entrepreneur.'
        ],
        [
            'no' => 30,
            'deskripsi' => 'Prodi Bisnis mempelajari strategi pengembangan usaha, manajemen pemasaran, keuangan, serta inovasi produk dan layanan. Mahasiswa dilatih untuk berpikir kreatif, mengambil keputusan strategis, dan memahami dinamika pasar. Proses belajar berbasis proyek, studi kasus, serta kerja sama dengan dunia usaha. Lulusan dapat berkarier sebagai wirausahawan, manajer bisnis, konsultan, atau pengembang startup inovatif.'
        ],
        [
            'no' => 31,
            'deskripsi' => 'Prodi Ilmu atau Sains Komunikasi mempelajari teori dan praktik komunikasi, baik interpersonal, organisasi, maupun massa. Mahasiswa belajar tentang media, jurnalistik, hubungan masyarakat, periklanan, hingga komunikasi digital. Proses pembelajaran melibatkan proyek kreatif, produksi media, riset audiens, dan praktik di lapangan. Lulusan dapat berkarier sebagai jurnalis, public relations, content creator, atau konsultan komunikasi di berbagai sektor.'
        ],
        [
            'no' => 32,
            'deskripsi' => 'Prodi Pendidikan membekali mahasiswa dengan teori pedagogi, kurikulum, psikologi pendidikan, serta strategi pengajaran yang efektif. Mahasiswa dilatih dalam microteaching, praktik mengajar, serta penggunaan teknologi pendidikan. Prodi ini menekankan pembentukan guru dan pendidik yang profesional, inovatif, dan berkarakter. Lulusan siap menjadi guru, pengembang kurikulum, konselor pendidikan, atau praktisi di lembaga pelatihan.'
        ],
        [
            'no' => 33,
            'deskripsi' => 'Prodi Teknik atau Rekayasa fokus pada penerapan prinsip ilmiah dan teknologi untuk merancang, membangun, dan mengelola sistem atau produk. Mahasiswa mempelajari matematika, fisika, dan teknologi sesuai bidang teknik yang dipilih, seperti teknik sipil, mesin, elektro, atau industri. Proses pembelajaran mencakup praktikum, proyek rekayasa, dan kerja sama dengan industri. Lulusan dapat berkarier sebagai insinyur, konsultan teknik, peneliti, maupun pengembang teknologi.'
        ],
        [
            'no' => 34,
            'deskripsi' => 'Prodi Ilmu atau Sains Lingkungan mempelajari interaksi manusia dengan alam serta cara mengelola lingkungan secara berkelanjutan. Mahasiswa mempelajari ekologi, pencemaran, konservasi, serta teknologi pengelolaan lingkungan. Proses belajar mencakup riset lapangan, analisis laboratorium, dan simulasi kebijakan lingkungan. Lulusan dapat bekerja sebagai konsultan lingkungan, peneliti, analis kebijakan, atau aktivis di bidang keberlanjutan.'
        ],
        [
            'no' => 35,
            'deskripsi' => 'Prodi Kehutanan fokus pada pengelolaan hutan, konservasi, dan pemanfaatan sumber daya hutan secara lestari. Mahasiswa mempelajari ekologi hutan, manajemen hutan, teknologi hasil hutan, serta kebijakan kehutanan. Kegiatan belajar melibatkan praktik lapangan, penelitian, dan kerja sama dengan industri kehutanan. Lulusan dapat menjadi rimbawan, peneliti kehutanan, konsultan konservasi, atau pengelola kawasan hutan.'
        ],
        [
            'no' => 36,
            'deskripsi' => 'Prodi Ilmu atau Sains Kedokteran membekali mahasiswa dengan pengetahuan anatomi, fisiologi, patologi, dan praktik klinis untuk memahami serta menangani kesehatan manusia. Mahasiswa belajar melalui kuliah, praktikum laboratorium, serta praktik klinik di rumah sakit pendidikan. Proses belajar menekankan keterampilan medis, etika, dan empati. Lulusan dapat berkarier sebagai dokter umum, dokter spesialis, peneliti medis, atau akademisi.'
        ],
        [
            'no' => 37,
            'deskripsi' => 'Prodi Ilmu atau Sains Kedokteran Gigi berfokus pada kesehatan mulut dan gigi, termasuk pencegahan, diagnosis, serta perawatan penyakit gigi. Mahasiswa belajar anatomi mulut, radiologi, prostodonti, hingga bedah mulut. Pembelajaran melibatkan praktik laboratorium, klinik, dan penelitian. Lulusan dapat berkarier sebagai dokter gigi, peneliti kesehatan gigi, atau pendidik kedokteran gigi.'
        ],
        [
            'no' => 38,
            'deskripsi' => 'Prodi Ilmu atau Sains Veteriner mempelajari kesehatan hewan, pencegahan penyakit, serta keamanan pangan asal hewan. Mahasiswa belajar anatomi, fisiologi hewan, patologi, farmakologi, dan bedah hewan. Proses pembelajaran mencakup praktik klinik hewan, laboratorium, dan riset kesehatan hewan. Lulusan dapat menjadi dokter hewan, peneliti veteriner, konsultan peternakan, atau pegawai lembaga kesehatan hewan.'
        ],
        [
            'no' => 39,
            'deskripsi' => 'Prodi Ilmu Farmasi mempelajari obat-obatan, mulai dari penemuan, formulasi, produksi, hingga penggunaannya secara aman. Mahasiswa belajar kimia farmasi, farmakologi, farmakognosi, dan teknologi farmasi. Proses belajar mencakup penelitian di laboratorium, praktik di apotek, serta kerja sama dengan industri farmasi. Lulusan dapat berkarier sebagai apoteker, peneliti farmasi, konsultan regulasi obat, atau wirausahawan di bidang kesehatan.'
        ],
        [
            'no' => 40,
            'deskripsi' => 'Prodi Ilmu atau Sains Gizi membekali mahasiswa dengan pemahaman nutrisi, dietetik, dan hubungan makanan dengan kesehatan manusia. Mahasiswa mempelajari biokimia gizi, nutrisi klinis, serta gizi masyarakat. Proses pembelajaran mencakup praktik laboratorium, penelitian lapangan, serta konseling gizi. Lulusan dapat berkarier sebagai ahli gizi klinis, konsultan nutrisi, peneliti gizi, atau praktisi kesehatan masyarakat.'
        ],
        [
            'no' => 41,
            'deskripsi' => 'Prodi Kesehatan Masyarakat mempelajari strategi pencegahan penyakit, promosi kesehatan, serta kebijakan kesehatan masyarakat. Mahasiswa belajar epidemiologi, manajemen kesehatan, biostatistik, dan kesehatan lingkungan. Proses pembelajaran melibatkan penelitian lapangan, analisis data kesehatan, dan praktik kerja di institusi kesehatan. Lulusan dapat bekerja sebagai tenaga kesehatan masyarakat, peneliti, konsultan kesehatan, atau pejabat di lembaga kesehatan.'
        ],
        [
            'no' => 42,
            'deskripsi' => 'Prodi Kebidanan membekali mahasiswa dengan ilmu dan keterampilan dalam pelayanan kesehatan ibu, anak, serta reproduksi. Mahasiswa mempelajari anatomi, fisiologi, kehamilan, persalinan, dan neonatus. Proses belajar mencakup praktik klinik, simulasi persalinan, dan penelitian di bidang kebidanan. Lulusan dapat berkarier sebagai bidan, konselor kesehatan reproduksi, atau praktisi di fasilitas kesehatan.'
        ],
        [
            'no' => 43,
            'deskripsi' => 'Prodi Keperawatan mempelajari konsep perawatan kesehatan holistik untuk pasien di berbagai kondisi. Mahasiswa mempelajari ilmu dasar keperawatan, gawat darurat, keperawatan komunitas, dan manajemen pelayanan kesehatan. Proses pembelajaran melibatkan praktik klinik, simulasi perawatan, dan penelitian. Lulusan dapat bekerja sebagai perawat klinis, pendidik keperawatan, peneliti, atau manajer pelayanan kesehatan.'
        ],
        [
            'no' => 44,
            'deskripsi' => 'Prodi Kesehatan mempelajari aspek umum kesehatan manusia, termasuk promosi kesehatan, pencegahan penyakit, dan layanan dasar kesehatan. Mahasiswa belajar dasar ilmu kesehatan, kebijakan kesehatan, serta teknologi pendukung layanan kesehatan. Proses belajar mencakup penelitian, praktik lapangan, dan kerja sama dengan institusi kesehatan. Lulusan dapat berkarier sebagai tenaga kesehatan, analis kebijakan kesehatan, atau pekerja di lembaga pelayanan kesehatan.'
        ],
        [
            'no' => 45,
            'deskripsi' => 'Prodi Ilmu atau Sains Informasi berfokus pada pengelolaan, analisis, dan pemanfaatan informasi menggunakan teknologi. Mahasiswa belajar sistem informasi, basis data, analisis data, serta teknologi informasi terkini. Proses belajar berbasis proyek, penelitian, dan praktik di laboratorium komputer. Lulusan dapat menjadi analis sistem, data scientist, konsultan TI, atau pengembang solusi berbasis informasi.'
        ],
        [
            'no' => 46,
            'deskripsi' => 'Prodi Hukum mempelajari teori dan praktik hukum, mulai dari hukum perdata, pidana, tata negara, hingga hukum internasional. Mahasiswa dilatih untuk berpikir kritis, menganalisis kasus, dan memahami prosedur hukum. Proses belajar mencakup simulasi peradilan, riset hukum, dan praktik kerja di lembaga hukum. Lulusan dapat berkarier sebagai advokat, jaksa, hakim, notaris, konsultan hukum, atau akademisi.'
        ],
        [
            'no' => 47,
            'deskripsi' => 'Prodi Ilmu atau Sains Militer fokus pada strategi pertahanan, manajemen sumber daya militer, serta teknologi pertahanan. Mahasiswa mempelajari sejarah militer, geopolitik, kepemimpinan, dan taktik militer modern. Proses belajar mencakup simulasi strategi, riset pertahanan, serta kerja sama dengan institusi militer. Lulusan dapat berkarier sebagai perwira militer, peneliti pertahanan, atau konsultan strategi keamanan.'
        ],
        [
            'no' => 48,
            'deskripsi' => 'Prodi Urusan Publik mempelajari administrasi, kebijakan, dan tata kelola sektor publik. Mahasiswa belajar teori pemerintahan, kebijakan publik, manajemen birokrasi, serta pelayanan masyarakat. Proses pembelajaran mencakup studi kasus, simulasi kebijakan, dan kerja sama dengan lembaga pemerintahan. Lulusan dapat berkarier sebagai birokrat, analis kebijakan, konsultan publik, atau pengelola lembaga non-pemerintah.'
        ],
        [
            'no' => 49,
            'deskripsi' => 'Prodi Ilmu atau Sains Keolahragaan mempelajari teori dan praktik olahraga, fisiologi, serta manajemen kegiatan olahraga. Mahasiswa belajar ilmu gerak tubuh, gizi olahraga, dan kepelatihan. Proses belajar mencakup praktik olahraga, penelitian, dan pengelolaan event olahraga. Lulusan dapat bekerja sebagai pelatih, instruktur kebugaran, manajer olahraga, atau peneliti bidang keolahragaan.'
        ],
        [
            'no' => 50,
            'deskripsi' => 'Prodi Pariwisata mempelajari manajemen destinasi, perhotelan, perjalanan, serta pemasaran pariwisata. Mahasiswa belajar teori pariwisata, budaya, dan strategi pelayanan. Proses belajar mencakup praktik lapangan, proyek pariwisata, serta kerja sama dengan industri perhotelan dan perjalanan. Lulusan dapat berkarier sebagai manajer pariwisata, konsultan destinasi, pemandu wisata, atau entrepreneur pariwisata.'
        ],
        [
            'no' => 51,
            'deskripsi' => 'Prodi Transportasi berfokus pada perencanaan, manajemen, dan teknologi transportasi darat, laut, maupun udara. Mahasiswa belajar rekayasa transportasi, logistik, kebijakan transportasi, serta teknologi kendaraan. Proses pembelajaran mencakup simulasi sistem transportasi, penelitian, dan kerja sama dengan industri transportasi. Lulusan dapat menjadi perencana transportasi, manajer operasional, atau konsultan transportasi.'
        ],
        [
            'no' => 52,
            'deskripsi' => 'Prodi Bioteknologi, Biokewirausahaan, atau Bioinformatika menggabungkan ilmu biologi dengan teknologi dan bisnis. Mahasiswa mempelajari teknik bioteknologi, analisis bioinformatika, serta strategi kewirausahaan berbasis sains. Proses belajar mencakup penelitian laboratorium, proyek inovasi, dan pengembangan produk berbasis biologi. Lulusan dapat berkarier sebagai peneliti, pengembang produk bioteknologi, entrepreneur, atau konsultan bioindustri.'
        ],
        [
            'no' => 53,
            'deskripsi' => 'Prodi Geografi, Lingkungan, atau Sains Informasi Geografi mempelajari fenomena bumi, penggunaan lahan, serta teknologi informasi geografis. Mahasiswa belajar pemetaan, sistem informasi geografis (SIG), serta analisis spasial. Proses pembelajaran mencakup penelitian lapangan, pemodelan komputer, dan praktik menggunakan perangkat SIG. Lulusan dapat menjadi ahli geografi, analis spasial, konsultan lingkungan, atau perencana wilayah.'
        ],
        [
            'no' => 54,
            'deskripsi' => 'Prodi Informatika Medis atau Kesehatan menggabungkan teknologi informasi dengan bidang kesehatan. Mahasiswa mempelajari sistem informasi kesehatan, rekam medis elektronik, analisis data kesehatan, serta teknologi pendukung pelayanan medis. Proses belajar mencakup proyek TI kesehatan, penelitian, dan praktik di fasilitas kesehatan. Lulusan dapat berkarier sebagai analis sistem kesehatan, konsultan TI medis, atau peneliti di bidang teknologi kesehatan.'
        ],
        [
            'no' => 55,
            'deskripsi' => 'Prodi Konservasi Biologi, Hewan Liar, Hutan, atau Sumber Daya Alam berfokus pada pelestarian keanekaragaman hayati dan sumber daya alam. Mahasiswa mempelajari ekologi konservasi, manajemen kawasan lindung, serta teknologi konservasi. Proses belajar mencakup penelitian lapangan, program konservasi, dan kerja sama dengan lembaga lingkungan. Lulusan dapat berkarier sebagai konservasionis, peneliti, atau konsultan lingkungan.'
        ],
        [
            'no' => 56,
            'deskripsi' => 'Prodi Teknologi Pangan, Hasil Pertanian, Peternakan, atau Perikanan mempelajari pengolahan, keamanan, dan inovasi produk pangan. Mahasiswa mempelajari teknologi pengawetan, rekayasa pangan, dan manajemen mutu. Proses belajar mencakup praktikum laboratorium, proyek inovasi produk, serta kerja sama dengan industri pangan. Lulusan dapat bekerja sebagai teknolog pangan, peneliti, konsultan industri, atau entrepreneur di sektor pangan.'
        ],
        [
            'no' => 57,
            'deskripsi' => 'Prodi Sains Data mempelajari pengumpulan, pengolahan, analisis, dan interpretasi data dalam jumlah besar. Mahasiswa belajar statistik, machine learning, data mining, serta pemrograman. Proses pembelajaran berbasis proyek dengan data nyata, riset, dan aplikasi industri. Lulusan dapat menjadi data scientist, analis data, konsultan bisnis berbasis data, atau peneliti.'
        ],
        [
            'no' => 58,
            'deskripsi' => 'Prodi Sains Perkopian mempelajari seluruh rantai nilai kopi, mulai dari budidaya, pengolahan pasca panen, hingga bisnis kopi. Mahasiswa belajar ilmu pertanian, teknologi pangan, manajemen bisnis, serta budaya kopi. Proses belajar mencakup praktik lapangan di kebun kopi, laboratorium, serta proyek kewirausahaan. Lulusan dapat berkarier sebagai ahli kopi, peneliti, pengusaha kopi, atau konsultan industri kopi.'
        ],
        [
            'no' => 59,
            'deskripsi' => 'Prodi Studi Humanitas mempelajari manusia dari perspektif budaya, sosial, dan sejarah untuk memahami nilai, identitas, serta peran manusia dalam peradaban. Mahasiswa belajar antropologi, sosiologi, filsafat, dan kajian budaya. Proses belajar menekankan analisis kritis, penelitian, serta diskusi interdisipliner. Lulusan dapat berkarier sebagai peneliti humaniora, pendidik, konsultan budaya, atau pekerja di organisasi yang berfokus pada isu kemanusiaan.'
        ]
    ];
    
    echo "Found " . count($majorDescriptions) . " major descriptions to add\n\n";
    
    // Get existing majors
    $existingMajors = \App\Models\MajorRecommendation::all();
    echo "Found " . $existingMajors->count() . " existing majors in database\n\n";
    
    $updated = 0;
    $notFound = 0;
    
    foreach ($majorDescriptions as $majorData) {
        $no = $majorData['no'];
        $deskripsi = $majorData['deskripsi'];
        
        // Find major by ID (assuming no corresponds to ID)
        $major = \App\Models\MajorRecommendation::find($no);
        
        if ($major) {
            $major->description = $deskripsi;
            $major->save();
            $updated++;
            echo "✅ Updated major #{$no}: {$major->major_name}\n";
        } else {
            $notFound++;
            echo "❌ Major #{$no} not found in database\n";
        }
    }
    
    echo "\n=== Summary ===\n";
    echo "Updated: {$updated} majors\n";
    echo "Not found: {$notFound} majors\n";
    
    // Verify updates
    echo "\n=== Verification ===\n";
    $majorsWithDescription = \App\Models\MajorRecommendation::whereNotNull('description')
        ->where('description', '!=', '')
        ->count();
    echo "Majors with description: {$majorsWithDescription}\n";
    
    // Show some examples
    echo "\n=== Sample Updated Majors ===\n";
    $sampleMajors = \App\Models\MajorRecommendation::whereNotNull('description')
        ->where('description', '!=', '')
        ->take(5)
        ->get();
    
    foreach ($sampleMajors as $major) {
        echo "ID: {$major->id} - {$major->major_name}\n";
        echo "Description: " . substr($major->description, 0, 100) . "...\n\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}

echo "\nScript completed.\n";
