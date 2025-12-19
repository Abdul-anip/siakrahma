<?php
// ===================== KONEKSI DATABASE db_siak =====================
$host = "localhost";
$user = "root";
$pass = "";
$db = "db_siak2";
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// ===================== SESSION START =====================
session_start();
$login_message = ""; 



// Fungsi helper untuk mendapatkan data prodi dalam format JSON
function getProdiData($conn) {
    $prodi_data = [];
    $sql = "SELECT prodiId, prodiJurId, prodiNama, prodiJenjang FROM program_studi WHERE prodiIsAktif = 1 ORDER BY prodiNama ASC";
    $result = $conn->query($sql);
    
    if ($result && $result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $prodi_data[] = [
                'prodiId' => $row['prodiId'],
                'prodiJurId' => $row['prodiJurId'],
                'prodiNama' => $row['prodiNama'],
                'prodiJenjang' => $row['prodiJenjang']
            ];
        }
    }
    
    return $prodi_data;
}


// ===================== FUNGSI SETUP DAN POPULASI DATABASE =====================
function setup_database_for_demo($conn) {
    // 1. Drop Table
    $conn->query("SET FOREIGN_KEY_CHECKS = 0;");
    $tables = [
        'krs_khs', 'kelas_dosen', 'kelas_mahasiswa', 'kelas_matakuliah', 'kelas', 'krs',
        'matakuliah', 'kurikulum', 'mahasiswa', 'dosen', 'program_studi', 'tahun_akademik', 'jurusan', 'user'
    ];
    foreach ($tables as $table) {
        $conn->query("DROP TABLE IF EXISTS `$table`;");
    }
    $conn->query("SET FOREIGN_KEY_CHECKS = 1;");
    
    // 2. Create Table
    $create_sqls = [
        "CREATE TABLE `jurusan` (`jurId` smallint NOT NULL AUTO_INCREMENT, `jurKode` varchar(20) DEFAULT NULL, `jurNama` varchar(100) NOT NULL DEFAULT '', `jurNamaAsing` varchar(100) DEFAULT NULL, `jurIsAktif` tinyint DEFAULT '1', PRIMARY KEY (`jurId`)) ENGINE=InnoDB DEFAULT CHARSET=latin1;",
        "CREATE TABLE `program_studi` (`prodiId` smallint NOT NULL AUTO_INCREMENT, `prodiJurId` smallint DEFAULT NULL, `prodiKode` varchar(20) DEFAULT NULL, `prodiNama` varchar(100) NOT NULL DEFAULT '', `prodiNamaAsing` varchar(100) DEFAULT NULL, `prodiJenjang` varchar(10) DEFAULT NULL, `prodiEmail` varchar(50) DEFAULT NULL, `prodiWebsite` varchar(50) DEFAULT NULL, `prodiIsAktif` tinyint DEFAULT '1', PRIMARY KEY (`prodiId`), CONSTRAINT `prodiJurId_fk` FOREIGN KEY (`prodiJurId`) REFERENCES `jurusan` (`jurId`) ON DELETE RESTRICT ON UPDATE CASCADE) ENGINE=InnoDB DEFAULT CHARSET=latin1;",
        "CREATE TABLE `dosen` (`dsnNidn` char(10) NOT NULL, `dsnJurId` smallint DEFAULT NULL, `dsnProdiId` smallint DEFAULT NULL, `dsnNama` varchar(50) NOT NULL DEFAULT '', `dsnJenisKelaminKode` enum('L','P') DEFAULT 'L', `dsnGelarDepan` varchar(50) DEFAULT NULL, `dsnGelarBelakang` varchar(50) DEFAULT NULL, `dsnFoto` varchar(50) DEFAULT NULL, PRIMARY KEY (`dsnNidn`), CONSTRAINT `dsnJurId_fk` FOREIGN KEY (`dsnJurId`) REFERENCES `jurusan` (`jurId`) ON DELETE RESTRICT ON UPDATE CASCADE, CONSTRAINT `dsnProdiId_fk` FOREIGN KEY (`dsnProdiId`) REFERENCES `program_studi` (`prodiId`) ON DELETE RESTRICT ON UPDATE CASCADE) ENGINE=InnoDB DEFAULT CHARSET=latin1;",
        "CREATE TABLE `mahasiswa` (
            `mhsNim` varchar(10) NOT NULL DEFAULT '', 
            `mhsNama` varchar(50) NOT NULL DEFAULT '', 
            `mhsTempatLahir` varchar(50) DEFAULT NULL, 
            `mhsTglLahir` date DEFAULT NULL, 
            `mhsJenisKelamin` enum('L','P') DEFAULT 'L', 
            `mhsJurId` smallint DEFAULT NULL, 
            `mhsProdiId` smallint DEFAULT NULL, 
            `mhsKodeKelas` enum('A','B','C','D') DEFAULT 'A', 
            `mhsStsAkademik` enum('A','L','C','D','K','M') DEFAULT 'A', 
            `mhsSmsAktif` tinyint DEFAULT '0', 
            `mhsFoto` varchar(50) DEFAULT NULL, 
            `mhsPaId` char(10) DEFAULT NULL, 
            PRIMARY KEY (`mhsNim`), 
            CONSTRAINT `mhsJurId_fk` FOREIGN KEY (`mhsJurId`) REFERENCES `jurusan` (`jurId`) ON DELETE RESTRICT ON UPDATE CASCADE, 
            CONSTRAINT `mhsProdiId_fk` FOREIGN KEY (`mhsProdiId`) REFERENCES `program_studi` (`prodiId`) ON DELETE RESTRICT ON UPDATE CASCADE,
            CONSTRAINT `mhsPaId_fk` FOREIGN KEY (`mhsPaId`) REFERENCES `dosen` (`dsnNidn`) ON DELETE SET NULL ON UPDATE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=latin1;",
        "CREATE TABLE `kurikulum` (`kurId` int NOT NULL AUTO_INCREMENT, `kurProdiId` smallint DEFAULT NULL, `kurTahun` year NOT NULL DEFAULT '0000', `kurNama` varchar(100) NOT NULL DEFAULT '', `kurIsAktif` tinyint DEFAULT '1', PRIMARY KEY (`kurId`), CONSTRAINT `kurProdiId_fk` FOREIGN KEY (`kurProdiId`) REFERENCES `program_studi` (`prodiId`) ON DELETE RESTRICT ON UPDATE CASCADE) ENGINE=InnoDB DEFAULT CHARSET=latin1;",
        "CREATE TABLE `matakuliah` (`mkId` int NOT NULL AUTO_INCREMENT, `mkKurId` int DEFAULT NULL, `mkKode` varchar(20) NOT NULL DEFAULT '', `mkNama` varchar(100) NOT NULL DEFAULT '', `mkNamaAsing` varchar(100) DEFAULT NULL, `mkSemester` tinyint DEFAULT NULL, `mkSks` tinyint NOT NULL DEFAULT '0', `mkIsAktif` tinyint DEFAULT '1', PRIMARY KEY (`mkId`), UNIQUE KEY `mkUniqueKey` (`mkKurId`,`mkKode`), CONSTRAINT `mkKurId_fk` FOREIGN KEY (`mkKurId`) REFERENCES `kurikulum` (`kurId`) ON DELETE RESTRICT ON UPDATE CASCADE) ENGINE=InnoDB DEFAULT CHARSET=latin1;",
        "CREATE TABLE `tahun_akademik` (`thakdId` smallint NOT NULL DEFAULT '0', `thakdTahun` year NOT NULL DEFAULT '0000', `thakdSemester` enum('1','2') NOT NULL DEFAULT '1', `thakdTglMulai` date DEFAULT NULL, `thakdTglSelesai` date DEFAULT NULL, `thakdIsAktif` tinyint NOT NULL DEFAULT '1', PRIMARY KEY (`thakdId`)) ENGINE=InnoDB DEFAULT CHARSET=latin1;",
        "CREATE TABLE `krs` (`krsId` int NOT NULL AUTO_INCREMENT, `krsThakdId` smallint DEFAULT NULL, `krsMhsNim` varchar(10) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL, `krsPADsnNidn` char(10) DEFAULT NULL, `krsTglTerdaftar` date DEFAULT NULL, `krsSemester` tinyint NOT NULL DEFAULT '0', `krsStatusLulus` enum('L','LP','TL','DO','-') DEFAULT '-', PRIMARY KEY (`krsId`), UNIQUE KEY `krsUID` (`krsThakdId`,`krsMhsNim`), CONSTRAINT `krsThakdId_fk` FOREIGN KEY (`krsThakdId`) REFERENCES `tahun_akademik` (`thakdId`) ON DELETE RESTRICT ON UPDATE CASCADE, CONSTRAINT `krsMhsNim_fk` FOREIGN KEY (`krsMhsNim`) REFERENCES `mahasiswa` (`mhsNim`) ON DELETE RESTRICT ON UPDATE CASCADE, CONSTRAINT `krsPADsnNidn_fk` FOREIGN KEY (`krsPADsnNidn`) REFERENCES `dosen` (`dsnNidn`) ON DELETE RESTRICT ON UPDATE CASCADE) ENGINE=InnoDB DEFAULT CHARSET=latin1;",
        "CREATE TABLE `krs_khs` (`khsId` int NOT NULL AUTO_INCREMENT, `khsKrsId` int NOT NULL DEFAULT '0', `khsMkId` int NOT NULL DEFAULT '0', `khsKodeNilai` varchar(3) DEFAULT NULL, `khsBobotNilai` decimal(4,2) NOT NULL DEFAULT '0.00', PRIMARY KEY (`khsId`), UNIQUE KEY `khsUID` (`khsKrsId`,`khsMkId`), CONSTRAINT `khsKrsId_fk` FOREIGN KEY (`khsKrsId`) REFERENCES `krs` (`krsId`) ON DELETE RESTRICT ON UPDATE CASCADE, CONSTRAINT `khsMkId_fk` FOREIGN KEY (`khsMkId`) REFERENCES `matakuliah` (`mkId`) ON DELETE RESTRICT ON UPDATE CASCADE) ENGINE=InnoDB DEFAULT CHARSET=latin1;",
        "CREATE TABLE `kelas` (`klsId` int NOT NULL AUTO_INCREMENT, `klsThakdId` smallint DEFAULT NULL, `klsProdiId` smallint DEFAULT NULL, `klsNama` varchar(30) NOT NULL DEFAULT '', PRIMARY KEY (`klsId`), UNIQUE KEY `klsUID` (`klsThakdId`,`klsProdiId`,`klsNama`), CONSTRAINT `klsThakdId_fk` FOREIGN KEY (`klsThakdId`) REFERENCES `tahun_akademik` (`thakdId`) ON DELETE RESTRICT ON UPDATE CASCADE, CONSTRAINT `klsProdiId_fk` FOREIGN KEY (`klsProdiId`) REFERENCES `program_studi` (`prodiId`) ON DELETE RESTRICT ON UPDATE CASCADE) ENGINE=InnoDB DEFAULT CHARSET=latin1;",
        "CREATE TABLE `kelas_matakuliah` (`klsmkId` int NOT NULL AUTO_INCREMENT, `klsmkKlsId` int DEFAULT NULL, `klsmkMkId` int NOT NULL DEFAULT '0', PRIMARY KEY (`klsmkId`), UNIQUE KEY `klsmkUID` (`klsmkKlsId`,`klsmkMkId`), CONSTRAINT `klsmkKlsId_fk` FOREIGN KEY (`klsmkKlsId`) REFERENCES `kelas` (`klsId`) ON DELETE RESTRICT ON UPDATE CASCADE, CONSTRAINT `klsmkMkId_fk` FOREIGN KEY (`klsmkMkId`) REFERENCES `matakuliah` (`mkId`) ON DELETE RESTRICT ON UPDATE CASCADE) ENGINE=InnoDB DEFAULT CHARSET=latin1;",
        "CREATE TABLE `kelas_mahasiswa` (`klsmhsId` int NOT NULL AUTO_INCREMENT, `klsmhsKlsId` int DEFAULT NULL, `klsmhsMhsNim` varchar(10) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL, `klsmhsIsAktif` tinyint NOT NULL DEFAULT '1' COMMENT '1=Aktif, 2=Non-Aktif', PRIMARY KEY (`klsmhsId`), UNIQUE KEY `klsmhsUID` (`klsmhsKlsId`,`klsmhsMhsNim`), CONSTRAINT `klsmhsKlsId_fk` FOREIGN KEY (`klsmhsKlsId`) REFERENCES `kelas` (`klsId`) ON DELETE RESTRICT ON UPDATE CASCADE, CONSTRAINT `klsmhsMhsNim_fk` FOREIGN KEY (`klsmhsMhsNim`) REFERENCES `mahasiswa` (`mhsNim`) ON DELETE RESTRICT ON UPDATE CASCADE) ENGINE=InnoDB DEFAULT CHARSET=latin1;",
        "CREATE TABLE `kelas_dosen` (`klsdsnId` int NOT NULL AUTO_INCREMENT, `klsdsnKlsId` int DEFAULT NULL, `klsdsnDsnNidn` char(10) DEFAULT NULL, `klsdsnMkId` int NOT NULL DEFAULT '0', `klsdsnIsAktif` tinyint NOT NULL DEFAULT '1' COMMENT '0=Non-Aktif, 1=Aktif', PRIMARY KEY (`klsdsnId`), UNIQUE KEY `klsdsnUID` (`klsdsnKlsId`,`klsdsnDsnNidn`,`klsdsnMkId`), CONSTRAINT `klsdsnKlsId_fk` FOREIGN KEY (`klsdsnKlsId`) REFERENCES `kelas` (`klsId`) ON DELETE RESTRICT ON UPDATE CASCADE, CONSTRAINT `klsdsnDsnNidn_fk` FOREIGN KEY (`klsdsnDsnNidn`) REFERENCES `dosen` (`dsnNidn`) ON DELETE RESTRICT ON UPDATE CASCADE, CONSTRAINT `klsdsnMkId_fk` FOREIGN KEY (`klsdsnMkId`) REFERENCES `matakuliah` (`mkId`) ON DELETE RESTRICT ON UPDATE CASCADE) ENGINE=InnoDB DEFAULT CHARSET=latin1;",
        "CREATE TABLE `user` (`appusrID` varchar(50) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '', `appusrNama` varchar(50) DEFAULT NULL, `appusrPassword` varchar(50) DEFAULT NULL, `appusrGrupUser` varchar(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL, `appusrNoHp` varchar(20) DEFAULT NULL, `appusrIsEnabled` tinyint UNSIGNED NOT NULL DEFAULT '0', PRIMARY KEY (`appusrID`)) ENGINE=InnoDB DEFAULT CHARSET=latin1;"
    ];
    foreach ($create_sqls as $sql) {
        $conn->query($sql);
    }
    
    // 3. Insert Data
    $insert_sqls = [
        // 1. DATA JURUSAN
        "INSERT INTO `jurusan` (`jurId`, `jurKode`, `jurNama`, `jurNamaAsing`, `jurIsAktif`) VALUES (1, 'ME', 'Teknik Mesin', 'Mechanical Engineering', 1), (2, 'SP', 'Teknik Sipil', 'Civil Engineering', 1), (3, 'EE', 'Teknik Elektro', 'Electrical Engineering', 1), (4, 'AN', 'Administrasi Niaga', 'Business Administration', 1), (5, 'AK', 'Akuntansi', 'Accounting', 1), (6, 'TI', 'Teknologi Informasi', 'Information Technology', 1), (7, 'BI', 'Bahasa Inggris', 'English', 1);",
        // 7. DATA TAHUN AKADEMIK
        "INSERT INTO `tahun_akademik` (`thakdId`, `thakdTahun`, `thakdSemester`, `thakdTglMulai`, `thakdTglSelesai`, `thakdIsAktif`) VALUES (20232, 2023, '2', '2024-02-19', '2024-07-20', 0), (20241, 2024, '1', '2024-08-19', '2025-01-06', 1);",
        // 2. DATA PROGRAM STUDI
        "INSERT INTO `program_studi` (`prodiId`, `prodiJurId`, `prodiKode`, `prodiNama`, `prodiNamaAsing`, `prodiJenjang`, `prodiEmail`, `prodiWebsite`, `prodiIsAktif`) VALUES (1, 3, '4EC', 'Teknik Elektronika', 'Electronics Engineering', 'D-4', 'ec@polinpdg.ac.id', 'http://ec.polinpdg.ac.id', 1), (7, 3, '4TT', 'Teknik Telekomunikasi', 'Telecommunication Engineering', 'D-4', 'tt@polinpdg.ac.id', 'http://tt.polinpdg.ac.id', 1);",
        // 5. DATA KURIKULUM
        "INSERT INTO `kurikulum` (`kurId`, `kurProdiId`, `kurTahun`, `kurNama`, `kurIsAktif`) VALUES (1, 7, 2013, 'Kurikulum 2013', 1), (2, 7, 2022, 'Kurikulum 2022', 1);",
        // 6. DATA MATAKULIAH
        "INSERT INTO `matakuliah` (`mkId`, `mkKurId`, `mkKode`, `mkNama`, `mkNamaAsing`, `mkSemester`, `mkSks`, `mkIsAktif`) VALUES (1, 2, 'TCE4106', 'Bahasa Inggris 2', '', 4, 2, 1), (2, 2, 'TCE4218', 'Rangkaian Elektronika Telekomunikasi', '', 4, 3, 1), (3, 2, 'TCE4219', 'Teori dan Perancangan Antena', '', 4, 2, 1);",
        // 3. DATA DOSEN
        "INSERT INTO `dosen` (`dsnNidn`, `dsnJurId`, `dsnProdiId`, `dsnNama`, `dsnJenisKelaminKode`, `dsnGelarDepan`, `dsnGelarBelakang`, `dsnFoto`) VALUES ('0006058102', 3, 7, 'Amelia Yolanda', 'P', '', 'ST., MT', '0006058102.jpg'), ('0006058302', 3, 7, 'Popy Maria', 'P', '', 'ST.,MT', '0006058302.jpg'), ('0007016802', 3, 7, 'Yustini', 'P', '', 'SST., MT', '0007016802.jpg'), ('0022057705', 3, 7, 'Dosen PA Dummy', 'L', '', 'M.Eng', ''), ('0014057908', 3, 7, 'Dosen Kelas 1', 'L', '', 'Dr.', ''), ('0009067701', 3, 7, 'Dosen Kelas 2', 'L', '', 'ST., MT', '');",
        // 4. DATA MAHASISWA
        "INSERT INTO `mahasiswa` (`mhsNim`, `mhsNama`, `mhsTempatLahir`, `mhsTglLahir`, `mhsJenisKelamin`, `mhsJurId`, `mhsProdiId`, `mhsKodeKelas`, `mhsStsAkademik`, `mhsSmsAktif`, `mhsFoto`, `mhsPaId`) VALUES ('2111071006', 'Ferdiansyah', 'Pariaman', '2003-07-29', 'L', 3, 7, 'C', 'A', 5, '2111071006.jpg', '0022057705'), ('2211071001', 'Cindy Juniarti', 'Sikapak', '2004-06-16', 'P', 3, 7, 'A', 'A', 5, '2211071001.jpg', '0022057705'), ('2211071002', 'Dhuha Rais', 'Padang', '2003-08-30', 'L', 3, 7, 'A', 'A', 5, '2211071002.jpg', '0022057705'), ('2211071003', 'Dinda Kurnia Illahi', 'None', '2003-01-01', 'P', 3, 7, 'A', 'A', 5, '2211071003.jpg', '0022057705');",
        // 10. DATA KELAS
        "INSERT INTO `kelas` (`klsId`, `klsThakdId`, `klsProdiId`, `klsNama`) VALUES (1, 20241, 7, 'III.A'), (2, 20241, 7, 'III.B'), (3, 20241, 7, 'III.C');",
        // 11. DATA KELAS MATAKULIAH
        "INSERT INTO `kelas_matakuliah` (`klsmkId`, `klsmkKlsId`, `klsmkMkId`) VALUES (1, 1, 1), (2, 1, 2), (3, 1, 3);",
        // 12. DATA KELAS MAHASISWA
        "INSERT INTO `kelas_mahasiswa` (`klsmhsId`, `klsmhsKlsId`, `klsmhsMhsNim`, `klsmhsIsAktif`) VALUES (1, 1, '2211071001', 1), (2, 1, '2211071002', 1);",
        // 13. DATA KELAS DOSEN
        "INSERT INTO `kelas_dosen` (`klsdsnId`, `klsdsnKlsId`, `klsdsnDsnNidn`, `klsdsnMkId`, `klsdsnIsAktif`) VALUES (1, 1, '0014057908', 1, 1), (2, 1, '0009067701', 2, 1), (3, 1, '0006058102', 3, 1);",
        // 8. DATA KRS
        "INSERT INTO `krs` (`krsId`, `krsThakdId`, `krsMhsNim`, `krsPADsnNidn`, `krsTglTerdaftar`, `krsSemester`, `krsStatusLulus`) VALUES (1, 20241, '2211071001', '0022057705', '2024-08-09', 5, '-'), (2, 20241, '2211071002', '0022057705', '2024-08-09', 5, '-');",
        // 9. DATA KRS KHS
        "INSERT INTO `krs_khs` (`khsId`, `khsKrsId`, `khsMkId`, `khsKodeNilai`, `khsBobotNilai`) VALUES (1, 1, 1, NULL, '0.00'), (2, 1, 2, NULL, '0.00'), (3, 2, 3, NULL, '0.00');",
        // Data User Admin (jika belum ada)
        "INSERT INTO `user` (`appusrID`, `appusrNama`, `appusrPassword`, `appusrGrupUser`, `appusrNoHp`, `appusrIsEnabled`) 
         VALUES ('ADMIN', 'RAHMA', '123', 'Admin', '081122334455', 1) ON DUPLICATE KEY UPDATE appusrNama='Administrator';"
    ];
    foreach ($insert_sqls as $sql) {
        $conn->query($sql);
    }
    // Memberi pesan bahwa setup selesai
    if (isset($_GET['setup_done'])) return;
    if ($conn->query("SELECT 1 FROM jurusan LIMIT 1;")) {
        // Redirect ke halaman login setelah setup selesai
        header("Location: ?modul=login&setup_done=1&status=success&msg=" . urlencode("Setup Database Demo Berhasil! Silakan login"));
        exit();
    }
}
// Cek apakah tabel Jurusan sudah ada, jika belum, jalankan
if (!$conn->query("SELECT 1 FROM jurusan LIMIT 1;")) {
    setup_database_for_demo($conn);
}
// ===================== LOGIKA PEMROSESAN FORM INSERT/UPDATE (CRUD) =====================
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['form_action'])) {
    // --- Proses Tambah Jurusan ---
    if ($_POST['form_action'] == 'add_jurusan') {
        $jur_kode = $conn->real_escape_string($_POST['jur_kode']);
        $jur_nama = $conn->real_escape_string($_POST['jur_nama']);
        $jur_nama_asing = $conn->real_escape_string($_POST['jur_nama_asing']);
        $jur_is_aktif = isset($_POST['jur_is_aktif']) ? 1 : 0;
        $insert_sql = "INSERT INTO jurusan (jurKode, jurNama, jurNamaAsing, jurIsAktif)
            VALUES ('$jur_kode', '$jur_nama', '$jur_nama_asing', $jur_is_aktif)";
        if ($conn->query($insert_sql) === TRUE) {
            $message = "   ✅    Data Jurusan $jur_nama berhasil ditambahkan!";
            header("Location: ?modul=datamaster&submenu=jurusan&status=success&msg=" . urlencode($message));
            exit();
        } else {
            if ($conn->errno == 1062) {
                $error_msg = "Kode Jurusan $jur_kode sudah ada. Gagal menambahkan data.";
            } else {
                $error_msg = "   ❌    Gagal menambahkan data Jurusan: " . $conn->error;
            }
            header("Location: ?modul=datamaster&submenu=jurusan&action=add&status=error&msg=" . urlencode($error_msg) . "&jur_kode=$jur_kode&jur_nama=$jur_nama");
            exit();
        }
    }

    
    // --- Proses Ubah Jurusan ---
    if ($_POST['form_action'] == 'edit_jurusan') {
        $jur_id = $conn->real_escape_string($_POST['jur_id']);
        $jur_kode = $conn->real_escape_string($_POST['jur_kode']);
        $jur_nama = $conn->real_escape_string($_POST['jur_nama']);
        $jur_nama_asing = $conn->real_escape_string($_POST['jur_nama_asing']);
        $jur_is_aktif = isset($_POST['jur_is_aktif']) ? 1 : 0;

        $update_sql = "UPDATE jurusan SET 
            jurKode = '$jur_kode', 
            jurNama = '$jur_nama', 
            jurNamaAsing = '$jur_nama_asing', 
            jurIsAktif = $jur_is_aktif 
            WHERE jurId = '$jur_id'";

        if ($conn->query($update_sql) === TRUE) {
            header("Location: ?modul=datamaster&submenu=jurusan&status=success&msg=" . urlencode("Data Jurusan $jur_nama berhasil diperbarui!"));
            exit();
        } else {
            header("Location: ?modul=datamaster&submenu=jurusan&action=edit&id=$jur_id&status=error&msg=" . urlencode("Gagal memperbarui data: " . $conn->error));
            exit();
        }
    }

    // --- Proses Tambah Kelas ---
    if ($_POST['form_action'] == 'add_kelas') {
        $kls_nama = $conn->real_escape_string($_POST['kls_nama']);
        $kls_prodi_id = $conn->real_escape_string($_POST['kls_prodi_id']);
        $kls_thakd_id = $conn->real_escape_string($_POST['kls_thakd_id']);
        
        $insert_sql = "INSERT INTO kelas (klsNama, klsProdiId, klsThakdId)
            VALUES ('$kls_nama', $kls_prodi_id, '$kls_thakd_id')";
        if ($conn->query($insert_sql) === TRUE) {
            $message = "   ✅    Data Kelas **$kls_nama** berhasil ditambahkan!";
            header("Location: ?modul=prakuliah&submenu=kelas&status=success&msg=" . urlencode($message));
            exit();
        } else {
            $error_msg = "   ❌    Gagal menambahkan data Kelas: " . $conn->error;
            if ($conn->errno == 1062) {
                $error_msg = "Nama Kelas **$kls_nama** sudah ada untuk Prodi dan Tahun Akademik tersebut. Gagal menambahkan data.";
            }
            header("Location: ?modul=prakuliah&submenu=kelas&action=add&status=error&msg=" . urlencode($error_msg) . "&kls_nama=$kls_nama");
            exit();
        }
    }

    //tambah prodi//
    if ($_POST['form_action'] == 'add_prodi') {
    $prodi_jur_id = $conn->real_escape_string($_POST['prodi_jur_id']);
    $prodi_kode = $conn->real_escape_string($_POST['prodi_kode']);
    $prodi_nama = $conn->real_escape_string($_POST['prodi_nama']);
    $prodi_nama_asing = $conn->real_escape_string($_POST['prodi_nama_asing']);
    $prodi_jenjang = $conn->real_escape_string($_POST['prodi_jenjang']);
    $prodi_email = $conn->real_escape_string($_POST['prodi_email']);
    $prodi_website = $conn->real_escape_string($_POST['prodi_website']);
    $prodi_is_aktif = isset($_POST['prodi_is_aktif']) ? 1 : 0;
    
    $insert_sql = "INSERT INTO program_studi (prodiJurId, prodiKode, prodiNama, prodiNamaAsing, prodiJenjang, prodiEmail, prodiWebsite, prodiIsAktif)
        VALUES ('$prodi_jur_id', '$prodi_kode', '$prodi_nama', '$prodi_nama_asing', '$prodi_jenjang', '$prodi_email', '$prodi_website', $prodi_is_aktif)";
    
    if ($conn->query($insert_sql) === TRUE) {
        $message = "   ✅    Data Program Studi $prodi_nama berhasil ditambahkan!";
        header("Location: ?modul=datamaster&submenu=prodi&status=success&msg=" . urlencode($message));
        exit();
    } else {
        if ($conn->errno == 1062) {
            $error_msg = "Kode Program Studi $prodi_kode sudah ada. Gagal menambahkan data.";
        } else {
            $error_msg = "   ❌    Gagal menambahkan data Program Studi: " . $conn->error;
        }
        header("Location: ?modul=datamaster&submenu=prodi&action=add&status=error&msg=" . urlencode($error_msg) . "&prodi_kode=$prodi_kode&prodi_nama=$prodi_nama");
        exit();
    }
}


// --- Proses Ubah Program Studi ---
if ($_POST['form_action'] == 'edit_prodi') {
    $prodi_id = $conn->real_escape_string($_POST['prodi_id']);
    $prodi_jur_id = $conn->real_escape_string($_POST['prodi_jur_id']);
    $prodi_kode = $conn->real_escape_string($_POST['prodi_kode']);
    $prodi_nama = $conn->real_escape_string($_POST['prodi_nama']);
    $prodi_nama_asing = $conn->real_escape_string($_POST['prodi_nama_asing']);
    $prodi_jenjang = $conn->real_escape_string($_POST['prodi_jenjang']);
    $prodi_email = $conn->real_escape_string($_POST['prodi_email']);
    $prodi_website = $conn->real_escape_string($_POST['prodi_website']);
    $prodi_is_aktif = isset($_POST['prodi_is_aktif']) ? 1 : 0;

    $update_sql = "UPDATE program_studi SET 
        prodiJurId = '$prodi_jur_id', 
        prodiKode = '$prodi_kode', 
        prodiNama = '$prodi_nama', 
        prodiNamaAsing = '$prodi_nama_asing', 
        prodiJenjang = '$prodi_jenjang', 
        prodiEmail = '$prodi_email', 
        prodiWebsite = '$prodi_website', 
        prodiIsAktif = $prodi_is_aktif 
        WHERE prodiId = '$prodi_id'";

    if ($conn->query($update_sql) === TRUE) {
        header("Location: ?modul=datamaster&submenu=prodi&status=success&msg=" . urlencode("Data Prodi $prodi_nama berhasil diperbarui!"));
        exit();
    } else {
        header("Location: ?modul=datamaster&submenu=prodi&action=edit&id=$prodi_id&status=error&msg=" . urlencode("Gagal memperbarui data: " . $conn->error));
        exit();
    }
}


// --- Proses Tambah Mahasiswa ---
if ($_POST['form_action'] == 'add_mahasiswa') {
    $mhs_nim = $conn->real_escape_string($_POST['mhs_nim']);
    $mhs_nama = $conn->real_escape_string($_POST['mhs_nama']);
    $mhs_tempat_lahir = $conn->real_escape_string($_POST['mhs_tempat_lahir']);
    $mhs_tgl_lahir = $conn->real_escape_string($_POST['mhs_tgl_lahir']);
    $mhs_jenis_kelamin = $conn->real_escape_string($_POST['mhs_jenis_kelamin']);
    $mhs_jur_id = $conn->real_escape_string($_POST['mhs_jur_id']);
    $mhs_prodi_id = $conn->real_escape_string($_POST['mhs_prodi_id']);
    $mhs_kode_kelas = $conn->real_escape_string($_POST['mhs_kode_kelas']);
    $mhs_sts_akademik = $conn->real_escape_string($_POST['mhs_sts_akademik']);
    $mhs_sms_aktif = $conn->real_escape_string($_POST['mhs_sms_aktif']);
    $mhs_pa_id = !empty($_POST['mhs_pa_id']) ? "'" . $conn->real_escape_string($_POST['mhs_pa_id']) . "'" : "NULL";
    
    // Cek apakah NIM sudah ada
    $check_sql = "SELECT mhsNim FROM mahasiswa WHERE mhsNim = '$mhs_nim'";
    $check_result = $conn->query($check_sql);
    
    if ($check_result && $check_result->num_rows > 0) {
        $error_msg = "NIM $mhs_nim sudah terdaftar. Silakan gunakan NIM yang berbeda.";
        header("Location: ?modul=datamaster&submenu=mahasiswa&action=add&status=error&msg=" . urlencode($error_msg) . "&mhs_nim=$mhs_nim&mhs_nama=" . urlencode($mhs_nama));
        exit();
    }
    
    $insert_sql = "INSERT INTO mahasiswa (mhsNim, mhsNama, mhsTempatLahir, mhsTglLahir, mhsJenisKelamin, mhsJurId, mhsProdiId, mhsKodeKelas, mhsStsAkademik, mhsSmsAktif, mhsFoto, mhsPaId)
        VALUES ('$mhs_nim', '$mhs_nama', '$mhs_tempat_lahir', '$mhs_tgl_lahir', '$mhs_jenis_kelamin', '$mhs_jur_id', '$mhs_prodi_id', '$mhs_kode_kelas', '$mhs_sts_akademik', '$mhs_sms_aktif', '', $mhs_pa_id)";
    
    if ($conn->query($insert_sql) === TRUE) {
        $message = "   ✅    Data Mahasiswa $mhs_nama (NIM: $mhs_nim) berhasil ditambahkan!";
        header("Location: ?modul=datamaster&submenu=mahasiswa&status=success&msg=" . urlencode($message));
        exit();
    } else {
        $error_msg = "   ❌    Gagal menambahkan data Mahasiswa: " . $conn->error;
        header("Location: ?modul=datamaster&submenu=mahasiswa&action=add&status=error&msg=" . urlencode($error_msg) . "&mhs_nim=$mhs_nim&mhs_nama=" . urlencode($mhs_nama));
        exit();
    }
}

if ($_POST['form_action'] == 'edit_mahasiswa') {
    $nim = $conn->real_escape_string($_POST['mhs_nim']);
    $nama = $conn->real_escape_string($_POST['mhs_nama']);
    $tempat_lahir = $conn->real_escape_string($_POST['mhs_tempat_lahir']);
    $tgl_lahir = $conn->real_escape_string($_POST['mhs_tgl_lahir']);
    $jk = $conn->real_escape_string($_POST['mhs_jenis_kelamin']);
    $jur = $conn->real_escape_string($_POST['mhs_jur_id']);
    $prodi = $conn->real_escape_string($_POST['mhs_prodi_id']);
    $kelas_kode = $conn->real_escape_string($_POST['mhs_kode_kelas']);
    $sts = $conn->real_escape_string($_POST['mhs_sts_akademik']);
    $sms_aktif = $conn->real_escape_string($_POST['mhs_sms_aktif']);
    $pa_id = !empty($_POST['mhs_pa_id']) ? "'" . $conn->real_escape_string($_POST['mhs_pa_id']) . "'" : "NULL";

    $sql = "UPDATE mahasiswa SET 
            mhsNama='$nama', 
            mhsTempatLahir='$tempat_lahir', 
            mhsTglLahir='$tgl_lahir', 
            mhsJenisKelamin='$jk', 
            mhsJurId='$jur', 
            mhsProdiId='$prodi', 
            mhsKodeKelas='$kelas_kode', 
            mhsStsAkademik='$sts', 
            mhsSmsAktif='$sms_aktif', 
            mhsPaId=$pa_id 
            WHERE mhsNim='$nim'";

    if ($conn->query($sql) === TRUE) {
        header("Location: ?modul=datamaster&submenu=mahasiswa&status=success&msg=" . urlencode("Data Mahasiswa berhasil diperbarui!")); 
        exit();
    } else {
        header("Location: ?modul=datamaster&submenu=mahasiswa&action=edit&id=$nim&status=error&msg=" . urlencode("Gagal edit: " . $conn->error)); 
        exit();
    }
}


// --- Proses Tambah Kurikulum ---
if ($_POST['form_action'] == 'add_kurikulum') {
    $kur_prodi_id = $conn->real_escape_string($_POST['kur_prodi_id']);
    $kur_tahun = $conn->real_escape_string($_POST['kur_tahun']);
    $kur_nama = $conn->real_escape_string($_POST['kur_nama']);
    $kur_is_aktif = isset($_POST['kur_is_aktif']) ? 1 : 0;
    
    // Validasi Tahun (harus 4 digit)
    if (!preg_match('/^[0-9]{4}$/', $kur_tahun)) {
        $error_msg = "Tahun harus terdiri dari 4 digit angka!";
        header("Location: ?modul=datamaster&submenu=kurikulum&action=add&status=error&msg=" . urlencode($error_msg) . "&kur_tahun=$kur_tahun&kur_nama=" . urlencode($kur_nama));
        exit();
    }
    
    // Validasi Nama tidak boleh kosong
    if (empty($kur_nama)) {
        $error_msg = "Nama Kurikulum tidak boleh kosong!";
        header("Location: ?modul=datamaster&submenu=kurikulum&action=add&status=error&msg=" . urlencode($error_msg) . "&kur_tahun=$kur_tahun");
        exit();
    }
    
    // Cek apakah kurikulum dengan nama yang sama sudah ada untuk prodi tersebut
    $check_sql = "SELECT kurId FROM kurikulum WHERE kurProdiId = '$kur_prodi_id' AND kurNama = '$kur_nama'";
    $check_result = $conn->query($check_sql);
    
    if ($check_result && $check_result->num_rows > 0) {
        $error_msg = "Kurikulum dengan nama '$kur_nama' sudah ada untuk Program Studi ini!";
        header("Location: ?modul=datamaster&submenu=kurikulum&action=add&status=error&msg=" . urlencode($error_msg) . "&kur_tahun=$kur_tahun&kur_nama=" . urlencode($kur_nama));
        exit();
    }
    
    $insert_sql = "INSERT INTO kurikulum (kurProdiId, kurTahun, kurNama, kurIsAktif)
        VALUES ('$kur_prodi_id', '$kur_tahun', '$kur_nama', $kur_is_aktif)";
    
    if ($conn->query($insert_sql) === TRUE) {
        $message = "✅ Data Kurikulum '$kur_nama' berhasil ditambahkan!";
        header("Location: ?modul=datamaster&submenu=kurikulum&status=success&msg=" . urlencode($message));
        exit();
    } else {
        $error_msg = "❌ Gagal menambahkan data Kurikulum: " . $conn->error;
        header("Location: ?modul=datamaster&submenu=kurikulum&action=add&status=error&msg=" . urlencode($error_msg) . "&kur_tahun=$kur_tahun&kur_nama=" . urlencode($kur_nama));
        exit();
    }
}

if ($_POST['form_action'] == 'edit_kurikulum') {
        $id = $conn->real_escape_string($_POST['kur_id']);
        $prodi_id = $conn->real_escape_string($_POST['kur_prodi_id']);
        $tahun = $conn->real_escape_string($_POST['kur_tahun']);
        $nama = $conn->real_escape_string($_POST['kur_nama']);
        $is_aktif = isset($_POST['kur_is_aktif']) ? 1 : 0;
        $sql = "UPDATE kurikulum SET kurProdiId='$prodi_id', kurTahun='$tahun', kurNama='$nama', kurIsAktif='$is_aktif' WHERE kurId='$id'";
        if ($conn->query($sql) === TRUE) {
            header("Location: ?modul=datamaster&submenu=kurikulum&status=success&msg=" . urlencode("Data Kurikulum berhasil diperbarui!")); exit();
        } else {
            header("Location: ?modul=datamaster&submenu=kurikulum&action=edit&id=$id&status=error&msg=" . urlencode("Gagal memperbarui data: " . $conn->error)); exit();
        }
    }


// --- Proses Tambah Matakuliah ---
if ($_POST['form_action'] == 'add_matakuliah') {
    $mk_kur_id = $conn->real_escape_string($_POST['mk_kur_id']);
    $mk_kode = $conn->real_escape_string($_POST['mk_kode']);
    $mk_nama = $conn->real_escape_string($_POST['mk_nama']);
    $mk_nama_asing = $conn->real_escape_string($_POST['mk_nama_asing']);
    $mk_semester = $conn->real_escape_string($_POST['mk_semester']);
    $mk_sks = $conn->real_escape_string($_POST['mk_sks']);
    $mk_is_aktif = isset($_POST['mk_is_aktif']) ? 1 : 0;
    
    // Validasi Kode Matakuliah (minimal 3 karakter)
    if (strlen($mk_kode) < 3) {
        $error_msg = "Kode Matakuliah minimal 3 karakter!";
        header("Location: ?modul=datamaster&submenu=matkul&action=add&status=error&msg=" . urlencode($error_msg) . "&mk_kode=$mk_kode&mk_nama=" . urlencode($mk_nama));
        exit();
    }
    
    // Validasi Nama tidak boleh kosong
    if (empty($mk_nama)) {
        $error_msg = "Nama Matakuliah tidak boleh kosong!";
        header("Location: ?modul=datamaster&submenu=matkul&action=add&status=error&msg=" . urlencode($error_msg) . "&mk_kode=$mk_kode");
        exit();
    }
    
    // Validasi Semester (1-8)
    if (!is_numeric($mk_semester) || $mk_semester < 1 || $mk_semester > 8) {
        $error_msg = "Semester harus antara 1-8!";
        header("Location: ?modul=datamaster&submenu=matkul&action=add&status=error&msg=" . urlencode($error_msg) . "&mk_kode=$mk_kode&mk_nama=" . urlencode($mk_nama));
        exit();
    }
    
    // Validasi SKS (1-6)
    if (!is_numeric($mk_sks) || $mk_sks < 1 || $mk_sks > 6) {
        $error_msg = "SKS harus antara 1-6!";
        header("Location: ?modul=datamaster&submenu=matkul&action=add&status=error&msg=" . urlencode($error_msg) . "&mk_kode=$mk_kode&mk_nama=" . urlencode($mk_nama));
        exit();
    }
    
    // Cek apakah Kode MK sudah ada untuk kurikulum yang sama
    $check_sql = "SELECT mkId FROM matakuliah WHERE mkKurId = '$mk_kur_id' AND mkKode = '$mk_kode'";
    $check_result = $conn->query($check_sql);
    
    if ($check_result && $check_result->num_rows > 0) {
        $error_msg = "Kode Matakuliah '$mk_kode' sudah ada untuk Kurikulum ini!";
        header("Location: ?modul=datamaster&submenu=matkul&action=add&status=error&msg=" . urlencode($error_msg) . "&mk_kode=$mk_kode&mk_nama=" . urlencode($mk_nama));
        exit();
    }
    
    $insert_sql = "INSERT INTO matakuliah (mkKurId, mkKode, mkNama, mkNamaAsing, mkSemester, mkSks, mkIsAktif)
        VALUES ('$mk_kur_id', '$mk_kode', '$mk_nama', '$mk_nama_asing', '$mk_semester', '$mk_sks', $mk_is_aktif)";
    
    if ($conn->query($insert_sql) === TRUE) {
        $message = "✅ Data Matakuliah '$mk_nama' (Kode: $mk_kode) berhasil ditambahkan!";
        header("Location: ?modul=datamaster&submenu=matkul&status=success&msg=" . urlencode($message));
        exit();
    } else {
        if ($conn->errno == 1062) {
            $error_msg = "Kode Matakuliah '$mk_kode' sudah ada untuk Kurikulum ini!";
        } else {
            $error_msg = "❌ Gagal menambahkan data Matakuliah: " . $conn->error;
        }
        header("Location: ?modul=datamaster&submenu=matkul&action=add&status=error&msg=" . urlencode($error_msg) . "&mk_kode=$mk_kode&mk_nama=" . urlencode($mk_nama));
        exit();
    }
}

if ($_POST['form_action'] == 'edit_matakuliah') {
        $id = $conn->real_escape_string($_POST['mk_id']);
        $kur_id = $conn->real_escape_string($_POST['mk_kur_id']);
        $kode = $conn->real_escape_string($_POST['mk_kode']);
        $nama = $conn->real_escape_string($_POST['mk_nama']);
        $smt = $conn->real_escape_string($_POST['mk_semester']);
        $sks = $conn->real_escape_string($_POST['mk_sks']);
        $is_aktif = isset($_POST['mk_is_aktif']) ? 1 : 0;
        $sql = "UPDATE matakuliah SET mkKurId='$kur_id', mkKode='$kode', mkNama='$nama', mkSemester='$smt', mkSks='$sks', mkIsAktif='$is_aktif' WHERE mkId='$id'";
        if ($conn->query($sql) === TRUE) {
            header("Location: ?modul=datamaster&submenu=matkul&status=success&msg=" . urlencode("Data Matakuliah berhasil diperbarui!")); exit();
        } else {
            header("Location: ?modul=datamaster&submenu=matkul&action=edit&id=$id&status=error&msg=" . urlencode("Gagal memperbarui data: " . $conn->error)); exit();
        }
    }



    // --- Proses Ubah Password ---
    if ($_POST['form_action'] == 'ubah_password') {
        $user_id = $_SESSION['appusrID'];
        $password_lama = $conn->real_escape_string($_POST['password_lama']);
        $password_baru = $conn->real_escape_string($_POST['password_baru']);
        $konfirmasi_password = $conn->real_escape_string($_POST['konfirmasi_password']);
        
        // Validasi password baru dan konfirmasi
        if ($password_baru !== $konfirmasi_password) {
            $error_msg = "Password baru dan konfirmasi password tidak cocok!";
            header("Location: ?modul=user&submenu=ubah_password&status=error&msg=" . urlencode($error_msg));
            exit();
        }
        
        // Validasi panjang password
        if (strlen($password_baru) < 3) {
            $error_msg = "Password baru minimal 3 karakter!";
            header("Location: ?modul=user&submenu=ubah_password&status=error&msg=" . urlencode($error_msg));
            exit();
        }
        
        // Cek password lama
        $sql_check = "SELECT appusrPassword FROM user WHERE appusrID = '$user_id'";
        $result_check = $conn->query($sql_check);
        
        if ($result_check && $result_check->num_rows == 1) {
            $user_data = $result_check->fetch_assoc();
            
            if ($password_lama === $user_data['appusrPassword']) {
                // Password lama benar, update password baru
                $update_sql = "UPDATE user SET appusrPassword = '$password_baru' WHERE appusrID = '$user_id'";
                
                if ($conn->query($update_sql) === TRUE) {
                    $success_msg = "✅ Password berhasil diubah!";
                    header("Location: ?modul=user&submenu=ubah_password&status=success&msg=" . urlencode($success_msg));
                    exit();
                } else {
                    $error_msg = "❌ Gagal mengubah password: " . $conn->error;
                    header("Location: ?modul=user&submenu=ubah_password&status=error&msg=" . urlencode($error_msg));
                    exit();
                }
            } else {
                $error_msg = "❌ Password lama tidak sesuai!";
                header("Location: ?modul=user&submenu=ubah_password&status=error&msg=" . urlencode($error_msg));
                exit();
            }
        } else {
            $error_msg = "❌ User tidak ditemukan!";
            header("Location: ?modul=user&submenu=ubah_password&status=error&msg=" . urlencode($error_msg));
            exit();
        }
    }


    // ===================== LOGIKA PENGHAPUSAN (DELETE) =====================
    if ($_POST['form_action'] == 'edit_dosen') {
        $nidn = $conn->real_escape_string($_POST['dsn_nidn']);
        $nama = $conn->real_escape_string($_POST['dsn_nama']);
        $jk = $conn->real_escape_string($_POST['dsn_jenis_kelamin']);
        $prodi = $conn->real_escape_string($_POST['dsn_prodi_id']);
        $jur = $conn->real_escape_string($_POST['dsn_jur_id']);
        $sql = "UPDATE dosen SET dsnNama='$nama', dsnJenisKelaminKode='$jk', dsnProdiId='$prodi', dsnJurId='$jur' WHERE dsnNidn='$nidn'";
        if ($conn->query($sql) === TRUE) {
            header("Location: ?modul=datamaster&submenu=dosen&status=success&msg=" . urlencode("Data Dosen berhasil diperbarui!")); exit();
        } else {
             header("Location: ?modul=datamaster&submenu=dosen&action=edit&id=$nidn&status=error&msg=" . urlencode("Gagal memperbarui data: " . $conn->error)); exit();
        }
    }

} // End of POST block

// ===================== LOGIKA PENGHAPUSAN (DELETE) =====================
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $id = $conn->real_escape_string($_GET['id']);
    $mod = $_GET['modul'];
    $sub = $_GET['submenu'];
    $table = ""; $pk = "";
    
    if ($sub == 'jurusan') { $table = "jurusan"; $pk = "jurId"; }
    elseif ($sub == 'prodi') { $table = "program_studi"; $pk = "prodiId"; }
    elseif ($sub == 'kelas') { $table = "kelas"; $pk = "klsId"; }
    elseif ($sub == 'kurikulum') { $table = "kurikulum"; $pk = "kurId"; }
    elseif ($sub == 'matkul') { $table = "matakuliah"; $pk = "mkId"; }
    elseif ($sub == 'dosen') { $table = "dosen"; $pk = "dsnNidn"; $id = "'$id'"; }
    elseif ($sub == 'mahasiswa') { $table = "mahasiswa"; $pk = "mhsNim"; $id = "'$id'"; }

    if ($table != "") {
        $conn->query("SET FOREIGN_KEY_CHECKS = 0;");
        $sql = "DELETE FROM $table WHERE $pk = $id";
        if ($conn->query($sql)) {
            $conn->query("SET FOREIGN_KEY_CHECKS = 1;");
            header("Location: ?modul=$mod&submenu=$sub&status=success&msg=" . urlencode("Data berhasil dihapus!")); exit();
        } else {
             $conn->query("SET FOREIGN_KEY_CHECKS = 1;");
             header("Location: ?modul=$mod&submenu=$sub&status=error&msg=" . urlencode("Gagal hapus: " . $conn->error)); exit();
        }
    }
}
// ===================== PHP Setup & Penentuan Konten =====================
// session_start() moved to top
// $login_message declared at top


// --- LOGIKA OTENTIKASI (LOGIN) ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    $input_id = $conn->real_escape_string($_POST['username']);
    $input_password = $_POST['password']; 

    $sql = "SELECT appusrID, appusrNama, appusrPassword, appusrGrupUser, appusrIsEnabled FROM user WHERE appusrID = '$input_id'";
    $result = $conn->query($sql);

    if ($result && $result->num_rows == 1) {
        $user_data = $result->fetch_assoc();
        
        if ($input_password === $user_data['appusrPassword'] && $user_data['appusrIsEnabled'] == 1) { 
            // Login Berhasil! Set Sesi
            $_SESSION['appusrID'] = $user_data['appusrID'];
            $_SESSION['appusrNama'] = $user_data['appusrNama'];
            $_SESSION['appusrGrupUser'] = $user_data['appusrGrupUser'];
            
            // Redirect ke beranda
            header("Location: ?modul=beranda&status=success&msg=" . urlencode("Selamat datang, " . $user_data['appusrNama'] . "!"));
            exit();
        } else {
             $login_message = "❌ User ID atau Password salah, atau akun dinonaktifkan.";
        }
    } else {
        $login_message = "❌ User ID atau Password salah.";
    }
}

// --- LOGIKA REGISTER ---
// --- LOGIKA REGISTER ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register'])) {
    $reg_username = $conn->real_escape_string($_POST['reg_username']);
    $reg_nama = $conn->real_escape_string($_POST['reg_nama']);
    $reg_password = $_POST['reg_password'];
    $reg_password_confirm = $_POST['reg_password_confirm'];
    
    // Validasi input
    if (empty($reg_username) || empty($reg_nama) || empty($reg_password)) {
        $login_message = "❌ Semua field harus diisi!";
    } else if ($reg_password !== $reg_password_confirm) {
        $login_message = "❌ Password dan Konfirmasi Password tidak cocok!";
    } else {
        // Cek username sudah ada
        $check_sql = "SELECT appusrID FROM user WHERE appusrID = '$reg_username'";
        if ($conn->query($check_sql)->num_rows > 0) {
            $login_message = "❌ Username '$reg_username' sudah terdaftar!";
        } else {
            // Tentukan Role berdasarkan format ID (Sederhana: NIDN biasanya 10 digit angka, NIM juga angka)
            // Validasi: Username harus angka
            if (!is_numeric($reg_username)) {
                $login_message = "❌ Username (NIM/NIDN) harus berupa angka!";
            } else {
                $role = 'Mahasiswa'; // Default
                $final_nama = $reg_nama; // Default pake input user
                
                // 1. Cek di tabel Dosen
                $check_dosen = $conn->query("SELECT dsnNama FROM dosen WHERE dsnNidn = '$reg_username'");
                if ($check_dosen && $check_dosen->num_rows > 0) {
                    $role = 'Dosen';
                    $data_dosen = $check_dosen->fetch_assoc();
                    $final_nama = $data_dosen['dsnNama']; // Sync nama dari master dosen
                } else {
                    // 2. Cek di tabel Mahasiswa
                    $check_mhs = $conn->query("SELECT mhsNama FROM mahasiswa WHERE mhsNim = '$reg_username'");
                    if ($check_mhs && $check_mhs->num_rows > 0) {
                        $role = 'Mahasiswa';
                        $data_mhs = $check_mhs->fetch_assoc();
                        $final_nama = $data_mhs['mhsNama']; // Sync nama dari master mahasiswa
                    }
                }

                // Sanitasi data sebelum insert (PENTING: Terutama jika data diambil dari DB master yang mungkin mengandung kutip)
                $final_nama = $conn->real_escape_string($final_nama);
                $reg_password = $conn->real_escape_string($reg_password);

                // Sanitasi data sebelum insert
                $final_nama = $conn->real_escape_string($final_nama);
                $reg_password = $conn->real_escape_string($reg_password);
                $reg_nohp = $conn->real_escape_string($_POST['reg_nohp']);

                // Insert User Baru
                $insert_sql = "INSERT INTO user (appusrID, appusrNama, appusrPassword, appusrGrupUser, appusrNoHp, appusrIsEnabled) 
                               VALUES ('$reg_username', '$final_nama', '$reg_password', '$role', '$reg_nohp', 1)";
            
            if ($conn->query($insert_sql) === TRUE) {
                // Opsional: Jika Mahasiswa, update nama di tabel mahasiswa jika belum ada nama yang benar
                // Atau biarkan sinkronisasi manual. Di sini kita fokus create akun user.
                
                header("Location: ?modul=login&status=success&msg=" . urlencode("Registrasi berhasil sebagai $role! Silakan login."));
                exit();
            } else {
                $login_message = "❌ Gagal registrasi: " . $conn->error;
            }
        }
        }
    }
}

// --- LOGIKA LOGOUT ---
if (isset($_GET['modul']) && $_GET['modul'] == 'logout') {
    session_unset();
    session_destroy();
    // Redirect ke halaman login setelah logout
    header("Location: ?modul=login&status=info&msg=" . urlencode("Anda telah berhasil logout."));
    exit();
}



// --- PENGENDALIAN AKSES ---
$modul = isset($_GET['modul']) ? $_GET['modul'] : 'beranda';
$submenu = isset($_GET['submenu']) ? $_GET['submenu'] : null;
$action = isset($_GET['action']) ? $_GET['action'] : 'view';
$id = isset($_GET['id']) ? $_GET['id'] : null;


if (!isset($_SESSION['appusrID'])) {
  
    if ($modul !== 'login' && $modul !== 'register') {
        $modul = 'login';
    }
    $nama_user = ""; 
} else {
    // Jika sudah login, ambil data dari sesi
    $nama_user = $_SESSION['appusrNama']; 
}
$periode = "TA 2024/2025 - Ganjil";

// Ambil nilai filter
if ($modul !== 'login') {
    $filter_tahun = isset($_GET['filter_tahun']) ? $conn->real_escape_string($_GET['filter_tahun']) : '';
    $filter_semester_id = isset($_GET['filter_semester_id']) ? $conn->real_escape_string($_GET['filter_semester_id']) : '';
    $filter_prodi = isset($_GET['filter_prodi']) ? $conn->real_escape_string($_GET['filter_prodi']) : '';
    // Nama Kelas ID harus berupa integer, default 0
    $filter_kelas = isset($_GET['filter_kelas']) && is_numeric($_GET['filter_kelas']) ? (int)$_GET['filter_kelas'] : 0;
    $kls_id_selected = $filter_kelas;
    // --- Flag untuk mendeteksi apakah tombol Cari telah diklik ---
    $is_filtered_search = (isset($_GET['filter_tahun']) || isset($_GET['filter_semester_id']) || isset($_GET['filter_prodi']) || isset($_GET['filter_kelas']));
} else {
    $kls_id_selected = 0;
    $is_filtered_search = false;
}

// Ambil pesan
$status_message = isset($_GET['msg']) ? htmlspecialchars($_GET['msg']) : null;
$status_type = isset($_GET['status']) ? htmlspecialchars($_GET['status']) : null;
// Tentukan judul halaman dan breadcrumb (Default)
$judul_halaman = "Beranda";
$breadcrumb_path = "» Beranda";
$content_html = "";
$active_menu = $modul;
// --- FUNGSI PENGAMBIL DATA DINAMIS UNTUK DROPDOWN & DEFAULT FILTER ---
$prodi_options = '';

$jurusan_options = '';
$sql_jurusan_list = "SELECT jurId, jurKode, jurNama FROM jurusan WHERE jurIsAktif = 1 ORDER BY jurNama ASC";
$result_jurusan_list = $conn->query($sql_jurusan_list);
if ($result_jurusan_list && $result_jurusan_list->num_rows > 0) {
    while($row = $result_jurusan_list->fetch_assoc()) {
        $jurusan_options .= '<option value="' . $row['jurId'] . '">' . htmlspecialchars($row['jurKode'] . ' - ' . $row['jurNama']) . '</option>';
    }
}

$dosen_pa_options = '<option value="">-- Pilih Dosen PA (Opsional) --</option>';
$sql_dosen_pa = "SELECT d.dsnNidn, d.dsnNama, d.dsnGelarBelakang, p.prodiNama 
                 FROM dosen d 
                 LEFT JOIN program_studi p ON d.dsnProdiId = p.prodiId 
                 ORDER BY d.dsnNama ASC";
$result_dosen_pa = $conn->query($sql_dosen_pa);
if ($result_dosen_pa && $result_dosen_pa->num_rows > 0) {
    while($row = $result_dosen_pa->fetch_assoc()) {
        $nama_lengkap = $row['dsnNama'] . (empty($row['dsnGelarBelakang']) ? '' : ', ' . $row['dsnGelarBelakang']);
        $prodi_info = $row['prodiNama'] ? ' (' . $row['prodiNama'] . ')' : '';
        $dosen_pa_options .= '<option value="' . $row['dsnNidn'] . '">' . htmlspecialchars($nama_lengkap . $prodi_info) . '</option>';
    }
}

$sql_prodi_list = "SELECT prodiId, prodiNama, prodiJenjang FROM program_studi ORDER BY prodiNama ASC";
$result_prodi_list = $conn->query($sql_prodi_list);
if ($result_prodi_list && $result_prodi_list->num_rows > 0) {
    while($row = $result_prodi_list->fetch_assoc()) {
        $prodi_options .= '<option value="' . $row['prodiId'] . '">' . htmlspecialchars($row['prodiJenjang'] . ' - ' . $row['prodiNama']) . '</option>';
    }
}
// Ambil data Tahun Akademik dan Semester
$thn_akd_options = '';
$smt_options_html = '';
$thakd_combined_options_for_form_kelas = '';
$sql_thn_akd = "SELECT thakdId, thakdTahun, thakdSemester, thakdIsAktif FROM tahun_akademik ORDER BY thakdTahun DESC, thakdSemester DESC";
$result_thn_akd = $conn->query($sql_thn_akd);
$tahun_unique_options = [];
$semester_unique_options = ['1' => 'Ganjil', '2' => 'Genap'];
if ($result_thn_akd && $result_thn_akd->num_rows > 0) {
    while($row = $result_thn_akd->fetch_assoc()) {
        $semester_nama = ($row['thakdSemester'] == '1') ? 'Ganjil' : 'Genap';
        $status_aktif = ($row['thakdIsAktif'] == 1) ? ' (Aktif)' : '';
        $tahun_semester_nama = htmlspecialchars($row['thakdTahun'] . ' / ' . $semester_nama . $status_aktif);
        $tahun_unique_options[ $row['thakdTahun'] ] = $row['thakdTahun'];
        $semester_unique_options[ $row['thakdSemester'] ] = $semester_nama;
        $thakd_combined_options_for_form_kelas .= '<option value="' . $row['thakdId'] . '">' . $tahun_semester_nama . '</option>';
    }
}
krsort($tahun_unique_options);
foreach ($tahun_unique_options as $tahun) {
    $thn_akd_options .= '<option value="' . $tahun . '">' . $tahun . '</option>';
}
foreach ($semester_unique_options as $id => $nama) {
    $smt_options_html .= '<option value="' . $id . '">' . $nama . '</option>';
}
// --- FUNGSI PEMBUAT MENU ---
function generate_submenu_html($modul_name, $menu_items) {
    $html = '<div id="submenu-' . $modul_name . '" class="submenu-container">';
    $html .= '<ul class="submenu-list">';
    foreach ($menu_items as $url_key => $title) {
        $is_submenu_active = (isset($_GET['submenu']) && $_GET['submenu'] == $url_key);
        $active_class = $is_submenu_active ? 'submenu-active' : '';
        $html .= '<li><a href="?modul=' . $modul_name . '&submenu=' . $url_key . '" class="' . $active_class . '">' . $title .
        '</a></li>';
    }
    $html .= '</ul>';
    $html .= '</div>';
    return $html;
}
// Definisikan semua submenu
$prakuliah_menu_items = ['kelas' => 'Daftar Kelas', 'matkul' => 'Daftar Matakuliah Kelas', 'dosen' => 'Daftar Dosen Kelas', 'mahasiswa' => 'Daftar Mahasiswa Kelas'];
$prakuliah_menu_html = generate_submenu_html('prakuliah', $prakuliah_menu_items);
$perkuliahan_menu_items = ['nilai' => 'Daftar Nilai', 'transkrip' => 'Transkrip Nilai', 'krs' => 'Kartu Rencana Studi (KRS)', 'khs' => 'Kartu Hasil Studi (KHS)'];
$perkuliahan_menu_html = generate_submenu_html('perkuliahan', $perkuliahan_menu_items);
$pascakuliah_menu_items = ['rapor' => 'Rapor Semester'];
$pascakuliah_menu_html = generate_submenu_html('pascakuliah', $pascakuliah_menu_items);
$datamaster_menu_items = ['jurusan' => 'Data Jurusan', 'prodi' => 'Data Program Studi', 'kurikulum' => 'Data Kurikulum', 'matkul' => 'Data Matakuliah', 'dosen' => 'Data Dosen', 'mahasiswa' => 'Data Mahasiswa'];
$datamaster_menu_html = generate_submenu_html('datamaster', $datamaster_menu_items);
$user_menu_items = ['kotak_pesan' => 'Kotak Pesan', 'ubah_password' => 'Ubah Password']; 
$user_menu_html = generate_submenu_html('user', $user_menu_items);
// --- FUNGSI PEMBUAT FORM PENCARIAN (Retensi Nilai Filter) ---
function generate_pencarian_form($conn, $prodi_options_orig, $thn_akd_options_orig, $smt_options_html_orig, $kelas_needed = false) {
    // Ambil nilai filter dari URL (retensi nilai)
    $filter_tahun = isset($_GET['filter_tahun']) ? $_GET['filter_tahun'] : '';
    $filter_semester_id = isset($_GET['filter_semester_id']) ? $_GET['filter_semester_id'] : '';
    $filter_prodi = isset($_GET['filter_prodi']) ? $_GET['filter_prodi'] : '';
    $filter_kelas = isset($_GET['filter_kelas']) ? $_GET['filter_kelas'] : '';
    
    
    $select_option = function($options_html, $selected_value) {
        if (!empty($selected_value)  || $selected_value === '0' || $selected_value === 0) {
			 $selected_value = addslashes($selected_value);
			 
            // Gantilah selected="selected" di option yang sesuai
            $options_html = str_replace('value="' . $selected_value . '"', 'value="' . $selected_value . '" selected="selected"', $options_html);
        }
        return $options_html;
    };
    
    // Terapkan retensi nilai
    $options_prodi_selected = $select_option($prodi_options_orig, $filter_prodi);
    $options_tahun_selected = $select_option($thn_akd_options_orig, $filter_tahun);
    $options_smt_selected = $select_option($smt_options_html_orig, $filter_semester_id);
    
    $kelas_options = '';
    
    // Jika Kelas dibutuhkan dan filter dasar sudah ada (Prodi/Tahun)
     $kelas_where_clauses = [];
    if (!empty($filter_tahun)) {
        $kelas_where_clauses[] = "ta.thakdTahun = '" . $conn->real_escape_string($filter_tahun) . "'";
    }
    if (!empty($filter_semester_id)) {
        $kelas_where_clauses[] = "ta.thakdSemester = '" . $conn->real_escape_string($filter_semester_id) . "'";
    }
    if (!empty($filter_prodi)) {
        $kelas_where_clauses[] = "k.klsProdiId = '" . $conn->real_escape_string($filter_prodi) . "'";
    }
    
    $kelas_where = count($kelas_where_clauses) > 0 ? " WHERE " . implode(" AND ", $kelas_where_clauses) : "";
    // Query untuk mengambil daftar Kelas berdasarkan filter yang dipilih pengguna
    $sql_kelas_list = "SELECT k.klsId, k.klsNama, ta.thakdTahun, ta.thakdSemester FROM kelas k JOIN tahun_akademik ta ON k.klsThakdId = ta.thakdId " . $kelas_where . " ORDER BY ta.thakdTahun DESC, ta.thakdSemester DESC, k.klsNama ASC";
    $result_kelas_list = $conn->query($sql_kelas_list);
    
    // Pastikan Nama Kelas selalu memiliki opsi "Pilih Nama Kelas" dengan value 0
    $kelas_options = '<option value="0">-- Pilih Nama Kelas --</option>'; 
    if ($result_kelas_list && $result_kelas_list->num_rows > 0) {
        while($row = $result_kelas_list->fetch_assoc()) {
            $selected = ($row['klsId'] == $filter_kelas) ? 'selected' : '';
            $semester_nama = ($row['thakdSemester'] == '1') ? 'Ganjil' : 'Genap';
            $kelas_info = htmlspecialchars($row['klsNama'] . ' (' . $row['thakdTahun'] . '/' . $semester_nama . ')');
            $kelas_options .= '<option value="' . $row['klsId'] . '" ' . $selected . '>' . $kelas_info . '</option>';
        }
    } 
    
    // Tentukan Judul Form Berdasarkan Submenu
    $submenu_title_for_form = "Data Berdasarkan :";
     
    $html = ' <div class="pencarian-box"> 
                <form action="" method="GET"> 
                    <input type="hidden" name="modul" value="' . (isset($_GET['modul']) ? htmlspecialchars($_GET['modul']) : '') . '"> 
                    <input type="hidden" name="submenu" value="' . (isset($_GET['submenu']) ? htmlspecialchars($_GET['submenu']) : '') . '"> 
                    <div class="pencarian-header">' . $submenu_title_for_form . '</div> 
                    <table> 
                        <tr> 
                            <td class="label-td">Thn-Smt Akademik</td> 
                            <td> 
                                <select name="filter_tahun" style="width: 150px;"><option value="">-- Pilih Tahun --</option>' . $options_tahun_selected . '</select> 
                                <select name="filter_semester_id" style="width: 100px;"><option value="">-- Smt --</option>' . $options_smt_selected . '</select> 
                            </td> 
                        </tr> 
                        <tr> 
                            <td class="label-td">Program Studi</td> 
                            <td> 
                                <select name="filter_prodi" style="width: 300px;"><option value="">-- Pilih Program Studi --</option>' . $options_prodi_selected . '</select> 
                            </td> 
                        </tr>';
    
    // Tambahkan filter Kelas jika diperlukan
    if ($kelas_needed) {
        $html .= '<tr>
                    <td class="label-td">Nama Kelas</td> 
                    <td> 
                        <select name="filter_kelas" style="width: 300px;"><option value="">-- Pilih Nama Kelas --</option>' . $select_option($kelas_options, $filter_kelas) . '</select> 
                    </td> 
                </tr>';
    }
    
    $html .= '      <tr>
                        <td colspan="2" style="text-align: right; padding-top: 15px;">
                            <button type="submit" class="btn-cari">Cari Data</button>
                        </td>
                    </tr>
                </table> 
            </form> 
        </div>';
    
    return $html;
}
// ===================== KONTEN BERDASARKAN MODUL DAN SUBMENU =====================
if ($modul == 'beranda') {
    // --- Modul Beranda (Dashboard Icons) ---
    $judul_halaman = "Beranda";
    $breadcrumb_path = '» Beranda';
    $content_html = '
        <div class="icon-grid">
            <a href="?modul=prakuliah" class="icon-box"><img src="https://tse3.mm.bing.net/th/id/OIP.i4-BDmM0LxhSZdNEdG4g9gHaHa?pid=Api&P=0&h=180" alt="Prakuliah Icon"><span>Prakuliah</span></a>
            <a href="?modul=perkuliahan" class="icon-box"><img src="https://tse3.mm.bing.net/th/id/OIP.qE1HM78CVS8RmF6KH1eNHwHaHa?pid=Api&P=0&h=180" alt="Perkuliahan Icon"><span>Perkuliahan</span></a>
            <a href="?modul=pascakuliah" class="icon-box"><img src="https://cdn.icon-icons.com/icons2/3600/PNG/512/study_science_education_school_learning_book_icon_226274.png" alt="Pascakuliah Icon"><span>Pascakuliah</span></a>
            <a href="?modul=datamaster" class="icon-box"><img src="https://tse1.mm.bing.net/th/id/OIP.MG0KIrrQ8XkPBSKU55S5rAHaHa?pid=Api&P=0&h=180" alt="Data Master Icon"><span>Data Master</span></a>
            <a href="?modul=user" class="icon-box"><img src="https://tse3.mm.bing.net/th/id/OIP.AilNOmF3DqonVoNds_ncvgHaHa?pid=Api&P=0&h=180" alt="User Icon"><span>User</span></a>
        </div>
    ';
	
	
} else if ($modul == 'prakuliah') {
    $judul_halaman = "Modul Prakuliah";
    $breadcrumb_path = '<a href="?modul=beranda">Beranda</a> <span class="breadcrumb-separator">  ►  </span> Prakuliah';
    $add_button = '<a href="?modul=prakuliah&submenu=' . $submenu . '&action=add" class="btn-add">Tambah Data</a>';
    
    // Ambil nama kelas jika filter kelas dipilih
    $kelas_nama = 'N/A';
    if ($kls_id_selected > 0) {
        $sql_kelas_name = "SELECT klsNama FROM kelas WHERE klsId = $kls_id_selected";
        $result_name = $conn->query($sql_kelas_name);
        if ($result_name && $result_name->num_rows > 0) {
            $kelas_nama = $result_name->fetch_assoc()['klsNama'];
        }
    }
    if ($submenu == 'kelas') { 
        // =======================================================
        // === 1. SUBMENU DAFTAR KELAS (Wajib Filter Prodi/Tahun)
        // =======================================================
        $table_header = '<thead><tr class="header-table-kelas"><th>No.</th><th>Nama Kelas</th><th>Prodi</th><th>Thn/Smt Akademik</th><th>Jlh Mahasiswa</th><th>Jlh Dosen</th><th>Jlh MK</th><th>Jlh SKS</th><th>Aksi</th></tr></thead>';
        $pencarian_form = generate_pencarian_form($conn, $prodi_options, $thn_akd_options, $smt_options_html, false);
        $submenu_title_display = 'Daftar Data Kelas';
        $table_data = '<tr><td colspan="9" style="text-align: center; padding: 20px;"></td></tr>';
        
        if ($is_filtered_search) {
            if (!empty($filter_prodi) && !empty($filter_tahun)) {
                // Jika sudah klik Cari dan filter wajib sudah dipilih, jalankan query
                $where_clause_kelas = " WHERE p.prodiId = '$filter_prodi' AND ta.thakdTahun = '$filter_tahun'";
                if (!empty($filter_semester_id)) { 
                    $where_clause_kelas .= " AND ta.thakdSemester = '$filter_semester_id'";
                }
                
                // Kueri untuk mengambil data kelas
                $sql_kelas = "SELECT k.klsId, k.klsNama, ta.thakdTahun, ta.thakdSemester, p.prodiJenjang, p.prodiNama, 
                                (SELECT COUNT(klsmhsId) FROM kelas_mahasiswa WHERE klsmhsKlsId = k.klsId) AS JlhMahasiswa, 
                                (SELECT COUNT(DISTINCT klsdsnDsnNidn) FROM kelas_dosen WHERE klsdsnKlsId = k.klsId) AS JlhDosen,
                                (SELECT COUNT(klsmkId) FROM kelas_matakuliah WHERE klsmkKlsId = k.klsId) AS JlhMk,
                                (SELECT SUM(m.mkSks) FROM kelas_matakuliah km JOIN matakuliah m ON km.klsmkMkId = m.mkId WHERE km.klsmkKlsId = k.klsId) AS JlhSks
                              FROM kelas k 
                              JOIN program_studi p ON k.klsProdiId = p.prodiId 
                              JOIN tahun_akademik ta ON k.klsThakdId = ta.thakdId 
                              " . $where_clause_kelas . " 
                              ORDER BY k.klsNama ASC";
                
                $result_kelas = $conn->query($sql_kelas);
                
                if ($result_kelas && $result_kelas->num_rows > 0) {
                    $no = 1;
                    $table_data = '';
                    while($row = $result_kelas->fetch_assoc()) {
                        $semester_nama = ($row['thakdSemester'] == '1') ? 'Ganjil' : 'Genap';
                        $thn_smt_akd = htmlspecialchars($row['thakdTahun'] . '/' . $semester_nama);
                        $prodi_info = htmlspecialchars($row['prodiJenjang'] . ' - ' . $row['prodiNama']);
                        
                        $table_data .= '
                            <tr>
                                <td>' . $no++ . '.</td>
                                <td style="text-align: left; font-weight: bold;">' . htmlspecialchars($row['klsNama']) . '</td>
                                <td>' . $prodi_info . '</td>
                                <td>' . $thn_smt_akd . '</td>
                                <td>' . htmlspecialchars($row['JlhMahasiswa']) . '</td>
                                <td>' . htmlspecialchars($row['JlhDosen']) . '</td>
                                <td>' . htmlspecialchars($row['JlhMk']) . '</td>
                                <td>' . htmlspecialchars($row['JlhSks']) . '</td>
                                <td>
                                    <a href="?modul=prakuliah&submenu=kelas&action=edit&id=' . $row['klsId'] . '" class="btn-aksi" style="color: blue;">Ubah</a> | 
                                    <a href="?modul=prakuliah&submenu=kelas&action=delete&id=' . $row['klsId'] . '" class="btn-aksi" style="color: red;" onclick="return confirm(\'Hapus ' . $row['klsNama'] . '?\')">Hapus</a>
                                </td>
                            </tr>';
                    }
                } else {
                    $table_data = '<tr><td colspan="9" style="text-align: center; padding: 5px;">**Tidak ditemukan data Kelas.**</td></tr>';
                }
            } else {
                $table_data = '<tr><td colspan="9" style="text-align: center; padding: 5px;">**Filter Program Studi dan Tahun Akademik harus dipilih.**</td></tr>';
            }
        } 
        
        // Cek jika action adalah 'add' (Form Tambah)
        if ($action == 'add') {
             $action_text = 'Tambah';
             $submenu_title = 'Kelas';
             $content_html = ' 
                 <div class="centered-form">
                     <form class="crud-form" method="POST" action=""> 
                         <input type="hidden" name="form_action" value="add_kelas">
                         <h3 style="text-align: center;">Tambah Data Kelas</h3>
                         <label for="kls_nama">Nama Kelas:</label>
                         <input type="text" id="kls_nama" name="kls_nama" value="' . (isset($_GET['kls_nama']) ? htmlspecialchars($_GET['kls_nama']) : '') . '" required placeholder="Contoh: III.A" style="width: 100%; padding: 12px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;"><br>
                         
                         <label for="kls_prodi_id">Program Studi:</label>
                         <select id="kls_prodi_id" name="kls_prodi_id" required style="width: 100%; padding: 12px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;">
                             <option value="">-- Pilih Program Studi --</option>
                             ' . $prodi_options . '
                         </select><br>
                         
                         <label for="kls_thakd_id">Tahun Akademik / Semester:</label>
                         <select id="kls_thakd_id" name="kls_thakd_id" required style="width: 100%; padding: 12px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;">
                             <option value="">-- Pilih Tahun Akademik --</option>
                             ' . $thakd_combined_options_for_form_kelas . '
                         </select><br>
                         
                         <p style="color: #6c757d; font-size: 0.9em; margin-top: 20px;">* Status Aktif diatur otomatis.</p>
                         <button type="submit" class="btn-save">Simpan Data Kelas</button>
                     </form>
                 </div>
                 <a href="?modul=prakuliah&submenu=' . $submenu . '" class="back-link">← Kembali ke Daftar Data</a>';
        } else {
            // Tampilan Daftar
             $content_html = ' 
                 <h2 class="submenu-data-title">' . $submenu_title_display . '</h2> 
                 ' . $pencarian_form . ' 
                 <div class="data-actions">
                    ' . $add_button . '
                 </div> 
                 <table class="data-kelas-table"> 
                     ' . $table_header . ' 
                     <tbody> 
                         ' . $table_data . ' 
                     </tbody> 
                 </table>';
        }
    } else if ($submenu == 'matkul') {
        // =======================================================
        // === 2. SUBMENU MATAKULIAH KELAS (Wajib Filter Kelas)
        // =======================================================
        $kelas_needed = true;
        $table_header = '<thead><tr class="header-table-mk"><th>No.</th><th>Nama Matakuliah</th><th>Kode MK</th><th>Smt</th><th>SKS</th><th>Jam</th><th>T/P</th><th>KMK</th><th>Aksi</th></tr></thead>';
        $pencarian_form = generate_pencarian_form($conn, $prodi_options, $thn_akd_options, $smt_options_html, true);
        $submenu_title_display = 'Daftar Data Matakuliah Kelas' . (($kls_id_selected > 0) ? ' (Kelas ' . $kelas_nama . ')' : '');
        $table_data = '<tr><td colspan="9" style="text-align: center; padding: 20px;"></td></tr>';
        if ($is_filtered_search) {
            if ($kls_id_selected > 0) {
                // Filter telah diklik dan Kelas dipilih
                $sql_matkul_kelas = "SELECT m.mkNama, m.mkKode, m.mkSemester, m.mkSks FROM kelas_matakuliah kmk JOIN matakuliah m ON kmk.klsmkMkId = m.mkId WHERE kmk.klsmkKlsId = $kls_id_selected ORDER BY m.mkSemester ASC, m.mkKode ASC";
                $result_matkul_kelas = $conn->query($sql_matkul_kelas);
                if ($result_matkul_kelas && $result_matkul_kelas->num_rows > 0) {
                    $no = 1;
                    $table_data = '';
                    while($row = $result_matkul_kelas->fetch_assoc()) {
						 $jam = $row['mkSks'] * 2;
						 $tipe = ($no % 2 == 0) ? 'T' : 'P';
						 $kmk = 'Wajib';
                         $table_data .= '
                            <tr>
                                <td>' . $no++ . '.</td>
                                <td style="text-align: left; font-weight: bold;">' . htmlspecialchars($row['mkNama']) . '</td>
                                <td>' . htmlspecialchars($row['mkKode']) . '</td>
                                <td>' . htmlspecialchars($row['mkSemester']) . '</td>
                                <td>' . htmlspecialchars($row['mkSks']) . '</td>
                                <td>' . $jam . '</td>
                                <td>' . $tipe . '</td>
                                <td>' . $kmk . '</td>
                                <td>
                                    <a href="#" class="btn-aksi" style="color: red;">Hapus</a>
                                </td>
                            </tr>';
                    }
                } else {
                    $table_data = '<tr><td colspan="9" style="text-align: center; padding: 5px;">**Tidak ditemukan Matakuliah untuk kelas ini.**</td></tr>';
                }
            } else {
                 $table_data = '<tr><td colspan="9" style="text-align: center; padding: 5px;">**Filter Nama Kelas harus dipilih.**</td></tr>';
            }
        }
		
        
         $content_html = ' 
            <h2 class="submenu-data-title">' . $submenu_title_display . '</h2> 
            ' . $pencarian_form . ' 
            <div class="data-actions">
                <a href="?modul=prakuliah&submenu=' . $submenu . '&action=add" class="btn-add">Tambah Matakuliah</a>
            </div> 
            <table class="data-kelas-table"> 
                ' . $table_header . ' 
                <tbody> 
                    ' . $table_data . ' 
                </tbody> 
            </table>';
    } else if ($submenu == 'dosen') {
        // =======================================================
        // === 3. SUBMENU DOSEN KELAS (Wajib Filter Kelas)
        // =======================================================
        $kelas_needed = true;
        $table_header = '<thead><tr class="header-table-dosen"><th>No.</th><th>Nama Dosen</th><th>Matakuliah</th><th>Kode MK</th><th>SKS</th><th>Jam</th><th>Status Aktif</th><th>Aksi</th></tr></thead>';
        $pencarian_form = generate_pencarian_form($conn, $prodi_options, $thn_akd_options, $smt_options_html, true);
        $submenu_title_display = 'Daftar Data Dosen Kelas' . (($kls_id_selected > 0) ? ' (Kelas ' . $kelas_nama . ')' : '');
        $table_data = '<tr><td colspan="8" style="text-align: center; padding: 20px;"></td></tr>';
        if ($is_filtered_search) {
            if ($kls_id_selected > 0) {
                // Filter telah diklik dan Kelas dipilih
                $sql_dosen_kelas = "SELECT d.dsnNama, d.dsnGelarBelakang, m.mkNama, m.mkKode, m.mkSks, kd.klsdsnIsAktif 
                                    FROM kelas_dosen kd 
                                    JOIN dosen d ON kd.klsdsnDsnNidn = d.dsnNidn 
                                    JOIN matakuliah m ON kd.klsdsnMkId = m.mkId 
                                    WHERE kd.klsdsnKlsId = $kls_id_selected 
                                    ORDER BY d.dsnNama ASC";
                $result_dosen_kelas = $conn->query($sql_dosen_kelas);
                
                if ($result_dosen_kelas && $result_dosen_kelas->num_rows > 0) {
                    $no = 1;
                    $table_data = '';
                    while($row = $result_dosen_kelas->fetch_assoc()) {
                        $nama_lengkap = htmlspecialchars($row['dsnNama']) . (empty($row['dsnGelarBelakang']) ? '' : ', ' . htmlspecialchars($row['dsnGelarBelakang']));
                        $status_aktif = ($row['klsdsnIsAktif'] == 1) ? 'Aktif' : 'Non-Aktif';
                        $table_data .= '
                            <tr>
                                <td>' . $no++ . '.</td>
                                <td style="text-align: left; font-weight: bold;">' . $nama_lengkap . '</td>
                                <td style="text-align: left;">' . htmlspecialchars($row['mkNama']) . '</td>
                                <td>' . htmlspecialchars($row['mkKode']) . '</td>
                                <td>' . htmlspecialchars($row['mkSks']) . '</td>
                                <td>2</td>
                                <td>' . $status_aktif . '</td>
                                <td>
                                    <a href="#" class="btn-aksi" style="color: red;">Hapus</a>
                                </td>
                            </tr>';
                    }
                } else {
                    $table_data = '<tr><td colspan="8" style="text-align: center; padding: 5px;">**Tidak ditemukan Dosen untuk kelas ini.**</td></tr>';
                }
            } else {
                 $table_data = '<tr><td colspan="8" style="text-align: center; padding: 5px;">**Filter Nama Kelas harus dipilih.**</td></tr>';
            }
        }
        
         $content_html = ' 
            <h2 class="submenu-data-title">' . $submenu_title_display . '</h2> 
            ' . $pencarian_form . ' 
            <div class="data-actions">
                <a href="?modul=prakuliah&submenu=' . $submenu . '&action=add" class="btn-add">Tambah Dosen</a>
            </div> 
            <table class="data-kelas-table"> 
                ' . $table_header . ' 
                <tbody> 
                    ' . $table_data . ' 
                </tbody> 
            </table>';
    } else if ($submenu == 'mahasiswa') {
        // =======================================================
        // === 4. SUBMENU MAHASISWA KELAS (Wajib Filter Kelas)
        // =======================================================
        $kelas_needed = true;
        $table_header = '<thead><tr class="header-table-mhs"><th>No.</th><th>NIM</th><th>Nama Mahasiswa</th><th>Jenis Kelamin</th><th>Status Akademik</th><th>Dosen PA</th><th>Aksi</th></tr></thead>';
        $pencarian_form = generate_pencarian_form($conn, $prodi_options, $thn_akd_options, $smt_options_html, true);
        $submenu_title_display = 'Daftar Data Mahasiswa Kelas' . (($kls_id_selected > 0) ? ' (Kelas ' . $kelas_nama . ')' : '');
        $table_data = '<tr><td colspan="7" style="text-align: center; padding: 20px;"></td></tr>';
        
        if ($is_filtered_search) {
            
            // Logic untuk Penugasan PA (Assign PA)
            if ($action == 'assign_pa' && isset($_GET['nim'])) {
                $nim_target = $conn->real_escape_string($_GET['nim']);
                
                // Handle Form Submission
                if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['assign_pa_submit'])) {
                    $pa_id_new = !empty($_POST['pa_id']) ? "'" . $conn->real_escape_string($_POST['pa_id']) . "'" : "NULL";
                    $sql_update_pa = "UPDATE mahasiswa SET mhsPaId = $pa_id_new WHERE mhsNim = '$nim_target'";
                    if ($conn->query($sql_update_pa)) {
                        $redirect_url = "?modul=prakuliah&submenu=mahasiswa&filter_tahun=$filter_tahun&filter_semester_id=$filter_semester_id&filter_prodi=$filter_prodi&filter_kelas=$filter_kelas&status=success&msg=" . urlencode("Dosen PA berhasil diperbarui!");
                        header("Location: " . $redirect_url);
                        exit();
                    } else {
                        echo "<div class='alert-box error'>Gagal update PA: " . $conn->error . "</div>";
                    }
                }

                // View Form Assign PA
                $sql_mhs_detail = "SELECT mhsNama, mhsPaId FROM mahasiswa WHERE mhsNim = '$nim_target'";
                $res_mhs = $conn->query($sql_mhs_detail);
                $data_mhs = $res_mhs->fetch_assoc();
                
                // PA Options
                $pa_options_list = '<option value="">-- Pilih Dosen PA --</option>';
                $sql_pa = "SELECT dsnNidn, dsnNama FROM dosen ORDER BY dsnNama ASC";
                $res_pa = $conn->query($sql_pa);
                while($r = $res_pa->fetch_assoc()) {
                    $selected = ($r['dsnNidn'] == $data_mhs['mhsPaId']) ? 'selected' : '';
                   $pa_options_list .= '<option value="'.$r['dsnNidn'].'" '.$selected.'>'.$r['dsnNama'].'</option>';
                }

                $content_html = '
                <div class="centered-form">
                    <form class="crud-form" method="POST" action="">
                        <input type="hidden" name="assign_pa_submit" value="1">
                        <h3 style="text-align: center;">Set Dosen PA untuk ' . htmlspecialchars($data_mhs['mhsNama']) . '</h3>
                        <label>Pilih Dosen Pembimbing Akademik:</label>
                        <select name="pa_id" style="width: 100%; padding: 12px; border: 1px solid #ccc;">
                            ' . $pa_options_list . '
                        </select>
                        <br><br>
                        <button type="submit" class="btn-save">Simpan Penugasan</button>
                    </form>
                </div>
                <a href="?modul=prakuliah&submenu=mahasiswa&filter_tahun='.$filter_tahun.'&filter_semester_id='.$filter_semester_id.'&filter_prodi='.$filter_prodi.'&filter_kelas='.$filter_kelas.'" class="back-link">← Kembali ke Daftar</a>';
                
            } 
            else if ($kls_id_selected > 0) {
                // Filter telah diklik dan Kelas dipilih
                $sql_mahasiswa_kelas = "SELECT m.mhsNim, m.mhsNama, m.mhsJenisKelamin, m.mhsStsAkademik, d.dsnNama AS PaNama 
                                        FROM mahasiswa m 
                                        JOIN kelas_mahasiswa km ON m.mhsNim = km.klsmhsMhsNim 
                                        LEFT JOIN dosen d ON TRIM(m.mhsPaId) = TRIM(d.dsnNidn) 
                                        WHERE km.klsmhsKlsId = $kls_id_selected 
                                        ORDER BY m.mhsNama ASC";
                $result_mahasiswa_kelas = $conn->query($sql_mahasiswa_kelas);
                
                if ($result_mahasiswa_kelas && $result_mahasiswa_kelas->num_rows > 0) {
                    $no = 1;
                    $table_data = '';
                    while($row = $result_mahasiswa_kelas->fetch_assoc()) {
                        $jenis_kelamin = ($row['mhsJenisKelamin'] == 'L') ? 'Laki-laki' : 'Perempuan';
                        $pa_nama = htmlspecialchars($row['PaNama'] ?? 'N/A');
                        $status_akademik_map = ['A' => 'Aktif', 'L' => 'Lulus', 'C' => 'Cuti', 'D' => 'DO', 'K' => 'Keluar', 'M' => 'Meninggal'];
                        $status = $status_akademik_map[$row['mhsStsAkademik']];
                        
                        $table_data .= '
                            <tr>
                                <td>' . $no++ . '.</td>
                                <td>' . htmlspecialchars($row['mhsNim']) . '</td>
                                <td style="text-align: left; font-weight: bold;">' . htmlspecialchars($row['mhsNama']) . '</td>
                                <td>' . $jenis_kelamin . '</td>
                                <td>' . $status . '</td>
                                <td>' . $pa_nama . '</td>
                                <td>
                                    <a href="?modul=prakuliah&submenu=mahasiswa&action=assign_pa&nim=' . $row['mhsNim'] . '&filter_tahun='.$filter_tahun.'&filter_semester_id='.$filter_semester_id.'&filter_prodi='.$filter_prodi.'&filter_kelas='.$filter_kelas.'" class="btn-aksi" style="color: blue;">Set PA</a> |
                                    <a href="#" class="btn-aksi" style="color: red;">Hapus</a>
                                </td>
                            </tr>';
                    }
                } else {
                    $table_data = '<tr><td colspan="7" style="text-align: center; padding: 5px;">**Tidak ditemukan Mahasiswa di kelas ini.**</td></tr>';
                }
            } else {
                 $table_data = '<tr><td colspan="7" style="text-align: center; padding: 5px;">**Filter Nama Kelas harus dipilih.**</td></tr>';
            }
        }
        
         $content_html = ' 
            <h2 class="submenu-data-title">' . $submenu_title_display . '</h2> 
            ' . $pencarian_form . ' 
            <div class="data-actions">
                <a href="?modul=prakuliah&submenu=' . $submenu . '&action=add" class="btn-add">Tambah Mahasiswa</a>
            </div> 
            <table class="data-kelas-table"> 
                ' . $table_header . ' 
                <tbody> 
                    ' . $table_data . ' 
                </tbody> 
            </table>';
    } else {
        // Submenu default Prakuliah
        $content_html = ' 
             <h2>Pilih Submenu Modul Prakuliah</h2> 
             <hr> 
             <div class="icon-grid" style="grid-template-columns: repeat(4, 1fr);">
                 <a href="?modul=prakuliah&submenu=kelas" class="icon-box"><img src="https://png.pngtree.com/png-vector/20190701/ourmid/pngtree-classroom-icon-for-your-project-png-image_1533148.jpg" alt="Kelas"><span>Daftar Kelas</span></a>
                 <a href="?modul=prakuliah&submenu=matkul" class="icon-box"><img src="https://tse1.mm.bing.net/th/id/OIP.Rs1aK6D1E9wkNhLVkZvFUQHaFO?pid=Api&P=0&h=180" alt="Matakuliah Kelas"><span>Daftar Matakuliah Kelas</span></a>
                 <a href="?modul=prakuliah&submenu=dosen" class="icon-box"><img src="https://cdn.pixabay.com/photo/2016/04/26/12/25/male-1354358__480.png" alt="Dosen Kelas"><span>Daftar Dosen Kelas</span></a>
                 <a href="?modul=prakuliah&submenu=mahasiswa" class="icon-box"><img src="https://tse2.mm.bing.net/th/id/OIP.avPsweqjCly4XCznxqf0OQHaEz?pid=Api&P=0&h=180" alt="Mahasiswa Kelas"><span>Daftar Mahasiswa Kelas</span></a>
             </div>';
    }
    
} else if ($modul == 'perkuliahan') {
    $judul_halaman = "Modul Perkuliahan";
    $breadcrumb_path = '<a href="?modul=beranda">Beranda</a> <span class="breadcrumb-separator">  ►  </span> Perkuliahan';
    
    // Logika Perkuliahan
    // Ambil nama kelas jika filter kelas dipilih (Agar tidak error undefined variable)
    $kelas_nama = 'N/A';
    if ($kls_id_selected > 0) {
        $sql_kelas_name = "SELECT klsNama FROM kelas WHERE klsId = $kls_id_selected";
        $result_name = $conn->query($sql_kelas_name);
        if ($result_name && $result_name->num_rows > 0) {
            $kelas_nama = $result_name->fetch_assoc()['klsNama'];
        }
    }

    if ($submenu == 'nilai') {
        // =======================================================
        // === 1. SUBMENU DAFTAR NILAI (Wajib Filter Kelas)
        // =======================================================
        $kelas_needed = true;
        $table_header = '<thead><tr class="header-table-nilai"><th>No.</th><th>NIM</th><th>Nama Mahasiswa</th><th>Kode MK</th><th>Nama Matakuliah</th><th>Nilai Angka</th><th>Aksi</th></tr></thead>';
        $pencarian_form = generate_pencarian_form($conn, $prodi_options, $thn_akd_options, $smt_options_html, true);
        $submenu_title_display = 'Daftar Nilai' . (($kls_id_selected > 0) ? ' (Kelas ' . $kelas_nama . ')' : '');
        
        $table_data = '<tr><td colspan="7" style="text-align: center; padding: 20px;"></td></tr>';
        
        if ($action == 'input_nilai' && isset($_GET['nim']) && isset($_GET['mk_kode'])) {
            $nim_target = $conn->real_escape_string($_GET['nim']);
            $mk_kode_target = $conn->real_escape_string($_GET['mk_kode']);

            // --- PROSES SIMPAN NILAI ---
            if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['simpan_nilai'])) {
                $nilai_angka = $conn->real_escape_string($_POST['nilai_angka']);
                
                // Cek apakah data nilai sudah ada di tabel krs_khs (Asumsi tabel ini menyimpan nilai)
                // Karena struktur tabel nilai tidak didefinisikan secara eksplisit di setup_database,
                // Kita akan buat asumsi sederhana atau menggunakan tabel dummy jika belum ada.
                // UNTUK DEMO INI: Kita akan simpan di tabel 'krs_khs' jika ada, atau buat tabel baru 'nilai_mahasiswa'
                
                // Cek tabel 'nilai_mahasiswa' exists
                $check_table = $conn->query("SHOW TABLES LIKE 'nilai_mahasiswa'");
                if ($check_table->num_rows == 0) {
                     $conn->query("CREATE TABLE `nilai_mahasiswa` (
                        `nmId` int AUTO_INCREMENT PRIMARY KEY,
                        `nmNim` varchar(20),
                        `nmMkKode` varchar(20),
                        `nmNilaiAngka` float,
                        `nmSemesterId` int
                     )");
                }

                // Cek data existing
                $cek_sql = "SELECT nmId FROM nilai_mahasiswa WHERE nmNim = '$nim_target' AND nmMkKode = '$mk_kode_target' AND nmSemesterId = '$filter_semester_id'";
                $cek_res = $conn->query($cek_sql);

                if ($cek_res && $cek_res->num_rows > 0) {
                    $update_sql = "UPDATE nilai_mahasiswa SET nmNilaiAngka = '$nilai_angka' WHERE nmNim = '$nim_target' AND nmMkKode = '$mk_kode_target' AND nmSemesterId = '$filter_semester_id'";
                    $conn->query($update_sql);
                } else {
                    $insert_sql = "INSERT INTO nilai_mahasiswa (nmNim, nmMkKode, nmNilaiAngka, nmSemesterId) VALUES ('$nim_target', '$mk_kode_target', '$nilai_angka', '$filter_semester_id')";
                    $conn->query($insert_sql);
                }
                
                $redirect_url = "?modul=perkuliahan&submenu=nilai&filter_tahun=$filter_tahun&filter_semester_id=$filter_semester_id&filter_prodi=$filter_prodi&filter_kelas=$filter_kelas&status=success&msg=" . urlencode("Nilai berhasil disimpan!");
                header("Location: " . $redirect_url);
                exit();
            }

            // --- FORM INPUT NILAI ---
            // Ambil data Mahasiswa dan MK
            $sql_info = "SELECT m.mhsNama, mk.mkNama FROM mahasiswa m, matakuliah mk WHERE m.mhsNim = '$nim_target' AND mk.mkKode = '$mk_kode_target'";
            $res_info = $conn->query($sql_info);
            $info = $res_info->fetch_assoc();

            // Ambil nilai existing
            $nilai_existing = "";
            $msg_exists = $conn->query("SHOW TABLES LIKE 'nilai_mahasiswa'");
            if ($msg_exists->num_rows > 0) {
                 $sql_get_nilai = "SELECT nmNilaiAngka FROM nilai_mahasiswa WHERE nmNim = '$nim_target' AND nmMkKode = '$mk_kode_target'";
                 $res_nilai = $conn->query($sql_get_nilai);
                 if ($res_nilai && $res_nilai->num_rows > 0) {
                     $nilai_existing = $res_nilai->fetch_assoc()['nmNilaiAngka'];
                 }
            }

            $content_html = '
            <div class="centered-form">
                <form class="crud-form" method="POST" action="">
                    <input type="hidden" name="simpan_nilai" value="1">
                    <h3 style="text-align: center;">Input Nilai Mahasiswa</h3>
                    
                    <div class="info-box" style="background-color: #f8f9fa; padding: 10px; margin-bottom: 15px; border: 1px solid #ddd;">
                        <p><strong>NIM:</strong> ' . htmlspecialchars($nim_target) . '</p>
                        <p><strong>Nama:</strong> ' . htmlspecialchars($info['mhsNama']) . '</p>
                        <p><strong>Matakuliah:</strong> ' . htmlspecialchars($info['mkNama']) . ' (' . htmlspecialchars($mk_kode_target) . ')</p>
                    </div>

                    <label>Nilai Angka (0-100):</label>
                    <input type="number" name="nilai_angka" value="' . $nilai_existing . '" min="0" max="100" step="0.01" required style="width: 100%; padding: 10px; border: 1px solid #ccc;">
                    
                    <button type="submit" class="btn-save">Simpan Nilai</button>
                </form>
            </div>
            <a href="?modul=perkuliahan&submenu=nilai&filter_tahun='.$filter_tahun.'&filter_semester_id='.$filter_semester_id.'&filter_prodi='.$filter_prodi.'&filter_kelas='.$filter_kelas.'" class="back-link">← Kembali ke Daftar Nilai</a>';
        
        } else if ($is_filtered_search) {
            if ($kls_id_selected > 0) {
                // Cek tabel nilai dulu untuk join
                $conn->query("CREATE TABLE IF NOT EXISTS `nilai_mahasiswa` (
                        `nmId` int AUTO_INCREMENT PRIMARY KEY,
                        `nmNim` varchar(20),
                        `nmMkKode` varchar(20),
                        `nmNilaiAngka` float,
                        `nmSemesterId` int
                     )");

                // Query Gabungan
                $sql_nilai = "SELECT m.mhsNim, m.mhsNama, mk.mkKode, mk.mkNama, nm.nmNilaiAngka 
                              FROM mahasiswa m 
                              JOIN kelas_mahasiswa km ON m.mhsNim = km.klsmhsMhsNim 
                              JOIN kelas_matakuliah mkl ON km.klsmhsKlsId = mkl.klsmkKlsId 
                              JOIN matakuliah mk ON mkl.klsmkMkId = mk.mkId 
                              LEFT JOIN nilai_mahasiswa nm ON (m.mhsNim = nm.nmNim AND mk.mkKode = nm.nmMkKode)
                              WHERE km.klsmhsKlsId = $kls_id_selected 
                              ORDER BY m.mhsNama ASC, mk.mkKode ASC";
                $result_nilai = $conn->query($sql_nilai);
                
                if ($result_nilai && $result_nilai->num_rows > 0) {
                    $no = 1;
                    $table_data = '';
                    while($row = $result_nilai->fetch_assoc()) {
                        $nilai_final = ($row['nmNilaiAngka'] !== null) ? $row['nmNilaiAngka'] : '-';
                        $table_data .= '
                            <tr>
                                <td>' . $no++ . '.</td>
                                <td>' . htmlspecialchars($row['mhsNim']) . '</td>
                                <td style="text-align: left; font-weight: bold;">' . htmlspecialchars($row['mhsNama']) . '</td>
                                <td>' . htmlspecialchars($row['mkKode']) . '</td>
                                <td style="text-align: left;">' . htmlspecialchars($row['mkNama']) . '</td>
                                <td style="font-weight: bold; text-align: center;">' . $nilai_final . '</td>
                                <td><a href="?modul=perkuliahan&submenu=nilai&action=input_nilai&nim=' . $row['mhsNim'] . '&mk_kode=' . $row['mkKode'] . '&filter_tahun='.$filter_tahun.'&filter_semester_id='.$filter_semester_id.'&filter_prodi='.$filter_prodi.'&filter_kelas='.$filter_kelas.'" class="btn-aksi" style="background-color: #28a745;">Input Nilai</a></td>
                            </tr>';
                    }
                } else {
                    $table_data = '<tr><td colspan="7" style="text-align: center; padding: 5px;">**Tidak ditemukan data Mahasiswa/Matakuliah untuk kelas ini.**</td></tr>';
                }
            } else {
                 $table_data = '<tr><td colspan="7" style="text-align: center; padding: 5px;">**Filter Nama Kelas harus dipilih.**</td></tr>';
            }

            $content_html = ' 
            <h2 class="submenu-data-title">' . $submenu_title_display . '</h2> 
            ' . $pencarian_form . ' 
            <table class="data-kelas-table"> 
                ' . $table_header . ' 
                <tbody> 
                    ' . $table_data . ' 
                </tbody> 
            </table>';
        } else {
             $content_html = ' 
            <h2 class="submenu-data-title">' . $submenu_title_display . '</h2> 
            ' . $pencarian_form . ' 
            <div style="text-align: center; padding: 40px; color: #666;">
                Silakan pilih filter (Tahun Akademi, Semester, Prodi, dan Kelas) lalu klik <strong>Tampilkan Data</strong> untuk melihat daftar nilai.
            </div>';
        }
    } else {
        // Submenu default Perkuliahan
        $content_html = ' 
             <h2>Pilih Submenu Modul Perkuliahan</h2> 
             <hr> 
             <div class="icon-grid" style="grid-template-columns: repeat(2, 1fr);">
                 <a href="?modul=perkuliahan&submenu=nilai" class="icon-box"><img src="https://png.pngtree.com/png-vector/20191018/ourlarge/pngtree-a-grade-icon-for-your-design-websites-and-projects-png-image_1830197.jpg" alt="Nilai"><span>Daftar Nilai</span></a>
                 <a href="?modul=perkuliahan&submenu=transkrip" class="icon-box"><img src="https://ult.unpad.ac.id/wp-content/uploads/2024/05/ICON-WEBSITE-3.png" alt="Transkrip"><span>Transkrip Nilai</span></a>
                 <a href="?modul=perkuliahan&submenu=krs" class="icon-box"><img src="https://tse4.mm.bing.net/th/id/OIP.3bngGQzOnGXhQ1oZg5QoLwHaEK?pid=Api&P=0&h=180" alt="KRS"><span>Kartu Rencana Studi (KRS)</span></a>
                 <a href="?modul=perkuliahan&submenu=khs" class="icon-box"><img src="https://media.istockphoto.com/id/520241473/id/vektor/kartu-laporan.jpg?s=612x612&w=0&k=20&c=LW6nLvGYSg_wq64QNabGE4pKGR6Hlpxvor-Z64J5bbM=" alt="KHS"><span>Kartu Hasil Studi (KHS)</span></a>
             </div>';
    }
	
} else if ($modul == 'datamaster') {
    $judul_halaman = "Modul Data Master";
    $breadcrumb_path = '<a href="?modul=beranda">Beranda</a> <span class="breadcrumb-separator">  ►  </span> Data Master';
    $add_button_datamaster = '<a href="?modul=datamaster&submenu=' . $submenu . '&action=add" class="btn-add">Tambah Data</a>';
    
    // Tentukan konten berdasarkan submenu
    if (isset($datamaster_menu_items[$submenu])) {
        $submenu_title = $datamaster_menu_items[$submenu];
        $pencarian_form = '';
        $table_header = '';
        $table_data = '';
        $add_button = '<div class="data-actions">' . $add_button_datamaster . '</div>';
        if ($submenu == 'jurusan') {
            // =======================================================
            // === 1. SUBMENU DATA JURUSAN (Tidak pakai filter form)
            // =======================================================
            $table_header = '<thead><tr class="header-table-datamaster-jurusan"><th>No.</th><th>Kode Jurusan</th><th>Nama Jurusan</th><th>Status Aktif</th><th>Aksi</th></tr></thead>';
            $sql_jurusan = "SELECT jurId, jurKode, jurNama, jurIsAktif FROM jurusan ORDER BY jurNama ASC";
            $result_jurusan = $conn->query($sql_jurusan);
            
            if ($action == 'add') {
                 $action_text = 'Tambah';
                 $content_html = ' 
                     <div class="centered-form">
                         <form class="crud-form" method="POST" action=""> 
                             <input type="hidden" name="form_action" value="add_jurusan">
                             <h3 style="text-align: center;">Formulir Tambah Data Jurusan</h3>
                             <label for="jur_kode">Kode Jurusan:</label>
                             <input type="text" id="jur_kode" name="jur_kode" value="' . (isset($_GET['jur_kode']) ? htmlspecialchars($_GET['jur_kode']) : '') . '" required placeholder="Contoh: TI" style="width: 100%; padding: 12px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;"><br>
                             <label for="jur_nama">Nama Jurusan:</label>
                             <input type="text" id="jur_nama" name="jur_nama" value="' . (isset($_GET['jur_nama']) ? htmlspecialchars($_GET['jur_nama']) : '') . '" required placeholder="Contoh: Teknologi Informasi" style="width: 100%; padding: 12px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;"><br>
                             <label for="jur_nama_asing">Nama Asing:</label>
                             <input type="text" id="jur_nama_asing" name="jur_nama_asing" placeholder="Contoh: Information Technology" style="width: 100%; padding: 12px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;"><br>
                             <label style="margin-top: 20px;">
                                <input type="checkbox" name="jur_is_aktif" value="1" checked> 
                                Status Aktif
                             </label><br>
                             <button type="submit" class="btn-save">Simpan Data Jurusan</button>
                         </form>
                     </div>
                     <a href="?modul=datamaster&submenu=' . $submenu . '" class="back-link">← Kembali ke Daftar Data</a>';
            } else if ($action == 'edit' && isset($_GET['id'])) {
                $jur_id_edit = $conn->real_escape_string($_GET['id']);
                $sql_get_data = "SELECT * FROM jurusan WHERE jurId = '$jur_id_edit'";
                $res_data = $conn->query($sql_get_data);
                $data = $res_data->fetch_assoc();

                if (!$data) {
                    $content_html = "<p style='color: red; text-align: center;'>Data Jurusan tidak ditemukan!</p>";
                } else {
                    $content_html = '
                    <div class="centered-form">
                        <form class="crud-form" method="POST" action=""> 
                            <input type="hidden" name="form_action" value="edit_jurusan">
                            <input type="hidden" name="jur_id" value="' . htmlspecialchars($data['jurId']) . '">
                            <h3 style="text-align: center;">Formulir Ubah Data Jurusan</h3>
                            <label for="jur_kode">Kode Jurusan:</label>
                            <input type="text" id="jur_kode" name="jur_kode" value="' . htmlspecialchars($data['jurKode']) . '" required placeholder="Contoh: TI" style="width: 100%; padding: 12px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;"><br>
                            <label for="jur_nama">Nama Jurusan:</label>
                            <input type="text" id="jur_nama" name="jur_nama" value="' . htmlspecialchars($data['jurNama']) . '" required placeholder="Contoh: Teknologi Informasi" style="width: 100%; padding: 12px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;"><br>
                            <label for="jur_nama_asing">Nama Asing:</label>
                            <input type="text" id="jur_nama_asing" name="jur_nama_asing" value="' . htmlspecialchars($data['jurNamaAsing']) . '" placeholder="Contoh: Information Technology" style="width: 100%; padding: 12px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;"><br>
                            <label style="margin-top: 20px;">
                                <input type="checkbox" name="jur_is_aktif" value="1" ' . ($data['jurIsAktif'] == 1 ? 'checked' : '') . '> 
                                Status Aktif
                            </label><br>
                            <button type="submit" class="btn-save">Simpan Perubahan Jurusan</button>
                        </form>
                    </div>
                    <a href="?modul=datamaster&submenu=' . $submenu . '" class="back-link">← Kembali ke Daftar Data</a>';
                }
            } else {
                 if ($result_jurusan && $result_jurusan->num_rows > 0) {
                     $no = 1;
                     $table_data = '';
                     while($row = $result_jurusan->fetch_assoc()) {
                         $status = ($row['jurIsAktif'] == 1) ? 'Aktif' : 'Non-Aktif';
                         $table_data .= '
                            <tr>
                                <td>' . $no++ . '.</td>
                                <td>' . htmlspecialchars($row['jurKode']) . '</td>
                                <td style="text-align: left; font-weight: bold;">' . htmlspecialchars($row['jurNama']) . '</td>
                                <td>' . $status . '</td>
                                <td>
                                    <a href="?modul=datamaster&submenu=jurusan&action=edit&id=' . $row['jurId'] . '" class="btn-aksi" style="color: blue;">Ubah</a> | 
                                    <a href="?modul=datamaster&submenu=jurusan&action=delete&id=' . $row['jurId'] . '" class="btn-aksi" style="color: red;" onclick="return confirm(\'Hapus ' . $row['jurNama'] . '?\')">Hapus</a>
                                </td>
                            </tr>';
                     }
                 } else {
                     $table_data = '<tr><td colspan="5" style="text-align: center; padding: 5px;">**Tidak ditemukan data Jurusan.**</td></tr>';
                 }
                 $content_html = ' 
                     <h2 class="submenu-data-title">' . $submenu_title . '</h2> 
                     ' . $add_button_datamaster . '
                     <table class="data-kelas-table"> 
                         ' . $table_header . ' 
                         <tbody> 
                             ' . $table_data . ' 
                         </tbody> 
                     </table>';
            }
} else if ($submenu == 'prodi') {
    // --- 2. PROGRAM STUDI (Tidak pakai filter form) --- 
    $table_header = '<thead><tr class="header-table-datamaster-prodi"><th>No.</th><th>Kode Prodi</th><th>Nama Program Studi</th><th>Jurusan</th><th>Jenjang</th><th>Aksi</th></tr></thead>';
    
    // Cek jika action adalah 'add' (Form Tambah)
    if ($action == 'add') {
        $action_text = 'Tambah';
        $content_html = ' 
            <div class="centered-form">
                <form class="crud-form" method="POST" action=""> 
                    <input type="hidden" name="form_action" value="add_prodi">
                    <h3 style="text-align: center;">Formulir Tambah Data Program Studi</h3>
                    
                    <label for="prodi_jur_id">Jurusan: <span style="color: red;">*</span></label>
                    <select id="prodi_jur_id" name="prodi_jur_id" required style="width: 100%; padding: 12px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;">
                        <option value="">-- Pilih Jurusan --</option>
                        ' . $jurusan_options . '
                    </select><br>
                    
                    <label for="prodi_kode">Kode Program Studi: <span style="color: red;">*</span></label>
                    <input type="text" id="prodi_kode" name="prodi_kode" value="' . (isset($_GET['prodi_kode']) ? htmlspecialchars($_GET['prodi_kode']) : '') . '" required placeholder="Contoh: 4TT" style="width: 100%; padding: 12px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;"><br>
                    
                    <label for="prodi_nama">Nama Program Studi: <span style="color: red;">*</span></label>
                    <input type="text" id="prodi_nama" name="prodi_nama" value="' . (isset($_GET['prodi_nama']) ? htmlspecialchars($_GET['prodi_nama']) : '') . '" required placeholder="Contoh: Teknik Telekomunikasi" style="width: 100%; padding: 12px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;"><br>
                    
                    <label for="prodi_nama_asing">Nama Asing:</label>
                    <input type="text" id="prodi_nama_asing" name="prodi_nama_asing" placeholder="Contoh: Telecommunication Engineering" style="width: 100%; padding: 12px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;"><br>
                    
                    <label for="prodi_jenjang">Jenjang: <span style="color: red;">*</span></label>
                    <select id="prodi_jenjang" name="prodi_jenjang" required style="width: 100%; padding: 12px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;">
                        <option value="">-- Pilih Jenjang --</option>
                        <option value="D-3">D-3 (Diploma Tiga)</option>
                        <option value="D-4">D-4 (Diploma Empat)</option>
                        <option value="S-1">S-1 (Sarjana)</option>
                        <option value="S-2">S-2 (Magister)</option>
                    </select><br>
                    
                    <label for="prodi_email">Email Program Studi:</label>
                    <input type="email" id="prodi_email" name="prodi_email" placeholder="Contoh: tt@polinpdg.ac.id" style="width: 100%; padding: 12px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;"><br>
                    
                    <label for="prodi_website">Website:</label>
                    <input type="text" id="prodi_website" name="prodi_website" placeholder="Contoh: http://tt.polinpdg.ac.id" style="width: 100%; padding: 12px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;"><br>
                    
                    <label style="margin-top: 20px;">
                       <input type="checkbox" name="prodi_is_aktif" value="1" checked> 
                       Status Aktif
                    </label><br>
                    
                    <p style="color: #6c757d; font-size: 0.9em; margin-top: 20px;"><span style="color: red;">*</span> Wajib diisi</p>
                    
                    <button type="submit" class="btn-save">Simpan Data Program Studi</button>
                </form>
            </div>
            <a href="?modul=datamaster&submenu=' . $submenu . '" class="back-link">← Kembali ke Daftar Data</a>';
    } 
    
    else if ($action == 'edit' && isset($_GET['id'])) {
        $id_edit = $conn->real_escape_string($_GET['id']);
        $sql_get_data = "SELECT * FROM program_studi WHERE prodiId = '$id_edit'";
        $res_data = $conn->query($sql_get_data);
        $data = $res_data->fetch_assoc();

        if (!$data) {
            echo "Data tidak ditemukan!";
        } else {
            $content_html = '
            <div class="centered-form">
                <form class="crud-form" method="POST" action=""> 
                    <input type="hidden" name="form_action" value="edit_prodi">
                    <input type="hidden" name="prodi_id" value="'.$data['prodiId'].'">
                    <h3 style="text-align: center;">Formulir Ubah Data Program Studi</h3>
                    
                    <label for="prodi_jur_id">Jurusan:</label>
                    <select id="prodi_jur_id" name="prodi_jur_id" required style="width: 100%; padding: 12px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;">
                        ' . str_replace('value="'.$data['prodiJurId'].'"', 'value="'.$data['prodiJurId'].'" selected', $jurusan_options) . '
                    </select><br>
                    
                    <label for="prodi_kode">Kode Program Studi:</label>
                    <input type="text" id="prodi_kode" name="prodi_kode" value="'.$data['prodiKode'].'" required style="width: 100%; padding: 12px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;"><br>
                    
                    <label for="prodi_nama">Nama Program Studi:</label>
                    <input type="text" id="prodi_nama" name="prodi_nama" value="'.$data['prodiNama'].'" required style="width: 100%; padding: 12px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;"><br>
                    
                    <label for="prodi_jenjang">Jenjang:</label>
                    <select id="prodi_jenjang" name="prodi_jenjang" required style="width: 100%; padding: 12px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;">
                        <option value="D-3" '.($data['prodiJenjang'] == 'D-3' ? 'selected' : '').'>D-3 (Diploma Tiga)</option>
                        <option value="D-4" '.($data['prodiJenjang'] == 'D-4' ? 'selected' : '').'>D-4 (Diploma Empat)</option>
                    </select><br>

                    <label style="margin-top: 20px;">
                       <input type="checkbox" name="prodi_is_aktif" value="1" '.($data['prodiIsAktif'] == 1 ? 'checked' : '').'> Status Aktif
                    </label><br>
                    
                    <button type="submit" class="btn-save">Simpan Perubahan</button>
                </form>
            </div>
            <a href="?modul=datamaster&submenu=prodi" class="back-link">← Kembali ke Daftar Data</a>';
        }
    }
    
    
    else {
        // Tampilan List
        $sql_prodi = "SELECT p.prodiId, p.prodiKode, p.prodiNama, p.prodiJenjang, j.jurNama 
                      FROM program_studi p JOIN jurusan j ON p.prodiJurId = j.jurId ORDER BY j.jurNama, p.prodiNama ASC";
        $result_prodi = $conn->query($sql_prodi);
        
        if ($result_prodi && $result_prodi->num_rows > 0) {
            $no = 1;
            $table_data = '';
            while($row = $result_prodi->fetch_assoc()) {
                $table_data .= '
                    <tr>
                        <td>' . $no++ . '.</td>
                        <td>' . htmlspecialchars($row['prodiKode']) . '</td>
                        <td style="text-align: left; font-weight: bold;">' . htmlspecialchars($row['prodiNama']) . '</td>
                        <td style="text-align: left;">' . htmlspecialchars($row['jurNama']) . '</td>
                        <td>' . htmlspecialchars($row['prodiJenjang']) . '</td>
                        <td>
                            <a href="?modul=datamaster&submenu=prodi&action=edit&id=' . $row['prodiId'] . '" class="btn-aksi" style="color: blue;">Ubah</a> | 
                            <a href="?modul=datamaster&submenu=prodi&action=delete&id=' . $row['prodiId'] . '" class="btn-aksi" style="color: red;" onclick="return confirm(\'Hapus ' . $row['prodiNama'] . '?\')">Hapus</a>
                        </td>
                    </tr>';
            }
        } else {
            $table_data = '<tr><td colspan="6" style="text-align: center; padding: 5px;">**Tidak ditemukan data Program Studi.**</td></tr>';
        }
        
        $content_html = ' 
            <h2 class="submenu-data-title">' . $submenu_title . '</h2> 
            ' . $add_button_datamaster . '
            <table class="data-kelas-table"> 
                ' . $table_header . ' 
                <tbody> 
                    ' . $table_data . ' 
                </tbody> 
            </table>';
    }
} else if ($submenu == 'kurikulum') {
    // --- 3. KURIKULUM (Tidak pakai filter form) --- 
    $table_header = '<thead><tr class="header-table-datamaster-kurikulum"><th>No.</th><th>Nama Kurikulum</th><th>Program Studi</th><th>Tahun</th><th>Status Aktif</th><th>Aksi</th></tr></thead>';
    
    // Cek jika action adalah 'add' (Form Tambah)
    if ($action == 'add') {
        $action_text = 'Tambah';
        
        // Get nilai dari URL untuk retensi jika ada error
        $kur_tahun = isset($_GET['kur_tahun']) ? htmlspecialchars($_GET['kur_tahun']) : date('Y');
        $kur_nama = isset($_GET['kur_nama']) ? htmlspecialchars($_GET['kur_nama']) : '';
        
        $content_html = '
        <style>
            .form-kurikulum-container {
                max-width: 600px;
                margin: 30px auto;
            }
            .form-kurikulum {
                background-color: #f9f9f9;
                padding: 30px;
                border: 1px solid #ddd;
                border-radius: 8px;
                box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            }
            .form-kurikulum h3 {
                text-align: center;
                color: var(--color-dark-blue);
                border-bottom: 2px solid var(--color-header-orange);
                padding-bottom: 10px;
                margin-top: 0;
                margin-bottom: 25px;
            }
            .form-group {
                margin-bottom: 20px;
            }
            .form-group label {
                display: block;
                margin-bottom: 8px;
                font-weight: bold;
                color: #555;
            }
            .form-group input, 
            .form-group select {
                width: 100%;
                padding: 12px;
                border: 1px solid #ccc;
                border-radius: 4px;
                box-sizing: border-box;
                font-size: 1em;
            }
            .form-group input:focus, 
            .form-group select:focus {
                outline: none;
                border-color: var(--color-accent-blue);
                box-shadow: 0 0 5px rgba(0, 123, 255, 0.3);
            }
            .required {
                color: red;
            }
            .checkbox-group {
                display: flex;
                align-items: center;
                margin-top: 15px;
            }
            .checkbox-group input[type="checkbox"] {
                width: auto;
                margin-right: 10px;
            }
            .info-text {
                font-size: 0.85em;
                color: #666;
                margin-top: 5px;
            }
        </style>
        
        <h2 class="submenu-data-title">Tambah Data Kurikulum</h2>
        
        <div class="form-kurikulum-container">
            <form class="form-kurikulum" method="POST" action="" onsubmit="return validateFormKurikulum()">
                <input type="hidden" name="form_action" value="add_kurikulum">
                <h3>Formulir Data Kurikulum</h3>
                
                <div class="form-group">
                    <label for="kur_prodi_id">Program Studi <span class="required">*</span></label>
                    <select id="kur_prodi_id" name="kur_prodi_id" required>
                        <option value="">-- Pilih Program Studi --</option>
                        ' . $prodi_options . '
                    </select>
                    <div class="info-text">Pilih program studi untuk kurikulum ini</div>
                </div>
                
                <div class="form-group">
                    <label for="kur_tahun">Tahun Kurikulum <span class="required">*</span></label>
                    <input type="text" id="kur_tahun" name="kur_tahun" 
                           value="' . $kur_tahun . '" 
                           required 
                           placeholder="Contoh: 2024" 
                           pattern="[0-9]{4}"
                           maxlength="4"
                           title="Tahun harus 4 digit angka">
                    <div class="info-text">Format: YYYY (4 digit tahun)</div>
                </div>
                
                <div class="form-group">
                    <label for="kur_nama">Nama Kurikulum <span class="required">*</span></label>
                    <input type="text" id="kur_nama" name="kur_nama" 
                           value="' . $kur_nama . '" 
                           required 
                           placeholder="Contoh: Kurikulum 2024">
                    <div class="info-text">Nama kurikulum yang akan ditampilkan</div>
                </div>
                
                <div class="checkbox-group">
                    <input type="checkbox" id="kur_is_aktif" name="kur_is_aktif" value="1" checked>
                    <label for="kur_is_aktif" style="margin: 0; font-weight: normal;">
                        Status Aktif (Centang jika kurikulum ini sedang digunakan)
                    </label>
                </div>
                
                <button type="submit" class="btn-save">💾 Simpan Data Kurikulum</button>
            </form>
        </div>
        
        <a href="?modul=datamaster&submenu=kurikulum" class="back-link">← Kembali ke Daftar Data</a>
        
        <script>
            function validateFormKurikulum() {
                var prodi = document.getElementById("kur_prodi_id").value;
                var tahun = document.getElementById("kur_tahun").value;
                var nama = document.getElementById("kur_nama").value;
                
                // Validasi Program Studi
                if (prodi === "") {
                    alert("❌ Program Studi harus dipilih!");
                    return false;
                }
                
                // Validasi Tahun
                if (!/^[0-9]{4}$/.test(tahun)) {
                    alert("❌ Tahun harus terdiri dari 4 digit angka!");
                    return false;
                }
                
                var tahunNum = parseInt(tahun);
                var tahunSekarang = new Date().getFullYear();
                
                if (tahunNum < 2000 || tahunNum > (tahunSekarang + 5)) {
                    alert("❌ Tahun harus antara 2000 dan " + (tahunSekarang + 5) + "!");
                    return false;
                }
                
                // Validasi Nama
                if (nama.trim() === "") {
                    alert("❌ Nama Kurikulum tidak boleh kosong!");
                    return false;
                }
                
                return confirm("Apakah data yang diinput sudah benar?");
            }
        </script>
        ';
    } 
    
    // --- View Edit Kurikulum ---
    else if ($action == 'edit' && isset($_GET['id'])) {
        $id_edit = $conn->real_escape_string($_GET['id']);
        $sql_get_data = "SELECT * FROM kurikulum WHERE kurId = '$id_edit'";
        $res_data = $conn->query($sql_get_data);
        $data = $res_data->fetch_assoc();

        if (!$data) {
            echo "Data tidak ditemukan!";
        } else {
            $content_html = '
            <div class="centered-form">
                <form class="crud-form" method="POST" action=""> 
                    <input type="hidden" name="form_action" value="edit_kurikulum">
                    <input type="hidden" name="kur_id" value="'.$data['kurId'].'">
                    <h3 style="text-align: center;">Ubah Data Kurikulum</h3>
                    
                    <label>Program Studi:</label>
                    <select name="kur_prodi_id" required style="width: 100%; padding: 12px; border: 1px solid #ccc;">
                        ' . str_replace('value="'.$data['kurProdiId'].'"', 'value="'.$data['kurProdiId'].'" selected', $prodi_options) . '
                    </select><br>
                    
                    <label>Tahun:</label>
                    <input type="text" name="kur_tahun" value="'.$data['kurTahun'].'" required style="width: 100%; padding: 12px; border: 1px solid #ccc;"><br>
                    
                    <label>Nama Kurikulum:</label>
                    <input type="text" name="kur_nama" value="'.$data['kurNama'].'" required style="width: 100%; padding: 12px; border: 1px solid #ccc;"><br>
                    
                    <label style="margin-top: 20px;">
                       <input type="checkbox" name="kur_is_aktif" value="1" '.($data['kurIsAktif'] == 1 ? 'checked' : '').'> Status Aktif
                    </label><br>
                    
                    <button type="submit" class="btn-save">Simpan Perubahan</button>
                </form>
            </div>
            <a href="?modul=datamaster&submenu=kurikulum" class="back-link">← Kembali ke Daftar Data</a>';
        }
    } 
    
    
    
    else {
        // Tampilan List
        $sql_kurikulum = "SELECT k.kurId, k.kurNama, k.kurTahun, k.kurIsAktif, p.prodiNama 
                          FROM kurikulum k JOIN program_studi p ON k.kurProdiId = p.prodiId 
                          ORDER BY k.kurTahun DESC, p.prodiNama ASC";
        $result_kurikulum = $conn->query($sql_kurikulum);
        
        if ($result_kurikulum && $result_kurikulum->num_rows > 0) {
            $no = 1;
            $table_data = '';
            while($row = $result_kurikulum->fetch_assoc()) {
                $status = ($row['kurIsAktif'] == 1) ? 'Aktif' : 'Non-Aktif';
                $table_data .= '
                    <tr>
                        <td>' . $no++ . '.</td>
                        <td style="text-align: left; font-weight: bold;">' . htmlspecialchars($row['kurNama']) . '</td>
                        <td style="text-align: left;">' . htmlspecialchars($row['prodiNama']) . '</td>
                        <td>' . htmlspecialchars($row['kurTahun']) . '</td>
                        <td>' . $status . '</td>
                        <td>
                            <a href="?modul=datamaster&submenu=kurikulum&action=edit&id=' . $row['kurId'] . '" class="btn-aksi" style="color: blue;">Ubah</a> | 
                            <a href="?modul=datamaster&submenu=kurikulum&action=delete&id=' . $row['kurId'] . '" class="btn-aksi" style="color: red;" onclick="return confirm(\'Hapus ' . $row['kurNama'] . '?\')">Hapus</a>
                        </td>
                    </tr>';
            }
        } else {
            $table_data = '<tr><td colspan="6" style="text-align: center; padding: 5px;">**Tidak ditemukan data Kurikulum.**</td></tr>';
        }
        
        $content_html = ' 
            <h2 class="submenu-data-title">' . $submenu_title . '</h2> 
            ' . $add_button_datamaster . '
            <table class="data-kelas-table"> 
                ' . $table_header . ' 
                <tbody> 
                    ' . $table_data . ' 
                </tbody> 
            </table>';
    }
}







        
        else if ($submenu == 'matkul') {
    // --- 4. MATAKULIAH (Memakai filter form - Prodi Wajib) --- 
    $table_header = '<thead><tr class="header-table-datamaster-matkul"><th>No.</th><th>Kode MK</th><th>Nama Matakuliah</th><th>SKS</th><th>Semester</th><th>Kurikulum (Prodi)</th><th>Aksi</th></tr></thead>';
    
    // Cek jika action adalah 'add' (Form Tambah)
    if ($action == 'add') {
        $action_text = 'Tambah';
        
        // Get nilai dari URL untuk retensi jika ada error
        $mk_kode = isset($_GET['mk_kode']) ? htmlspecialchars($_GET['mk_kode']) : '';
        $mk_nama = isset($_GET['mk_nama']) ? htmlspecialchars($_GET['mk_nama']) : '';
        $mk_nama_asing = isset($_GET['mk_nama_asing']) ? htmlspecialchars($_GET['mk_nama_asing']) : '';
        
        // Ambil data Kurikulum untuk dropdown (dengan info Prodi)
        $kurikulum_options = '';
        $sql_kurikulum_list = "SELECT k.kurId, k.kurNama, k.kurTahun, p.prodiNama, p.prodiJenjang 
                               FROM kurikulum k 
                               JOIN program_studi p ON k.kurProdiId = p.prodiId 
                               WHERE k.kurIsAktif = 1 
                               ORDER BY p.prodiNama ASC, k.kurTahun DESC";
        $result_kurikulum_list = $conn->query($sql_kurikulum_list);
        if ($result_kurikulum_list && $result_kurikulum_list->num_rows > 0) {
            while($row = $result_kurikulum_list->fetch_assoc()) {
                $kur_display = htmlspecialchars($row['kurNama'] . ' - ' . $row['prodiJenjang'] . ' ' . $row['prodiNama'] . ' (' . $row['kurTahun'] . ')');
                $kurikulum_options .= '<option value="' . $row['kurId'] . '">' . $kur_display . '</option>';
            }
        }
        
        $content_html = '
        <style>
            .form-matkul-container {
                max-width: 700px;
                margin: 30px auto;
            }
            .form-matkul {
                background-color: #f9f9f9;
                padding: 30px;
                border: 1px solid #ddd;
                border-radius: 8px;
                box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            }
            .form-matkul h3 {
                text-align: center;
                color: var(--color-dark-blue);
                border-bottom: 2px solid var(--color-header-orange);
                padding-bottom: 10px;
                margin-top: 0;
                margin-bottom: 25px;
            }
            .form-row-matkul {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 15px;
                margin-bottom: 15px;
            }
            .form-group-full-matkul {
                grid-column: 1 / -1;
            }
            .form-group-matkul {
                margin-bottom: 15px;
            }
            .form-group-matkul label {
                display: block;
                margin-bottom: 8px;
                font-weight: bold;
                color: #555;
            }
            .form-group-matkul input, 
            .form-group-matkul select,
            .form-group-matkul textarea {
                width: 100%;
                padding: 12px;
                border: 1px solid #ccc;
                border-radius: 4px;
                box-sizing: border-box;
                font-size: 1em;
            }
            .form-group-matkul textarea {
                resize: vertical;
                min-height: 60px;
            }
            .form-group-matkul input:focus, 
            .form-group-matkul select:focus,
            .form-group-matkul textarea:focus {
                outline: none;
                border-color: var(--color-accent-blue);
                box-shadow: 0 0 5px rgba(0, 123, 255, 0.3);
            }
            .required {
                color: red;
            }
            .checkbox-group-matkul {
                display: flex;
                align-items: center;
                margin-top: 15px;
            }
            .checkbox-group-matkul input[type="checkbox"] {
                width: auto;
                margin-right: 10px;
            }
            .info-text-matkul {
                font-size: 0.85em;
                color: #666;
                margin-top: 5px;
            }
            .info-box-matkul {
                background-color: #e7f3ff;
                border-left: 4px solid #2196F3;
                padding: 12px;
                margin-bottom: 20px;
                border-radius: 4px;
            }
            .info-box-matkul ul {
                margin: 5px 0 0 20px;
                padding: 0;
            }
        </style>
        
        <h2 class="submenu-data-title">Tambah Data Matakuliah</h2>
        
        <div class="form-matkul-container">
            <form class="form-matkul" method="POST" action="" onsubmit="return validateFormMatakuliah()">
                <input type="hidden" name="form_action" value="add_matakuliah">
                <h3>Formulir Data Matakuliah</h3>
                
                <div class="info-box-matkul">
                    <strong>📌 Petunjuk Pengisian:</strong>
                    <ul>
                        <li>Kode MK: Minimal 3 karakter (contoh: TCE4106)</li>
                        <li>SKS: Jumlah Satuan Kredit Semester (1-6 SKS)</li>
                        <li>Semester: Semester dimana MK diajarkan (1-8)</li>
                    </ul>
                </div>
                
                <div class="form-group-matkul form-group-full-matkul">
                    <label for="mk_kur_id">Kurikulum <span class="required">*</span></label>
                    <select id="mk_kur_id" name="mk_kur_id" required>
                        <option value="">-- Pilih Kurikulum --</option>
                        ' . $kurikulum_options . '
                    </select>
                    <div class="info-text-matkul">Pilih kurikulum untuk matakuliah ini</div>
                </div>
                
                <div class="form-row-matkul">
                    <div class="form-group-matkul">
                        <label for="mk_kode">Kode Matakuliah <span class="required">*</span></label>
                        <input type="text" id="mk_kode" name="mk_kode" 
                               value="' . $mk_kode . '" 
                               required 
                               placeholder="Contoh: TCE4106"
                               minlength="3"
                               maxlength="20">
                        <div class="info-text-matkul">Min. 3 karakter</div>
                    </div>
                    
                    <div class="form-group-matkul">
                        <label for="mk_semester">Semester <span class="required">*</span></label>
                        <select id="mk_semester" name="mk_semester" required>
                            <option value="">-- Pilih --</option>
                            <option value="1">Semester 1</option>
                            <option value="2">Semester 2</option>
                            <option value="3">Semester 3</option>
                            <option value="4">Semester 4</option>
                            <option value="5">Semester 5</option>
                            <option value="6">Semester 6</option>
                            <option value="7">Semester 7</option>
                            <option value="8">Semester 8</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-group-matkul form-group-full-matkul">
                    <label for="mk_nama">Nama Matakuliah <span class="required">*</span></label>
                    <input type="text" id="mk_nama" name="mk_nama" 
                           value="' . $mk_nama . '" 
                           required 
                           placeholder="Contoh: Bahasa Inggris 2"
                           maxlength="100">
                </div>
                
                <div class="form-group-matkul form-group-full-matkul">
                    <label for="mk_nama_asing">Nama Asing (English)</label>
                    <input type="text" id="mk_nama_asing" name="mk_nama_asing" 
                           value="' . $mk_nama_asing . '" 
                           placeholder="Contoh: English 2"
                           maxlength="100">
                    <div class="info-text-matkul">Opsional - Nama matakuliah dalam bahasa Inggris</div>
                </div>
                
                <div class="form-row-matkul">
                    <div class="form-group-matkul">
                        <label for="mk_sks">Jumlah SKS <span class="required">*</span></label>
                        <select id="mk_sks" name="mk_sks" required>
                            <option value="">-- Pilih --</option>
                            <option value="1">1 SKS</option>
                            <option value="2">2 SKS</option>
                            <option value="3">3 SKS</option>
                            <option value="4">4 SKS</option>
                            <option value="5">5 SKS</option>
                            <option value="6">6 SKS</option>
                        </select>
                    </div>
                    
                    <div class="form-group-matkul">
                        <div class="checkbox-group-matkul" style="margin-top: 30px;">
                            <input type="checkbox" id="mk_is_aktif" name="mk_is_aktif" value="1" checked>
                            <label for="mk_is_aktif" style="margin: 0; font-weight: normal;">
                                Status Aktif
                            </label>
                        </div>
                    </div>
                </div>
                
                <button type="submit" class="btn-save">💾 Simpan Data Matakuliah</button>
            </form>
        </div>
        
        <a href="?modul=datamaster&submenu=matkul" class="back-link">← Kembali ke Daftar Data</a>
        
        <script>
            function validateFormMatakuliah() {
                var kurikulum = document.getElementById("mk_kur_id").value;
                var kode = document.getElementById("mk_kode").value;
                var nama = document.getElementById("mk_nama").value;
                var semester = document.getElementById("mk_semester").value;
                var sks = document.getElementById("mk_sks").value;
                
                // Validasi Kurikulum
                if (kurikulum === "") {
                    alert("❌ Kurikulum harus dipilih!");
                    return false;
                }
                
                // Validasi Kode
                if (kode.trim().length < 3) {
                    alert("❌ Kode Matakuliah minimal 3 karakter!");
                    return false;
                }
                
                // Validasi Nama
                if (nama.trim() === "") {
                    alert("❌ Nama Matakuliah tidak boleh kosong!");
                    return false;
                }
                
                // Validasi Semester
                if (semester === "") {
                    alert("❌ Semester harus dipilih!");
                    return false;
                }
                
                // Validasi SKS
                if (sks === "") {
                    alert("❌ Jumlah SKS harus dipilih!");
                    return false;
                }
                
                return confirm("Apakah data yang diinput sudah benar?\\n\\nKode: " + kode + "\\nNama: " + nama + "\\nSKS: " + sks + " SKS\\nSemester: " + semester);
            }
        </script>
        ';
    } 
    // --- View Edit Matakuliah ---
    else if ($action == 'edit' && isset($_GET['id'])) {
        $id_edit = $conn->real_escape_string($_GET['id']);
        $sql_get_data = "SELECT * FROM matakuliah WHERE mkId = '$id_edit'";
        $res_data = $conn->query($sql_get_data);
        $data = $res_data->fetch_assoc();

        if (!$data) {
            echo "Data tidak ditemukan!";
        } else {
             // Ambil list kurikulum
             $kurikulum_options = '';
             $sql_kurikulum_list = "SELECT k.kurId, k.kurNama, k.kurTahun, p.prodiNama, p.prodiJenjang 
                                    FROM kurikulum k 
                                    JOIN program_studi p ON k.kurProdiId = p.prodiId 
                                    ORDER BY p.prodiNama ASC, k.kurTahun DESC";
             $result_kurikulum_list = $conn->query($sql_kurikulum_list);
             if ($result_kurikulum_list && $result_kurikulum_list->num_rows > 0) {
                 while($row = $result_kurikulum_list->fetch_assoc()) {
                     $selected = ($row['kurId'] == $data['mkKurId']) ? 'selected' : '';
                     $kur_display = htmlspecialchars($row['kurNama'] . ' - ' . $row['prodiJenjang'] . ' ' . $row['prodiNama'] . ' (' . $row['kurTahun'] . ')');
                     $kurikulum_options .= '<option value="' . $row['kurId'] . '" '.$selected.'>' . $kur_display . '</option>';
                 }
             }

            $content_html = '
            <div class="centered-form">
                <form class="crud-form" method="POST" action=""> 
                    <input type="hidden" name="form_action" value="edit_matakuliah">
                    <input type="hidden" name="mk_id" value="'.$data['mkId'].'">
                    <h3 style="text-align: center;">Ubah Data Matakuliah</h3>
                    
                    <label>Kurikulum:</label>
                    <select name="mk_kur_id" required style="width: 100%; padding: 12px; border: 1px solid #ccc;">
                        ' . $kurikulum_options . '
                    </select><br>
                    
                    <label>Kode MK:</label>
                    <input type="text" name="mk_kode" value="'.$data['mkKode'].'" required style="width: 100%; padding: 12px; border: 1px solid #ccc;"><br>
                    
                    <label>Nama MK:</label>
                    <input type="text" name="mk_nama" value="'.$data['mkNama'].'" required style="width: 100%; padding: 12px; border: 1px solid #ccc;"><br>
                    
                    <label>Semester:</label>
                    <input type="number" name="mk_semester" value="'.$data['mkSemester'].'" required style="width: 100%; padding: 12px; border: 1px solid #ccc;"><br>
                    
                    <label>SKS:</label>
                    <input type="number" name="mk_sks" value="'.$data['mkSks'].'" required style="width: 100%; padding: 12px; border: 1px solid #ccc;"><br>
                    
                    <label style="margin-top: 20px;">
                       <input type="checkbox" name="mk_is_aktif" value="1" '.($data['mkIsAktif'] == 1 ? 'checked' : '').'> Status Aktif
                    </label><br>
                    
                    <button type="submit" class="btn-save">Simpan Perubahan</button>
                </form>
            </div>
            <a href="?modul=datamaster&submenu=matkul" class="back-link">← Kembali ke Daftar Data</a>';
        }
    } else {
        // Tampilan List dengan Filter
        $pencarian_form = generate_pencarian_form($conn, $prodi_options, $thn_akd_options, $smt_options_html, false);
        
        $table_data = '<tr><td colspan="7" style="text-align: center; padding: 20px;"></td></tr>';
        
        if ($is_filtered_search) {
            if (!empty($filter_prodi)) {
                $where_clause = " WHERE p.prodiId = '$filter_prodi' ";
                $sql_matkul = "SELECT mk.mkId, mk.mkKode, mk.mkNama, mk.mkSks, mk.mkSemester, k.kurNama, p.prodiNama 
                              FROM matakuliah mk 
                              JOIN kurikulum k ON mk.mkKurId = k.kurId 
                              JOIN program_studi p ON k.kurProdiId = p.prodiId " . $where_clause . " 
                              ORDER BY mk.mkSemester ASC, mk.mkKode ASC";
                $result_matkul = $conn->query($sql_matkul);
                
                if ($result_matkul && $result_matkul->num_rows > 0) {
                     $no = 1;
                     $table_data = '';
                     while($row = $result_matkul->fetch_assoc()) {
                         $kurikulum_info = htmlspecialchars($row['kurNama']) . ' (' . htmlspecialchars($row['prodiNama']) . ')';
                         $table_data .= '
                             <tr>
                                 <td>' . $no++ . '.</td>
                                 <td>' . htmlspecialchars($row['mkKode']) . '</td>
                                 <td style="text-align: left; font-weight: bold;">' . htmlspecialchars($row['mkNama']) . '</td>
                                 <td>' . htmlspecialchars($row['mkSks']) . '</td>
                                 <td>' . htmlspecialchars($row['mkSemester']) . '</td>
                                 <td style="text-align: left;">' . $kurikulum_info . '</td>
                                 <td>
                                     <a href="?modul=datamaster&submenu=matkul&action=edit&id=' . $row['mkId'] . '" class="btn-aksi" style="color: blue;">Ubah</a> | 
                                     <a href="?modul=datamaster&submenu=matkul&action=delete&id=' . $row['mkId'] . '" class="btn-aksi" style="color: red;" onclick="return confirm(\'Hapus ' . $row['mkNama'] . '?\')">Hapus</a>
                                 </td>
                             </tr>';
                     }
                } else {
                     $table_data = '<tr><td colspan="7" style="text-align: center; padding: 5px;">**Tidak ditemukan data Matakuliah untuk Program Studi yang dipilih.**</td></tr>';
                }
            } else {
                 $table_data = '<tr><td colspan="7" style="text-align: center; padding: 5px;">**Filter Program Studi harus dipilih.**</td></tr>';
            }
        }
        
        $content_html = ' 
            <h2 class="submenu-data-title">' . $submenu_title . '</h2> 
            ' . $pencarian_form . ' 
            <div class="data-actions">' . $add_button_datamaster . '</div> 
            <table class="data-kelas-table"> 
                ' . $table_header . ' 
                <tbody> 
                    ' . $table_data . ' 
                </tbody> 
            </table>';
    }
} 
        
    
        else if ($submenu == 'dosen') {
    // --- 5. DOSEN (Memakai filter form - Prodi Wajib) --- 
    $table_header = '<thead><tr class="header-table-datamaster-dosen"><th>No.</th><th>NIDN</th><th>Nama Dosen</th><th>Jenis Kelamin</th><th>Program Studi</th><th>Aksi</th></tr></thead>';
    $pencarian_form = generate_pencarian_form($conn, $prodi_options, $thn_akd_options, $smt_options_html, false);
    
    // Cek apakah action adalah 'add' (Form Tambah)
    if ($action == 'add') {
        // Ambil data Jurusan untuk dropdown
        $jurusan_options = '';
        $sql_jurusan_list = "SELECT jurId, jurNama FROM jurusan WHERE jurIsAktif = 1 ORDER BY jurNama ASC";
        $result_jurusan_list = $conn->query($sql_jurusan_list);
        if ($result_jurusan_list && $result_jurusan_list->num_rows > 0) {
            while($row = $result_jurusan_list->fetch_assoc()) {
                $jurusan_options .= '<option value="' . $row['jurId'] . '">' . htmlspecialchars($row['jurNama']) . '</option>';
            }
        }
        
        $content_html = '
        <style>
            .form-dosen-container {
                max-width: 600px;
                margin: 30px auto;
            }
            .form-dosen {
                background-color: #f9f9f9;
                padding: 30px;
                border: 1px solid #ddd;
                border-radius: 8px;
                box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            }
            .form-dosen h3 {
                text-align: center;
                color: var(--color-dark-blue);
                border-bottom: 2px solid var(--color-header-orange);
                padding-bottom: 10px;
                margin-top: 0;
                margin-bottom: 25px;
            }
            .form-row {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 15px;
                margin-bottom: 15px;
            }
            .form-group-full {
                grid-column: 1 / -1;
            }
            .form-group label {
                display: block;
                margin-bottom: 5px;
                font-weight: bold;
                color: #555;
            }
            .form-group input, 
            .form-group select {
                width: 100%;
                padding: 10px;
                border: 1px solid #ccc;
                border-radius: 4px;
                box-sizing: border-box;
                font-size: 1em;
            }
            .form-group input:focus, 
            .form-group select:focus {
                outline: none;
                border-color: var(--color-accent-blue);
                box-shadow: 0 0 5px rgba(0, 123, 255, 0.3);
            }
            .required {
                color: red;
            }
            .info-text {
                font-size: 0.85em;
                color: #666;
                margin-top: 5px;
            }
        </style>
        
        <h2 class="submenu-data-title">Tambah Data Dosen</h2>
        
        <div class="form-dosen-container">
            <form class="form-dosen" method="POST" action="" onsubmit="return validateFormDosen()">
                <input type="hidden" name="form_action" value="add_dosen">
                <h3>Formulir Data Dosen</h3>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="dsn_nidn">NIDN <span class="required">*</span></label>
                        <input type="text" id="dsn_nidn" name="dsn_nidn" required 
                               placeholder="Contoh: 0006058102" 
                               pattern="[0-9]{10}" 
                               maxlength="10"
                               value="' . (isset($_GET['dsn_nidn']) ? htmlspecialchars($_GET['dsn_nidn']) : '') . '">
                        <div class="info-text">10 digit angka</div>
                    </div>
                    
                    <div class="form-group">
                        <label for="dsn_jenis_kelamin">Jenis Kelamin <span class="required">*</span></label>
                        <select id="dsn_jenis_kelamin" name="dsn_jenis_kelamin" required>
                            <option value="L">Laki-laki</option>
                            <option value="P">Perempuan</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-group form-group-full">
                    <label for="dsn_nama">Nama Dosen <span class="required">*</span></label>
                    <input type="text" id="dsn_nama" name="dsn_nama" required 
                           placeholder="Contoh: Amelia Yolanda"
                           value="' . (isset($_GET['dsn_nama']) ? htmlspecialchars($_GET['dsn_nama']) : '') . '">
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="dsn_gelar_depan">Gelar Depan</label>
                        <input type="text" id="dsn_gelar_depan" name="dsn_gelar_depan" 
                               placeholder="Contoh: Dr.">
                        <div class="info-text">Opsional</div>
                    </div>
                    
                    <div class="form-group">
                        <label for="dsn_gelar_belakang">Gelar Belakang</label>
                        <input type="text" id="dsn_gelar_belakang" name="dsn_gelar_belakang" 
                               placeholder="Contoh: ST., MT">
                        <div class="info-text">Opsional</div>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="dsn_jur_id">Jurusan <span class="required">*</span></label>
                        <select id="dsn_jur_id" name="dsn_jur_id" required onchange="loadProdiByJurusan(this.value)">
                            <option value="">-- Pilih Jurusan --</option>
                            ' . $jurusan_options . '
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="dsn_prodi_id">Program Studi <span class="required">*</span></label>
                        <select id="dsn_prodi_id" name="dsn_prodi_id" required>
                            <option value="">-- Pilih Jurusan Dulu --</option>
                        </select>
                    </div>
                </div>
                
                <button type="submit" class="btn-save">💾 Simpan Data Dosen</button>
            </form>
        </div>
        
        <a href="?modul=datamaster&submenu=dosen" class="back-link">← Kembali ke Daftar Data</a>
        
        <script>
            function validateFormDosen() {
                var nidn = document.getElementById("dsn_nidn").value;
                var nama = document.getElementById("dsn_nama").value;
                var jurusan = document.getElementById("dsn_jur_id").value;
                var prodi = document.getElementById("dsn_prodi_id").value;
                
                // Validasi NIDN
                if (!/^[0-9]{10}$/.test(nidn)) {
                    alert("❌ NIDN harus terdiri dari 10 digit angka!");
                    return false;
                }
                
                // Validasi Nama
                if (nama.trim() === "") {
                    alert("❌ Nama Dosen tidak boleh kosong!");
                    return false;
                }
                
                // Validasi Jurusan
                if (jurusan === "") {
                    alert("❌ Jurusan harus dipilih!");
                    return false;
                }
                
                // Validasi Program Studi
                if (prodi === "") {
                    alert("❌ Program Studi harus dipilih!");
                    return false;
                }
                
                return confirm("Apakah data yang diinput sudah benar?");
            }
            
            // Fungsi untuk load Program Studi berdasarkan Jurusan yang dipilih
            function loadProdiByJurusan(jurId) {
                var prodiSelect = document.getElementById("dsn_prodi_id");
                prodiSelect.innerHTML = "<option value=\"\">-- Loading... --</option>";
                
                if (jurId === "") {
                    prodiSelect.innerHTML = "<option value=\"\">-- Pilih Jurusan Dulu --</option>";
                    return;
                }
                
                // Simulasi load prodi (dalam implementasi nyata, gunakan AJAX)
                // Untuk saat ini, kita load semua prodi dan filter di client side
                var allProdi = ' . json_encode(getProdiData($conn)) . ';
                
                prodiSelect.innerHTML = "<option value=\"\">-- Pilih Program Studi --</option>";
                
                allProdi.forEach(function(prodi) {
                    if (prodi.prodiJurId == jurId) {
                        var option = document.createElement("option");
                        option.value = prodi.prodiId;
                        option.text = prodi.prodiJenjang + " - " + prodi.prodiNama;
                        prodiSelect.appendChild(option);
                    }
                });
            }
        </script>
        ';
    } 
    // --- View Edit Dosen ---
    else if ($action == 'edit' && isset($_GET['id'])) {
        $id_edit = $conn->real_escape_string($_GET['id']);
        $sql_get_data = "SELECT * FROM dosen WHERE dsnNidn = '$id_edit'";
        $res_data = $conn->query($sql_get_data);
        $data = $res_data->fetch_assoc();

        if (!$data) {
            echo "Data tidak ditemukan!";
        } else {
             // Jurusan Options
             $jurusan_options_edit = '';
             $sql_jur = "SELECT jurId, jurNama FROM jurusan ORDER BY jurNama ASC";
             $res_jur = $conn->query($sql_jur);
             while($row = $res_jur->fetch_assoc()) {
                 $selected = ($row['jurId'] == $data['dsnJurId']) ? 'selected' : '';
                 $jurusan_options_edit .= '<option value="'.$row['jurId'].'" '.$selected.'>'.$row['jurNama'].'</option>';
             }

             // Prodi Options
             $prodi_options_edit = '';
             $sql_prod = "SELECT prodiId, prodiNama FROM program_studi WHERE prodiJurId = '".$data['dsnJurId']."'";
             $res_prod = $conn->query($sql_prod);
             while($row = $res_prod->fetch_assoc()) {
                 $selected = ($row['prodiId'] == $data['dsnProdiId']) ? 'selected' : '';
                 $prodi_options_edit .= '<option value="'.$row['prodiId'].'" '.$selected.'>'.$row['prodiNama'].'</option>';
             }

            $content_html = '
            <div class="centered-form">
                <form class="crud-form" method="POST" action=""> 
                    <input type="hidden" name="form_action" value="edit_dosen">
                    <input type="hidden" name="dsn_nidn" value="'.$data['dsnNidn'].'">
                    <h3 style="text-align: center;">Ubah Data Dosen (NIDN: '.$data['dsnNidn'].')</h3>
                    
                    <label>Nama Dosen:</label>
                    <input type="text" name="dsn_nama" value="'.$data['dsnNama'].'" required style="width: 100%; padding: 12px; border: 1px solid #ccc;"><br>
                    
                    <label>Jenis Kelamin:</label>
                    <select name="dsn_jenis_kelamin" required style="width: 100%; padding: 12px; border: 1px solid #ccc;">
                        <option value="L" '.($data['dsnJenisKelaminKode'] == 'L' ? 'selected' : '').'>Laki-laki</option>
                        <option value="P" '.($data['dsnJenisKelaminKode'] == 'P' ? 'selected' : '').'>Perempuan</option>
                    </select><br>

                    <label>Jurusan:</label>
                    <select name="dsn_jur_id" required style="width: 100%; padding: 12px; border: 1px solid #ccc;" onchange="/*implement ajax if needed for dynamic prodi*/ alert(\'Prodi tidak otomatis update di mode edit ini, mohon simpan dulu, atau fitur ini perlu js tambahan\')">
                        '.$jurusan_options_edit.'
                    </select><br>

                    <label>Program Studi:</label>
                    <select name="dsn_prodi_id" required style="width: 100%; padding: 12px; border: 1px solid #ccc;">
                        '.$prodi_options_edit.'
                    </select><br>
                    
                    <button type="submit" class="btn-save">Simpan Perubahan</button>
                </form>
            </div>
            <a href="?modul=datamaster&submenu=dosen" class="back-link">← Kembali ke Daftar Data</a>';
        }
    } else {
        // Tampilan Daftar Dosen
        $table_data = '<tr><td colspan="6" style="text-align: center; padding: 20px;"></td></tr>';
        $result_dosen = null; 
        
        if ($is_filtered_search) {
            if (!empty($filter_prodi)) {
                $where_clause = " WHERE p.prodiId = '$filter_prodi' ";
                $sql_dosen = "SELECT d.dsnNidn, d.dsnNama, d.dsnJenisKelaminKode, d.dsnGelarDepan, d.dsnGelarBelakang, p.prodiNama, p.prodiJenjang
                              FROM dosen d 
                              LEFT JOIN program_studi p ON d.dsnProdiId = p.prodiId " . $where_clause . " 
                              ORDER BY d.dsnNama ASC";
                $result_dosen = $conn->query($sql_dosen);
                
                if ($result_dosen && $result_dosen->num_rows > 0) {
                     $no = 1;
                     $table_data = '';
                     while($row = $result_dosen->fetch_assoc()) {
                         $nama_lengkap = '';
                         if (!empty($row['dsnGelarDepan'])) {
                             $nama_lengkap .= htmlspecialchars($row['dsnGelarDepan']) . ' ';
                         }
                         $nama_lengkap .= htmlspecialchars($row['dsnNama']);
                         if (!empty($row['dsnGelarBelakang'])) {
                             $nama_lengkap .= ', ' . htmlspecialchars($row['dsnGelarBelakang']);
                         }
                         
                         $jenis_kelamin = ($row['dsnJenisKelaminKode'] == 'L') ? 'Laki-laki' : 'Perempuan';
                         $prodi_info = htmlspecialchars($row['prodiJenjang']) . ' - ' . htmlspecialchars($row['prodiNama']);
                         
                         $table_data .= '
                             <tr>
                                 <td>' . $no++ . '.</td>
                                 <td>' . htmlspecialchars($row['dsnNidn']) . '</td>
                                 <td style="text-align: left; font-weight: bold;">' . $nama_lengkap . '</td>
                                 <td>' . $jenis_kelamin . '</td>
                                 <td style="text-align: left;">' . $prodi_info . '</td>
                                 <td>
                                     <a href="?modul=datamaster&submenu=dosen&action=edit&id=' . $row['dsnNidn'] . '" class="btn-aksi" style="color: blue;">Ubah</a> | 
                                     <a href="?modul=datamaster&submenu=dosen&action=delete&id=' . $row['dsnNidn'] . '" class="btn-aksi" style="color: red;" onclick="return confirm(\'Hapus ' . $row['dsnNama'] . '?\')">Hapus</a>
                                 </td>
                             </tr>';
                     }
                } else {
                     $table_data = '<tr><td colspan="6" style="text-align: center; padding: 5px;">**Tidak ditemukan data Dosen untuk Program Studi yang dipilih.**</td></tr>';
                }
            } else {
                 $table_data = '<tr><td colspan="6" style="text-align: center; padding: 5px;">**Filter Program Studi harus dipilih.**</td></tr>';
            }
        }
        
         $content_html = ' 
             <h2 class="submenu-data-title">' . $submenu_title . '</h2> 
             ' . $pencarian_form . ' 
             <div class="data-actions">' . $add_button_datamaster . '</div> 
             <table class="data-kelas-table"> 
                 ' . $table_header . ' 
                 <tbody> 
                     ' . $table_data . ' 
                 </tbody> 
             </table>';
        } 
        } else if ($submenu == 'mahasiswa') {
    // --- 6. MAHASISWA (Memakai filter form - Prodi Wajib) --- 
    $table_header = '<thead><tr class="header-table-datamaster-mahasiswa"><th>No.</th><th>NIM</th><th>Nama Mahasiswa</th><th>Program Studi</th><th>Dosen PA</th><th>Status Akademik</th><th>Aksi</th></tr></thead>';
    
    // Cek jika action adalah 'add' (Form Tambah) 
    if ($action == 'add') {
        $action_text = 'Tambah';
        
        // Get nilai dari URL untuk retensi jika ada error
        $mhs_nim = isset($_GET['mhs_nim']) ? htmlspecialchars($_GET['mhs_nim']) : '';
        $mhs_nama = isset($_GET['mhs_nama']) ? htmlspecialchars($_GET['mhs_nama']) : '';
        $mhs_tempat_lahir = isset($_GET['mhs_tempat_lahir']) ? htmlspecialchars($_GET['mhs_tempat_lahir']) : '';
        $mhs_tgl_lahir = isset($_GET['mhs_tgl_lahir']) ? htmlspecialchars($_GET['mhs_tgl_lahir']) : '';
        
        $content_html = ' 
            <div class="centered-form">
                <form class="crud-form" method="POST" action="" style="max-width: 600px;"> 
                    <input type="hidden" name="form_action" value="add_mahasiswa">
                    <h3 style="text-align: center;">Formulir Tambah Data Mahasiswa</h3>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                        <div style="grid-column: 1 / -1;">
                            <label for="mhs_nim">NIM: <span style="color: red;">*</span></label>
                            <input type="text" id="mhs_nim" name="mhs_nim" value="' . $mhs_nim . '" required placeholder="Contoh: 2211071001" pattern="[0-9]{10}" title="NIM harus 10 digit angka" style="width: 100%; padding: 12px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;">
                        </div>
                        
                        <div style="grid-column: 1 / -1;">
                            <label for="mhs_nama">Nama Lengkap: <span style="color: red;">*</span></label>
                            <input type="text" id="mhs_nama" name="mhs_nama" value="' . $mhs_nama . '" required placeholder="Contoh: Ahmad Rizki" style="width: 100%; padding: 12px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;">
                        </div>
                        
                        <div>
                            <label for="mhs_tempat_lahir">Tempat Lahir: <span style="color: red;">*</span></label>
                            <input type="text" id="mhs_tempat_lahir" name="mhs_tempat_lahir" value="' . $mhs_tempat_lahir . '" required placeholder="Contoh: Padang" style="width: 100%; padding: 12px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;">
                        </div>
                        
                        <div>
                            <label for="mhs_tgl_lahir">Tanggal Lahir: <span style="color: red;">*</span></label>
                            <input type="date" id="mhs_tgl_lahir" name="mhs_tgl_lahir" value="' . $mhs_tgl_lahir . '" required style="width: 100%; padding: 12px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;">
                        </div>
                        
                        <div>
                            <label for="mhs_jenis_kelamin">Jenis Kelamin: <span style="color: red;">*</span></label>
                            <select id="mhs_jenis_kelamin" name="mhs_jenis_kelamin" required style="width: 100%; padding: 12px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;">
                                <option value="">-- Pilih --</option>
                                <option value="L">Laki-laki</option>
                                <option value="P">Perempuan</option>
                            </select>
                        </div>
                        
                        <div>
                            <label for="mhs_kode_kelas">Kode Kelas: <span style="color: red;">*</span></label>
                            <select id="mhs_kode_kelas" name="mhs_kode_kelas" required style="width: 100%; padding: 12px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;">
                                <option value="">-- Pilih --</option>
                                <option value="A">A</option>
                                <option value="B">B</option>
                                <option value="C">C</option>
                                <option value="D">D</option>
                            </select>
                        </div>
                        
                        <div style="grid-column: 1 / -1;">
                            <label for="mhs_jur_id">Jurusan: <span style="color: red;">*</span></label>
                            <select id="mhs_jur_id" name="mhs_jur_id" required style="width: 100%; padding: 12px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;">
                                <option value="">-- Pilih Jurusan --</option>
                                ' . $jurusan_options . '
                            </select>
                        </div>
                        
                        <div style="grid-column: 1 / -1;">
                            <label for="mhs_prodi_id">Program Studi: <span style="color: red;">*</span></label>
                            <select id="mhs_prodi_id" name="mhs_prodi_id" required style="width: 100%; padding: 12px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;">
                                <option value="">-- Pilih Program Studi --</option>
                                ' . $prodi_options . '
                            </select>
                        </div>
                        
                        <div style="grid-column: 1 / -1;">
                            <label for="mhs_pa_id">Dosen Pembimbing Akademik:</label>
                            <select id="mhs_pa_id" name="mhs_pa_id" style="width: 100%; padding: 12px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;">
                                ' . $dosen_pa_options . '
                            </select>
                        </div>
                        
                        <div>
                            <label for="mhs_sts_akademik">Status Akademik: <span style="color: red;">*</span></label>
                            <select id="mhs_sts_akademik" name="mhs_sts_akademik" required style="width: 100%; padding: 12px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;">
                                <option value="">-- Pilih Status --</option>
                                <option value="A" selected>Aktif</option>
                                <option value="C">Cuti</option>
                                <option value="L">Lulus</option>
                                <option value="D">DO (Drop Out)</option>
                                <option value="K">Keluar</option>
                                <option value="M">Meninggal</option>
                            </select>
                        </div>
                        
                        <div>
                            <label for="mhs_sms_aktif">Semester Aktif: <span style="color: red;">*</span></label>
                            <select id="mhs_sms_aktif" name="mhs_sms_aktif" required style="width: 100%; padding: 12px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;">
                                <option value="">-- Pilih --</option>
                                <option value="1">Semester 1</option>
                                <option value="2">Semester 2</option>
                                <option value="3">Semester 3</option>
                                <option value="4">Semester 4</option>
                                <option value="5">Semester 5</option>
                                <option value="6">Semester 6</option>
                                <option value="7">Semester 7</option>
                                <option value="8">Semester 8</option>
                            </select>
                        </div>
                    </div>
                    
                    <p style="color: #6c757d; font-size: 0.9em; margin-top: 20px;">
                        <span style="color: red;">*</span> Wajib diisi<br>
                        <small>Foto mahasiswa dapat diupload setelah data tersimpan melalui menu Ubah.</small>
                    </p>
                    
                    <button type="submit" class="btn-save">Simpan Data Mahasiswa</button>
                </form>
            </div>
            <a href="?modul=datamaster&submenu=' . $submenu . '" class="back-link">← Kembali ke Daftar Data</a>';
    } 
    // --- View Edit Mahasiswa ---
    else if ($action == 'edit' && isset($_GET['id'])) {
        $id_edit = $conn->real_escape_string($_GET['id']);
        $sql_get_data = "SELECT * FROM mahasiswa WHERE mhsNim = '$id_edit'";
        $res_data = $conn->query($sql_get_data);
        $data = $res_data->fetch_assoc();

        if (!$data) {
            echo "Data tidak ditemukan!";
        } else {
             // Jurusan Options
             $jurusan_options_edit = '';
             $sql_jur = "SELECT jurId, jurNama FROM jurusan ORDER BY jurNama ASC";
             $res_jur = $conn->query($sql_jur);
             while($row = $res_jur->fetch_assoc()) {
                 $selected = ($row['jurId'] == $data['mhsJurId']) ? 'selected' : '';
                 $jurusan_options_edit .= '<option value="'.$row['jurId'].'" '.$selected.'>'.$row['jurNama'].'</option>';
             }
             
             // Prodi Options
             $prodi_options_edit = '';
             $sql_prod = "SELECT prodiId, prodiNama FROM program_studi WHERE prodiJurId = '".$data['mhsJurId']."'";
             $res_prod = $conn->query($sql_prod);
             while($row = $res_prod->fetch_assoc()) {
                 $selected = ($row['prodiId'] == $data['mhsProdiId']) ? 'selected' : '';
                 $prodi_options_edit .= '<option value="'.$row['prodiId'].'" '.$selected.'>'.$row['prodiNama'].'</option>';
             }
             
             // PA Options
             $pa_options_edit = '<option value="">-- Pilih --</option>';
             $sql_pa = "SELECT dsnNidn, dsnNama FROM dosen ORDER BY dsnNama ASC";
             $res_pa = $conn->query($sql_pa);
             while($row = $res_pa->fetch_assoc()) {
                 $selected = ($row['dsnNidn'] == $data['mhsPaId']) ? 'selected' : '';
                 $pa_options_edit .= '<option value="'.$row['dsnNidn'].'" '.$selected.'>'.$row['dsnNama'].'</option>';
             }

            $content_html = '
            <div class="centered-form">
                <form class="crud-form" method="POST" action=""> 
                    <input type="hidden" name="form_action" value="edit_mahasiswa">
                    <input type="hidden" name="mhs_nim" value="'.$data['mhsNim'].'">
                    <h3 style="text-align: center;">Ubah Data Mahasiswa (NIM: '.$data['mhsNim'].')</h3>
                    
                    <label>Nama Mahasiswa:</label>
                    <input type="text" name="mhs_nama" value="'.$data['mhsNama'].'" required style="width: 100%; padding: 12px; border: 1px solid #ccc;"><br>
                    
                     <label>Tempat Lahir:</label>
                    <input type="text" name="mhs_tempat_lahir" value="'.$data['mhsTempatLahir'].'" required style="width: 100%; padding: 12px; border: 1px solid #ccc;"><br>
                    
                    <label>Tanggal Lahir:</label>
                    <input type="date" name="mhs_tgl_lahir" value="'.$data['mhsTglLahir'].'" required style="width: 100%; padding: 12px; border: 1px solid #ccc;"><br>
                    
                    <label>Jenis Kelamin:</label>
                    <select name="mhs_jenis_kelamin" required style="width: 100%; padding: 12px; border: 1px solid #ccc;">
                        <option value="L" '.($data['mhsJenisKelamin'] == 'L' ? 'selected' : '').'>Laki-laki</option>
                        <option value="P" '.($data['mhsJenisKelamin'] == 'P' ? 'selected' : '').'>Perempuan</option>
                    </select><br>

                    <label>Jurusan:</label>
                    <select name="mhs_jur_id" required style="width: 100%; padding: 12px; border: 1px solid #ccc;">
                        '.$jurusan_options_edit.'
                    </select><br>

                    <label>Program Studi:</label>
                    <select name="mhs_prodi_id" required style="width: 100%; padding: 12px; border: 1px solid #ccc;">
                        '.$prodi_options_edit.'
                    </select><br>
                    
                    <label>Kode Kelas:</label>
                    <select name="mhs_kode_kelas" required style="width: 100%; padding: 12px; border: 1px solid #ccc;">
                        <option value="A" '.($data['mhsKodeKelas'] == 'A' ? 'selected' : '').'>A</option>
                        <option value="B" '.($data['mhsKodeKelas'] == 'B' ? 'selected' : '').'>B</option>
                        <option value="C" '.($data['mhsKodeKelas'] == 'C' ? 'selected' : '').'>C</option>
                        <option value="D" '.($data['mhsKodeKelas'] == 'D' ? 'selected' : '').'>D</option>
                    </select><br>
                    
                    <label>Status Akademik:</label>
                    <select name="mhs_sts_akademik" required style="width: 100%; padding: 12px; border: 1px solid #ccc;">
                        <option value="A" '.($data['mhsStsAkademik'] == 'A' ? 'selected' : '').'>Aktif</option>
                        <option value="L" '.($data['mhsStsAkademik'] == 'L' ? 'selected' : '').'>Lulus</option>
                        <option value="C" '.($data['mhsStsAkademik'] == 'C' ? 'selected' : '').'>Cuti</option>
                         <option value="D" '.($data['mhsStsAkademik'] == 'D' ? 'selected' : '').'>DO</option>
                        <option value="K" '.($data['mhsStsAkademik'] == 'K' ? 'selected' : '').'>Keluar</option>
                         <option value="M" '.($data['mhsStsAkademik'] == 'M' ? 'selected' : '').'>Meninggal</option>
                    </select><br>
                    
                    <label>Semester Aktif:</label>
                     <select name="mhs_sms_aktif" required style="width: 100%; padding: 12px; border: 1px solid #ccc;">
                         <option value="1" '.($data['mhsSmsAktif'] == 1 ? 'selected' : '').'>1</option>
                         <option value="2" '.($data['mhsSmsAktif'] == 2 ? 'selected' : '').'>2</option>
                         <option value="3" '.($data['mhsSmsAktif'] == 3 ? 'selected' : '').'>3</option>
                         <option value="4" '.($data['mhsSmsAktif'] == 4 ? 'selected' : '').'>4</option>
                         <option value="5" '.($data['mhsSmsAktif'] == 5 ? 'selected' : '').'>5</option>
                         <option value="6" '.($data['mhsSmsAktif'] == 6 ? 'selected' : '').'>6</option>
                         <option value="7" '.($data['mhsSmsAktif'] == 7 ? 'selected' : '').'>7</option>
                         <option value="8" '.($data['mhsSmsAktif'] == 8 ? 'selected' : '').'>8</option>
                    </select><br>
                    
                    <label>Dosen PA:</label>
                    <select name="mhs_pa_id" style="width: 100%; padding: 12px; border: 1px solid #ccc;">
                        '.$pa_options_edit.'
                    </select><br>
                    
                    <button type="submit" class="btn-save">Simpan Perubahan</button>
                    <br><br>
                </form>
            </div>
            <a href="?modul=datamaster&submenu=mahasiswa" class="back-link">← Kembali ke Daftar Data</a>';
        }
    } else {
        // Tampilan List dengan Filter
        // Tampilan List dengan Filter
        // Kita membangun ulang form pencarian secara manual agar bisa menambahkan filter Kelas, 
        // tapi TETAP menyertakan Tahun dan Semester seperti permintaan user.
        
        $filter_kelas_dm = isset($_GET['filter_kelas_dm']) ? $conn->real_escape_string($_GET['filter_kelas_dm']) : '';
        
        // Logika Opsi Kelas (muncul jika Prodi dan Semester dipilih, atau minimal Prodi)
        // Disini kita ambil kelas berdasarkan prodi. Idealnya juga berdasarkan tahun/semester aktif, tapi fetch all for prodi is safer for data master filtering.
        $kelas_options_dm = '';
        if (!empty($filter_prodi)) {
             // Ambil semua kelas di prodi ini
             $sql_k_dm = "SELECT klsId, klsNama FROM kelas WHERE klsProdiId = '$filter_prodi' ORDER BY klsNama ASC";
             $res_k_dm = $conn->query($sql_k_dm);
             if ($res_k_dm) {
                 while($r = $res_k_dm->fetch_assoc()) {
                     $sel = ($r['klsId'] == $filter_kelas_dm) ? 'selected' : '';
                     $kelas_options_dm .= '<option value="'.$r['klsId'].'" '.$sel.'>'.$r['klsNama'].'</option>';
                 }
             }
        }

        $pencarian_form = '
        <div class="filter-box">
            <form method="GET" action="">
                <input type="hidden" name="modul" value="datamaster">
                <input type="hidden" name="submenu" value="mahasiswa">
                
                <div class="filter-group">
                    <label>Prodi:</label>
                    <select name="filter_prodi" onchange="this.form.submit()">
                        <option value="">-- Pilih Prodi --</option>
                        ' . str_replace('value="'.$filter_prodi.'"', 'value="'.$filter_prodi.'" selected', $prodi_options) . '
                    </select>
                </div>

                <div class="filter-group">
                    <label>Tahun:</label>
                    <select name="filter_tahun" onchange="this.form.submit()">
                        <option value="">-- Pilih Tahun --</option>
                        ' . str_replace('value="'.$filter_tahun.'"', 'value="'.$filter_tahun.'" selected', $thn_akd_options) . '
                    </select>
                </div>
                
                <div class="filter-group">
                    <label>Semester:</label>
                    <select name="filter_semester_id" onchange="this.form.submit()">
                         <option value="">-- Pilih Semester --</option>
                         ' . $smt_options_html . '
                    </select>
                </div>
                
                <div class="filter-group">
                    <label>Kelas (Opsional):</label>
                    <select name="filter_kelas_dm" onchange="this.form.submit()">
                        <option value="">-- Semua Kelas --</option>
                        ' . $kelas_options_dm . '
                    </select>
                </div>
                
                <button type="submit" class="btn-filter">Cari</button>
            </form>
        </div>';
        
        $table_data = '<tr><td colspan="7" style="text-align: center; padding: 20px;"></td></tr>';
        
        if ($is_filtered_search) {
            if (!empty($filter_prodi)) {
                $where_clause = " WHERE m.mhsProdiId = '$filter_prodi' ";
                
                // Tambahan Filter Kelas
                $join_kelas = "";
                if (!empty($filter_kelas_dm)) {
                    $join_kelas = " JOIN kelas_mahasiswa km ON m.mhsNim = km.klsmhsMhsNim ";
                    $where_clause .= " AND km.klsmhsKlsId = '$filter_kelas_dm' ";
                }

                $sql_mahasiswa = "SELECT m.mhsNim, m.mhsNama, m.mhsStsAkademik, p.prodiNama, d.dsnNama AS PaNama
                                  FROM mahasiswa m 
                                  JOIN program_studi p ON m.mhsProdiId = p.prodiId 
                                  LEFT JOIN dosen d ON m.mhsPaId = d.dsnNidn " 
                                  . $join_kelas 
                                  . $where_clause . " ORDER BY m.mhsNama ASC";
                $result_mahasiswa = $conn->query($sql_mahasiswa);
                
                if ($result_mahasiswa && $result_mahasiswa->num_rows > 0) {
                     $no = 1;
                     $table_data = '';
                     $status_akademik_map = ['A' => 'Aktif', 'L' => 'Lulus', 'C' => 'Cuti', 'D' => 'DO', 'K' => 'Keluar', 'M' => 'Meninggal'];
                     while($row = $result_mahasiswa->fetch_assoc()) {
                         $pa_nama = htmlspecialchars($row['PaNama'] ?? 'N/A');
                         $status_akademik = $status_akademik_map[$row['mhsStsAkademik']];
                         $table_data .= '
                             <tr>
                                 <td>' . $no++ . '.</td>
                                 <td>' . htmlspecialchars($row['mhsNim']) . '</td>
                                 <td style="text-align: left; font-weight: bold;">' . htmlspecialchars($row['mhsNama']) . '</td>
                                 <td>' . htmlspecialchars($row['prodiNama']) . '</td>
                                 <td>' . $pa_nama . '</td>
                                 <td>' . $status_akademik . '</td>
                                 <td>
                                     <a href="?modul=datamaster&submenu=mahasiswa&action=edit&id=' . $row['mhsNim'] . '" class="btn-aksi" style="color: blue;">Ubah</a> | 
                                     <a href="?modul=datamaster&submenu=mahasiswa&action=delete&id=' . $row['mhsNim'] . '" class="btn-aksi" style="color: red;" onclick="return confirm(\'Hapus data mahasiswa ' . $row['mhsNama'] . '?\')">Hapus</a>
                                 </td>
                             </tr>';
                     }
                } else {
                     $table_data = '<tr><td colspan="7" style="text-align: center; padding: 5px;">**Tidak ditemukan data Mahasiswa untuk Program Studi yang dipilih.**</td></tr>';
                }
            } else {
                 $table_data = '<tr><td colspan="7" style="text-align: center; padding: 5px;">**Filter Program Studi harus dipilih.**</td></tr>';
            }
        }
        
         $content_html = ' 
             <h2 class="submenu-data-title">' . $submenu_title . '</h2> 
             ' . $pencarian_form . ' 
             <div class="data-actions">' . $add_button_datamaster . '</div> 
             <table class="data-kelas-table"> 
                 ' . $table_header . ' 
                 <tbody> 
                     ' . $table_data . ' 
                 </tbody> 
             </table>';
    }
}






		
    } else {
        // Submenu default Data Master
        $content_html = ' 
             <h2>Daftar Submenu Modul Data Master</h2> 
             <hr> 
             <div class="icon-grid" style="grid-template-columns: repeat(3, 1fr);">
                 <a href="?modul=datamaster&submenu=jurusan" class="icon-box"><img src="https://tse4.mm.bing.net/th/id/OIP.rXiE5qINMWpbmXKrcZ8uUgHaHa?pid=Api&P=0&h=180" alt="Data Jurusan"><span>Data Jurusan</span></a>
                 <a href="?modul=datamaster&submenu=prodi" class="icon-box"><img src="https://tse2.mm.bing.net/th/id/OIP.egDhwXxkocv-DaQhn0BdAAHaHa?pid=Api&P=0&h=180" alt="Data Program Studi"><span>Data Program Studi</span></a>
                 <a href="?modul=datamaster&submenu=kurikulum" class="icon-box"><img src="https://tse4.mm.bing.net/th/id/OIP.Ob4T0MgEI6cABqgJYY1X7wHaEK?pid=Api&P=0&h=180" alt="Data Kurikulum"><span>Data Kurikulum</span></a>
                 <a href="?modul=datamaster&submenu=matkul" class="icon-box"><img src="https://tse2.mm.bing.net/th/id/OIP.nIx3JlegRzaELD5D498kQgHaHa?pid=Api&P=0&h=180" alt="Data Matakuliah"><span>Data Matakuliah</span></a>
                 <a href="?modul=datamaster&submenu=dosen" class="icon-box"><img src="https://tse2.mm.bing.net/th/id/OIP.fbE2WSNoqn1zdfIm-u2SwgHaE8?pid=Api&P=0&h=180" alt="Data Dosen"><span>Data Dosen</span></a>
                 <a href="?modul=datamaster&submenu=mahasiswa" class="icon-box"><img src="https://tse4.mm.bing.net/th/id/OIP.wDcPxVF70tHEu65AYfLEMQHaG4?pid=Api&P=0&h=180" alt="Data Mahasiswa"><span>Data Mahasiswa</span></a>
             </div>';
    }
	
// --- LOGIKA UNTUK MODUL LOGIN (TAMPILAN LOGIN) ---
} else if ($modul == 'user') {
    $judul_halaman = "Modul User";
    $breadcrumb_path = '<a href="?modul=beranda">Beranda</a> <span class="breadcrumb-separator">  ►  </span> User';
    
    if ($submenu == 'ubah_password') {
        // =======================================================
        // === SUBMENU UBAH PASSWORD
        // =======================================================
        $submenu_title_display = 'Ubah Password';
        
        $content_html = '
        <style>
            .password-form-container {
                max-width: 500px;
                margin: 30px auto;
                background-color: #fff;
                padding: 30px;
                border-radius: 8px;
                box-shadow: 0 4px 15px rgba(0,0,0,0.1);
                border: 1px solid #ddd;
            }
            .password-form-container h3 {
                text-align: center;
                color: var(--color-dark-blue);
                border-bottom: 2px solid var(--color-header-orange);
                padding-bottom: 10px;
                margin-bottom: 25px;
            }
            .form-group { margin-bottom: 20px; position: relative; }
            .form-group label { display: block; margin-bottom: 8px; font-weight: bold; color: #555; }
            .form-group input[type="password"], .form-group input[type="text"] {
                width: 100%; padding: 12px; border: 1px solid #ccc; border-radius: 4px;
                box-sizing: border-box; font-size: 1em; transition: border-color 0.3s;
            }
            .form-group input:focus { border-color: var(--color-accent-blue); outline: none; }
            .btn-submit-password {
                padding: 12px 20px; background-color: var(--color-btn-save);
                color: white; border: none; border-radius: 5px; cursor: pointer;
                width: 100%; font-size: 1.1em; margin-top: 10px; transition: background 0.3s;
            }
            .btn-submit-password:hover { background-color: #0056b3; }
            .toggle-password {
                margin-top: 10px; cursor: pointer; display: flex; align-items: center; font-size: 0.9em; color: #555;
            }
            .toggle-password input { margin-right: 8px; }
            .alert-box {
                padding: 15px; margin-bottom: 20px; border-radius: 4px; border: 1px solid transparent;
            }
            .alert-success { background-color: #d4edda; color: #155724; border-color: #c3e6cb; }
            .alert-error { background-color: #f8d7da; color: #721c24; border-color: #f5c6cb; }
        </style>

        <h2 class="submenu-data-title">' . $submenu_title_display . '</h2>
        
        <div class="password-form-container">
            <h3>🛡️ Ganti Password</h3>
            
            ' . ((isset($_GET['msg'])) ? 
                '<div class="alert-box ' . ((isset($_GET['status']) && $_GET['status'] == 'success') ? 'alert-success' : 'alert-error') . '">' . htmlspecialchars($_GET['msg']) . '</div>' 
                : '') . '
            
            <div class="info-box" style="background-color: #e7f3ff; border-left: 4px solid #2196F3; padding: 12px; margin-bottom: 20px; border-radius: 4px;">
                <p style="margin: 0;"><strong>Info:</strong> Gunakan password yang aman dengan kombinasi huruf dan angka.</p>
            </div>

            <form method="POST" action="" onsubmit="return validatePassword()">
                <input type="hidden" name="form_action" value="ubah_password">
                
                <div class="form-group">
                    <label for="password_lama">Password Lama <span style="color: red;">*</span></label>
                    <input type="password" id="password_lama" name="password_lama" required placeholder="Masukkan password lama">
                </div>
                
                <div class="form-group">
                    <label for="password_baru">Password Baru <span style="color: red;">*</span></label>
                    <input type="password" id="password_baru" name="password_baru" required placeholder="Masukkan password baru" minlength="3">
                </div>
                
                <div class="form-group">
                    <label for="konfirmasi_password">Konfirmasi Password Baru <span style="color: red;">*</span></label>
                    <input type="password" id="konfirmasi_password" name="konfirmasi_password" required placeholder="Masukkan ulang password baru" minlength="3">
                </div>
                
                <div class="toggle-password">
                    <label style="font-weight: normal; margin: 0; cursor: pointer;">
                        <input type="checkbox" onclick="togglePasswordVisibility()"> Tampilkan Password
                    </label>
                </div>
                
                <button type="submit" class="btn-submit-password">💾 Simpan Password Baru</button>
            </form>
        </div>
        
        <script>
            function validatePassword() {
                var pBaru = document.getElementById("password_baru").value;
                var pKonf = document.getElementById("konfirmasi_password").value;
                
                if (pBaru !== pKonf) {
                    alert("❌ Password baru dan konfirmasi tidak cocok!");
                    return false;
                }
                if (pBaru.length < 3) {
                    alert("❌ Password minimal 3 karakter!");
                    return false;
                }
                return confirm("Yakin ingin mengubah password?");
            }

            function togglePasswordVisibility() {
                var ids = ["password_lama", "password_baru", "konfirmasi_password"];
                ids.forEach(function(id) {
                    var x = document.getElementById(id);
                    if (x.type === "password") {
                        x.type = "text";
                    } else {
                        x.type = "password";
                    }
                });
            }
        </script>
        ';
        
    } else if ($submenu == 'kotak_pesan') {
        // =======================================================
        // === SUBMENU KOTAK PESAN (Placeholder)
        // =======================================================
        $content_html = '
        <h2 class="submenu-data-title">Kotak Pesan</h2>
        <div style="padding: 40px; text-align: center; background-color: #f9f9f9; border: 1px solid #ddd; border-radius: 8px; margin: 20px 0;">
            <img src="https://cdn-icons-png.flaticon.com/512/561/561127.png" alt="Pesan" style="width: 100px; opacity: 0.5; margin-bottom: 20px;">
            <h3 style="color: #666;">Fitur Kotak Pesan</h3>
            <p style="color: #999; margin-top: 10px;">Fitur ini akan segera tersedia</p>
        </div>
        ';
        
    } else {
        // Submenu default User
        $content_html = '
            <h2>Daftar Submenu Modul User</h2> 
            <hr> 
            <div class="icon-grid" style="grid-template-columns: repeat(2, 1fr);">
                <a href="?modul=user&submenu=kotak_pesan" class="icon-box">
                    <img src="https://cdn-icons-png.flaticon.com/512/561/561127.png" alt="Kotak Pesan">
                    <span>Kotak Pesan</span>
                </a>
                <a href="?modul=user&submenu=ubah_password" class="icon-box">
                    <img src="https://cdn-icons-png.flaticon.com/512/2889/2889676.png" alt="Ubah Password">
                    <span>Ubah Password</span>
                </a>
            </div>';
    }   
    } else if ($modul == 'register') {
        $judul_halaman = "Daftar Akun Baru";
        $breadcrumb_path = '» Daftar Akun';
        
        $register_message_html = '';
        if (!empty($login_message)) {
            $register_message_html = '<div class="login-message">'.$login_message.'</div>';
        }

        $content_html = '
            <style>
                body.login-body {
                     background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
                     min-height: 100vh;
                     display: flex;
                     align-items: center;
                     justify-content: center;
                     font-family: \'Segoe UI\', Tahoma, Geneva, Verdana, sans-serif;
                }
                 .login-message {
                    padding: 12px; margin-bottom: 20px; border-radius: 6px; font-weight: 500; text-align: center;
                    color: #721c24; background-color: #f8d7da; border: 1px solid #f5c6cb;
                    font-size: 0.95em;
                }
                .login-form-container {
                    width: 100%;
                    max-width: 420px; 
                    margin: 0 auto; 
                    background-color: #ffffff; 
                    padding: 40px;
                    border-radius: 12px; 
                    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05); 
                    border: 1px solid rgba(0,0,0,0.05);
                }
                .login-form-container h2 {
                    text-align: center; color: #2c3e50; 
                    margin-top: 0; margin-bottom: 25px;
                    font-size: 1.8em; letter-spacing: -0.5px;
                }
                .form-header-logo {
                    text-align: center; margin-bottom: 20px;
                }
                .form-header-logo img {
                    width: 60px; height: auto;
                }
                 .login-form-container label { 
                     display: block; margin-bottom: 6px; margin-top: 18px; 
                     font-weight: 600; color: #34495e; font-size: 0.9em;
                 }
                .login-form-container input[type="text"], .login-form-container input[type="password"] {
                    width: 100%; padding: 12px 15px; 
                    border: 1px solid #dfe6e9; border-radius: 6px; 
                    box-sizing: border-box; font-size: 0.95em;
                    transition: border-color 0.3s ease, box-shadow 0.3s ease;
                    background-color: #fafafa;
                }
                .login-form-container input:focus {
                    outline: none;
                    border-color: #3498db;
                    background-color: #fff;
                    box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
                }
                .btn-login { 
                    padding: 14px; 
                    background: linear-gradient(to right, #3498db, #2980b9); 
                    color: white; border: none;
                    border-radius: 6px; cursor: pointer; margin-top: 35px; 
                    font-weight: 700; width: 100%; font-size: 1em;
                    letter-spacing: 0.5px;
                    transition: transform 0.2s ease, box-shadow 0.2s ease;
                }
                .btn-login:hover {
                    background: linear-gradient(to right, #2980b9, #3498db);
                    transform: translateY(-1px);
                    box-shadow: 0 4px 12px rgba(41, 128, 185, 0.2);
                }
                .nav-bar-top, .main-menu-left { display: none !important; }
                .main-container { width: 100%; max-width: 100%; margin: 50px auto; background: transparent; box-shadow: none; border: none;}
                .content-area { flex-direction: column; background: transparent; }
                #content-right { padding: 0; display: flex; justify-content: center; align-items: center; background: transparent;}
                small { color: #7f8c8d; }
            </style>
    
            <div class="login-form-container">
                <div class="form-header-logo">
                    <!-- Placeholder Logo -->
                    <svg fill="#3498db" width="48px" height="48px" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                </div>
                <h2>Buat Akun Baru</h2>
                ' . $register_message_html . '
    
                <form method="POST" action="?modul=register" onsubmit="return validateRegister()"> 
                    <input type="hidden" name="register" value="1">
                    
                    <label for="reg_username">Username (NIM/NIDN)</label>
                    <input type="text" id="reg_username" name="reg_username" required placeholder="Contoh: 2101092028" autocomplete="off" value="' . (isset($_POST['reg_username']) ? htmlspecialchars($_POST['reg_username']) : '') . '">
                    <small style="color: grey; display: block; margin-top: 2px;">Gunakan NIDN untuk Dosen.</small>
                    
                    <label for="reg_nama">Nama Lengkap</label>
                    <input type="text" id="reg_nama" name="reg_nama" required placeholder="Nama Lengkap Anda" autocomplete="off" value="' . (isset($_POST['reg_nama']) ? htmlspecialchars($_POST['reg_nama']) : '') . '">
                    
                    <label for="reg_nohp">No. Handphone (WA)</label>
                    <input type="text" id="reg_nohp" name="reg_nohp" placeholder="Contoh: 08123456789" autocomplete="off" value="' . (isset($_POST['reg_nohp']) ? htmlspecialchars($_POST['reg_nohp']) : '') . '">
                    
                    <label for="reg_password">Password</label>
                    <input type="password" id="reg_password" name="reg_password" required placeholder="Buat Password" autocomplete="off">
                    
                    <label for="reg_password_confirm">Konfirmasi Password</label>
                    <input type="password" id="reg_password_confirm" name="reg_password_confirm" required placeholder="Ulangi Password" autocomplete="off">
                    
                    <div style="margin-top: 10px;">
                        <label style="font-weight: normal; margin: 0; cursor: pointer; display: flex; align-items: center;">
                            <input type="checkbox" onclick="toggleRegisterPassword()" style="width: auto; margin-right: 8px;"> Tampilkan Password
                        </label>
                    </div>
                    
                    <button type="submit" class="btn-login">DAFTAR SEKARANG</button>
                    
                    <div style="text-align: center; margin-top: 20px;">
                        Sudah punya akun? <a href="?modul=login" style="color: var(--color-dark-blue); font-weight: bold;">Login di sini</a>
                    </div>
                </form>
            </div>
            
            <script>
                function validateRegister() {
                    var pass = document.getElementById("reg_password").value;
                    var confirm = document.getElementById("reg_password_confirm").value;
                    var user = document.getElementById("reg_username").value;
                    
                    if (user.length < 5) {
                         alert("Username/NIM minimal 5 karakter!");
                         return false;
                    }

                    if (pass !== confirm) {
                        alert("Password dan Konfirmasi Password tidak sama!");
                        return false;
                    }
                    if (pass.length < 3) {
                         alert("Password minimal 3 karakter!");
                         return false;
                    }
                    return true;
                }

                function toggleRegisterPassword() {
                    var p = document.getElementById("reg_password");
                    var pc = document.getElementById("reg_password_confirm");
                    if (p.type === "password") {
                        p.type = "text";
                        pc.type = "text";
                    } else {
                        p.type = "password";
                        pc.type = "password";
                    }
                }
            </script>
        ';
        
    } else if ($modul == 'login') {
    $judul_halaman = "Login Sistem";
    $breadcrumb_path = '» Login';
    
    // Siapkan pesan yang akan ditampilkan
    $login_message_html = '';
    if (!empty($login_message)) {
        // Pesan error dari post login
        $login_message_html = '<div class="login-message">'.$login_message.'</div>';
    } else if ($status_message) {
        // Pesan sukses/info dari URL (misalnya setelah setup atau logout)
         $login_message_html = '<div class="login-message" style="color: #0c5460; background-color: #d1ecf1; border-color: #bee5eb;">'.$status_message.'</div>';
    }
    
    $content_html = '
        <style>
             .login-message {
                padding: 10px;
                margin-bottom: 15px;
                border-radius: 4px;
                font-weight: bold;
                text-align: center;
                color: #721c24; /* Default error color */
                background-color: #f8d7da;
                border: 1px solid #f5c6cb;
            }
            .login-form-container {
                max-width: 400px;
                margin: 50px auto;
                background-color: #fff;
                padding: 30px;
                border-radius: 8px;
                box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
                border: 1px solid #ddd;
            }
            .login-form-container h2 {
                text-align: center;
                color: var(--color-dark-blue);
                border-bottom: 2px solid var(--color-header-orange);
                padding-bottom: 10px;
                margin-top: 0;
                margin-bottom: 20px;
            }
             .login-form-container label {
                display: block; 
                margin-bottom: 5px;
                margin-top: 15px; 
                font-weight: bold; 
                color: #555; 
            }
            .login-form-container input[type="text"], 
            .login-form-container input[type="password"] {
                width: 100%; 
                padding: 12px;
                border: 1px solid #ccc; 
                border-radius: 4px; 
                box-sizing: border-box; 
                font-size: 1em; 
            }
            .btn-login { 
                padding: 12px 20px; 
                background-color: var(--color-btn-save); 
                color: white; 
                border: none; 
                border-radius: 5px; 
                cursor: pointer; 
                margin-top: 30px; 
                font-weight: bold;
                width: 100%; 
                font-size: 1.1em;
            }
            
            /* Sembunyikan Nav Bar dan Sidebar di halaman login */
            .nav-bar-top, .main-menu-left { display: none !important; }
            .main-container { width: 480px; margin: 100px auto; }
            .content-area { flex-direction: column; }
            #content-right { padding: 0; }
            .header-top { justify-content: center; } /* Pusatkan header logo */
            .header-top > img:last-child { display: none; } /* Sembunyikan Logo Kemendikbud di login */
            .header-logo-title { flex-direction: column; }
            .header-logo-title img { margin-bottom: 10px; margin-right: 0; }
            .header-title-box { text-align: center; }
            
            /* Memaksa elemen agar sejajar dengan tampilan login */
            .main-container { 
                align-items: center; 
                height: auto; 
                min-height: auto;
            }
            /* Styling untuk content-right saat login agar form berada di tengah */
            #content-right {
                display: flex;
                justify-content: center;
                align-items: center;
            }
            .breadcrumb-home, .top-bar-content {
                display: none; /* Sembunyikan breadcrumb saat login */
            }
        </style>

        <div class="login-form-container">
            <h2>Login</h2>
            ' . $login_message_html . '

            <form method="POST" action="?modul=login"> 
                <input type="hidden" name="login" value="1">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required placeholder="Username" autocomplete="off" value="' . (isset($_POST['username']) ? htmlspecialchars($_POST['username']) : '') . '"><br>
                
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required placeholder="Password" autocomplete="off"><br>
                
                <button type="submit" class="btn-login">MASUK</button>
                
                <div style="text-align: center; margin-top: 20px;">
                    Belum punya akun? <a href="?modul=register" style="color: var(--color-dark-blue); font-weight: bold;">Daftar Akun</a>
                </div>
            </form>
        </div>
        ';
	
	} else {
    // SUBMENU User
   
    $content_html = '
			<h2>Daftar Submenu Modul user</h2> 
            <hr> 
            <div class="icon-grid" style="grid-template-columns: repeat(2, 1fr);">
				<a href="?modul=user&submenu=Kotak Pesan" class="icon-box"><img src="https://media.istockphoto.com/id/520241473/id/vektor/kartu-laporan.jpg?s=612x612&w=0&k=20&c=LW6nLvGYSg_wq64QNabGE4pKGR6Hlpxvor-Z64J5bbM=" alt="Kotak Pesan"><span>Kotak Pesan</span></a>
                <a href="?modul=user&submenu=Ubah Password" class="icon-box"><img src="https://media.istockphoto.com/id/520241473/id/vektor/kartu-laporan.jpg?s=612x612&w=0&k=20&c=LW6nLvGYSg_wq64QNabGE4pKGR6Hlpxvor-Z64J5bbM=" alt="Ubah Password"><span>Ubah Password</span></a>
			</div>';
	}
// Menentukan judul halaman untuk ditampilkan di tag <title>
if ($submenu && isset(${$modul . '_menu_items'}[$submenu])) {
    $judul_halaman = ${$modul . '_menu_items'}[$submenu];
    $breadcrumb_path = '<a href ="?modul=beranda">Beranda</a> <span class="breadcrumb-separator">  ►  </span> <a href="?modul=' . $modul . '">' . ucfirst($modul) . '</a> <span class="breadcrumb-separator">  ►  </span> ' . $judul_halaman;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $judul_halaman ?> - Sistem Informasi Akademik</title>
    <style>
        /* POLITEKNIK NEGERI PADANG */
        
        /* Variabel CSS Anda yang Diberi Warna Khusus PNP */
        :root {
            --color-dark-blue: #1F3A60; /* Biru Tua PNP */
            --color-light-blue: #E0F2F7;
            --color-text-dark: #333333;
            --color-accent-blue: #007bff;
            --color-white: #FFFFFF;
            --color-border: #cccccc;
            
            /* Warna Khusus PNP */
            --color-bg-luar: #F8F8F8; /* Background Putih Abu */
            --color-main-container: #F8F8F8; /* Background Putih Abu */
            --color-header-orange: #E0A045; /* Oranye Nav Bar */
            --color-menu-utama-title: #D9534F; /* Merah untuk "Menu Utama :" */
            /* WARNA CRUD & Lain-lain */
            --color-btn-add: #28a745;
            --color-btn-edit: #ffc107;
            --color-btn-delete: #dc3545;
            --color-btn-save: #007bff;
            --color-btn-cari: #17a2b8; /* Cyan untuk Cari */
            --color-pencarian-bg: #f5f5f5; 
            
            /* Header Tabel Warna Baru */
            --header-kelas: #d35400; /* Orange Pumpkin */
            --header-matkul: #d35400; /* Orange Pumpkin */
            --header-dosen: #d35400; /* Orange Pumpkin */
            --header-mhs: #d35400; /* Orange Pumpkin */
            --header-nilai: #d35400; /* Orange Pumpkin */
            --header-datamaster-jurusan: #d35400; /* Orange Pumpkin */
            --header-datamaster-prodi: #d35400; /* Orange Pumpkin */
            --header-datamaster-kurikulum: #d35400; /* Orange Pumpkin */
            --header-datamaster-matkul: #d35400; /* Orange Pumpkin */
            --header-datamaster-dosen: #d35400; /* Orange Pumpkin */
            --header-datamaster-mahasiswa: #d35400; /* Orange Pumpkin */
        }
        
        body {
            font-family: Arial, sans-serif;
            background-color: var(--color-bg-luar); 
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            min-height: 100vh;
        }
        .main-container {
            width: 1000px;
            background-color: var(--color-main-container); 
            margin-top: 20px;
            margin-bottom: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
            border: 1px solid #B0B0B0;
        }
        /* 2. Bagian Header Atas) */
        .header-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 20px;
            background-color: var(--color-white);
            border-bottom: 1px solid #ccc;
        }
        .header-logo-title {
            display: flex;
            align-items: center;
        }
        .header-logo-title img {
            height: 50px; /* Sesuaikan ukuran logo */
            margin-right: 15px;
        }
        .header-title-box .title {
            font-size: 1.5em;
            font-weight: bold;
            color: var(--color-dark-blue);
        }
        .header-title-box .subtitle {
            font-size: 1.2em;
            color: #555;
        }
        /* 3. Navigation Bar Beranda/Logout (Warna Oranye) */
        .nav-bar-top {
            background-color: var(--color-header-orange); 
            background-image: linear-gradient(to right, #E0A045, #FFC173); /* Gradien Oranye */
            padding: 5px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-weight: bold;
            color: var(--color-white);
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.2);
            font-size: 0.9em;
        }
        .nav-bar-top a {
            color: var(--color-white);
            text-decoration: none;
            margin-right: 15px;
        }
        
        /* 4. Area Konten Utama (Menu Kiri dan Content Kanan) */
        .content-area {
            display: flex;
            flex-direction: row;
            flex-grow: 1;
        }
        /* 5. Menu Utama Kiri (Sidebar PNP Style) */
        .main-menu-left {
            width: 200px;
            padding: 10px;
            background-color: var(--color-white); 
            border-right: 1px solid #ccc;
            flex-shrink: 0;
        }
        .main-menu-left h3 {
            font-size: 1em;
            color: var(--color-menu-utama-title); 
            margin-top: 0;
            padding-bottom: 5px;
            border-bottom: 1px dashed var(--color-menu-utama-title);
        }
        .main-menu-left ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .main-menu-left li a {
            display: block;
            padding: 5px 0;
            text-decoration: none;
            color: var(--color-dark-blue);
            font-size: 0.95em;
        }
        
        .main-menu-left li a.active {
            font-weight: bold;
            color: var(--color-accent-blue);
        }
        /* Submenu Container (dipertahankan dari sistem lama Anda) */
        .submenu-container {
            /* Logic PHP akan mengatur display: block; jika menu aktif */
            display: none; 
            margin-top: 0;
            padding-top: 0;
            padding-bottom: 5px;
        }
        .submenu-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .submenu-list a {
            padding: 5px 0 5px 15px; 
            font-size: 0.9em;
            color: #555;
        }
        .submenu-list a.submenu-active {
            font-weight: bold;
            color: var(--color-dark-blue);
            background-color: #f0f0f0;
        }
        
        /* 6. Konten Utama Kanan */
        #content-right {
            flex-grow: 1;
            padding: 10px 20px;
            background-color: var(--color-white);
        }
        /* Breadcrumb/Top Bar */
        .breadcrumb-home, .top-bar-content {
            color: #555;
            font-size: 0.9em;
            padding: 5px 0;
            border-bottom: 1px solid #ddd;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .breadcrumb-home a, .top-bar-content a {
            color: var(--color-dark-blue);
            text-decoration: none;
        }
        .breadcrumb-separator {
            margin: 0 5px;
            color: #ccc;
        }
        
        /* Status Message */
        .status-message {
            padding: 15px; 
            margin-bottom: 20px; 
            border: 1px solid transparent; 
            border-radius: 4px;
            font-weight: bold;
        }
        
        /* Dashboard Icons */
        .icon-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr); 
            gap: 20px;
            margin-top: 20px;
        }
        .icon-box {
            background-color: var(--color-white);
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            text-align: center;
            text-decoration: none;
            color: var(--color-dark-blue);
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            transition: all 0.2s;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }
        .icon-box:hover {
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            transform: translateY(-2px);
        }
        .icon-box span {
            display: block;
            font-weight: 400; 
            font-size: 0.9em;
            margin-top: 5px;
        }
        .icon-box img {
            width: 50px; 
            height: 50px;
            margin-bottom: 5px;
        }
        
        /* Pencarian Box (Sesuai Screenshot) */
        .pencarian-box {
            background-color: var(--color-pencarian-bg);
            border: 1px solid #ddd;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        .pencarian-box table {
            width: 100%;
            border-collapse: collapse;
        }
        .pencarian-box .label-td {
            width: 150px;
            font-weight: bold;
            color: var(--color-dark-blue);
        }
        .pencarian-box td {
            padding: 5px 10px 5px 0;
        }
        .pencarian-box select, .pencarian-box input[type="text"] {
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 3px;
            margin-right: 10px;
        }
        .btn-cari {
            padding: 8px 15px;
            background-color: var(--color-btn-cari);
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
        }
        /* Tabel Data */
        .submenu-data-title {
            color: var(--color-dark-blue);
            border-bottom: 2px solid var(--color-dark-blue);
            padding-bottom: 5px;
            margin-bottom: 15px;
        }
        .data-actions {
            margin-bottom: 15px;
            text-align: right;
        }
        .btn-add {
            padding: 8px 15px;
            background-color: var(--color-btn-add);
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
        }
        .data-kelas-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.9em;
        }
        .data-kelas-table th, .data-kelas-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }
        .data-kelas-table thead th {
            color: var(--color-white);
        }
        
        /* Warna Header Tabel Per Modul/Submenu */
        .header-table-kelas th { background-color: var(--header-kelas); } 
        .header-table-mk th { background-color: var(--header-matkul); } 
        .header-table-dosen th { background-color: var(--header-dosen); } 
        .header-table-mhs th { background-color: var(--header-mhs); } 
        .header-table-nilai th { background-color: var(--header-nilai); } 
        .header-table-datamaster-jurusan th { background-color: var(--header-datamaster-jurusan); }
        .header-table-datamaster-prodi th { background-color: var(--header-datamaster-prodi); }
        .header-table-datamaster-kurikulum th { background-color: var(--header-datamaster-kurikulum); }
        .header-table-datamaster-matkul th { background-color: var(--header-datamaster-matkul); }
        .header-table-datamaster-dosen th { background-color: var(--header-datamaster-dosen); }
        .header-table-datamaster-mahasiswa th { background-color: var(--header-datamaster-mahasiswa); }
        .data-kelas-table tr:nth-child(even) { background-color: #f9f9f9; }
        .btn-aksi {
            text-decoration: none;
            font-weight: bold;
            margin: 0 3px;
        }
        
        /* Form CRUD */
        .centered-form { 
            display: flex; 
            justify-content: center;
            margin-top: 20px; 
        }
        .crud-form { 
            background-color: #f9f9f9; 
            padding: 30px;
            border: 1px solid #ddd; 
            border-radius: 8px; 
            max-width: 450px; 
            width: 100%; 
        }
        .crud-form label { 
            display: block; 
            margin-bottom: 5px;
            margin-top: 15px; 
            font-weight: bold; 
            color: #555; 
        }
        .crud-form input[type="text"], .crud-form select { 
            width: 100%; 
            padding: 12px;
            border: 1px solid #ccc; 
            border-radius: 4px; 
            box-sizing: border-box; 
            font-size: 1em; 
        }
        .btn-save { 
            padding: 10px 20px; 
            background-color: var(--color-btn-save); 
            color: white; 
            border: none; 
            border-radius: 5px; 
            cursor: pointer; 
            margin-top: 25px; 
            font-weight: bold;
            width: 100%; 
        }
        .back-link {
            display: block;
            margin-top: 20px;
            font-weight: bold;
            color: var(--color-dark-blue);
            text-decoration: none;
        }
        
        /* 7. Footer */
        .main-footer {
            text-align: center;
            padding: 10px;
            font-size: 0.8em;
            color: #666;
            background-color: var(--color-main-container);
            border-top: 1px solid #ccc;
            border-radius: 0 0 5px 5px;
        }

        /* Form dengan Grid Layout */
.crud-form input[type="date"] {
    width: 100%;
    padding: 12px;
    border: 1px solid #ccc;
    border-radius: 4px;
    box-sizing: border-box;
    font-size: 1em;
}

.crud-form small {
    font-size: 0.85em;
    color: #666;
}
        
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Skrip untuk membuka submenu berdasarkan URL
            var activeModul = '<?= $active_menu ?>';
            if (activeModul && activeModul !== 'beranda') {
                var submenuContainer = document.getElementById('submenu-' + activeModul);
                if (submenuContainer) {
                    submenuContainer.style.display = 'block';
                }
                
                // Tandai link modul utama sebagai active
                var activeLink = document.querySelector('.main-menu-left a[href*="?modul=' + activeModul + '"]');
                if (activeLink) {
                    activeLink.classList.add('active');
                }
            }
        });
    </script>
</head>
<body>
    
    <div class="main-container">
        <?php if ($modul !== 'login'): ?>
        <div class="header-top">
            <div class="header-logo-title">
                <img src="https://tse1.mm.bing.net/th/id/OIP.VdVq8jZb-olTmojQg59-owHaHa?pid=Api&P=0&h=180" alt="Logo PNP"> 
                <div class="header-title-box">
                    <span class="title">Sistem Informasi Akademik</span>
                    <span class="subtitle">Politeknik Negeri Padang</span>
                </div>
            </div>
            <img src="https://e7.pngegg.com/pngimages/717/704/png-clipart-logo-kementerian-pendidikan-dan-kebudayaan-indonesia-graphics-ministry-of-education-and-culture-logo-bendera-indonesia-logo-high-school.png" alt="Logo Kemendikbud" style="height: 50px;">
        </div>
        <div class="nav-bar-top">
            <div>
                <a href="?modul=beranda">Beranda</a> | <a href="?modul=logout">Logout</a>
            </div>
            <div>
                [ <?= $nama_user ?> ] | <span>[ <?= $periode ?> ]</span>
            </div>
        </div>
        <?php else: ?>
        <div class="header-top">
             <div class="header-logo-title">
                <img src="https://tse1.mm.bing.net/th/id/OIP.VdVq8jZb-olTmojQg59-owHaHa?pid=Api&P=0&h=180" alt="Logo PNP"> 
                <div class="header-title-box">
                
                    <span class="subtitle">POLITEKNIK NEGERI PADANG</span>
                </div>
            </div>
        </div>
        <?php endif; ?>
        
        <div class="content-area">
            
            <?php if ($modul !== 'login'): ?>
            <div class="main-menu-left">
                <h3>Menu Utama :</h3>
                <ul>
                    <li><a href="?modul=beranda" class="<?= ($active_menu == 'beranda') ? 'active' : '' ?>">Beranda</a></li>
                    
                    
                    <li><a href="?modul=prakuliah" class="<?= ($active_menu == 'prakuliah') ? 'active' : '' ?>">Prakuliah</a></li>
                    <?= $prakuliah_menu_html ?> 
                    
                    <li><a href="?modul=perkuliahan" class="<?= ($active_menu == 'perkuliahan') ? 'active' : '' ?>">Perkuliahan</a></li>
                    <?= $perkuliahan_menu_html ?>
                    
                    <li><a href="?modul=pascakuliah" class="<?= ($active_menu == 'pascakuliah') ? 'active' : '' ?>">Pascakuliah</a></li>
                    <?= $pascakuliah_menu_html ?>
             
                    
                    <li><a href="?modul=datamaster" class="<?= ($active_menu == 'datamaster') ? 'active' : '' ?>">Data Master</a></li>
                    <?= $datamaster_menu_html ?>
                    
                    <li><a href="?modul=user" class="<?= ($active_menu == 'user') ? 'active' : '' ?>">User</a></li>
                    <?= $user_menu_html ?>
                </ul>
            </div>
            <?php endif; ?> 
            
            <div id="content-right">
                
                <?php if ($active_menu == 'beranda'): ?>
                    <div class="breadcrumb-home">
                        <?= $breadcrumb_path ?>
                    </div>
                <?php elseif ($modul !== 'login'): ?>
                    <div class="top-bar-content">
                         <div class="breadcrumb">
                             <?= $breadcrumb_path ?>
                         </div>
                         <div class="user-info-content">
                             Periode: <?= $periode ?>
                         </div>
                    </div>
                <?php endif; ?>
                
                <?php if ($status_message && $modul !== 'login'): ?>
                <div class="status-message" style="
                <?php 
                    if($status_type == 'success') {
                        echo 'color: #155724; background-color: #d4edda; border-color: #c3e6cb;';
                    } else if ($status_type == 'error') {
                        echo 'color: #721c24; background-color: #f8d7da; border-color: #f5c6cb;';
                    } else {
                        echo 'color: #856404; background-color: #fff3cd; border-color: #ffeeba;';
                    }
                ?>">
                    <strong></strong> <?= $status_message ?>
                </div>
                <?php endif; ?>
                
                <?= $content_html ?>
                
            </div>
        </div>
        <div class="main-footer">
            &copy; RAHMA DANI - 2025 Politeknik Negeri Padang
        </div>
    </div>
</body>
</html>