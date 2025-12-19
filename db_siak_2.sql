-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 19, 2025 at 09:58 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_siak2`
--

-- --------------------------------------------------------

--
-- Table structure for table `dosen`
--

CREATE TABLE `dosen` (
  `dsnNidn` char(10) NOT NULL,
  `dsnJurId` smallint(6) DEFAULT NULL,
  `dsnProdiId` smallint(6) DEFAULT NULL,
  `dsnNama` varchar(50) NOT NULL DEFAULT '',
  `dsnJenisKelaminKode` enum('L','P') DEFAULT 'L',
  `dsnGelarDepan` varchar(50) DEFAULT NULL,
  `dsnGelarBelakang` varchar(50) DEFAULT NULL,
  `dsnFoto` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `dosen`
--

INSERT INTO `dosen` (`dsnNidn`, `dsnJurId`, `dsnProdiId`, `dsnNama`, `dsnJenisKelaminKode`, `dsnGelarDepan`, `dsnGelarBelakang`, `dsnFoto`) VALUES
('0006058102', 3, 13, 'Amelia Yolanda', 'P', '', 'ST., MT', '0006058102.jpg'),
('0006058302', 3, 13, 'Popy Maria', 'P', '', 'ST.,MT', '0006058302.jpg'),
('0007016802', 3, 13, 'Yustini', 'P', '', 'SST., MT', '0007016802.jpg'),
('0007039304', 3, 13, 'Ummul Khair', 'P', '', 'S.T., M.T', '0007039304.jpg'),
('0007047602', 3, 13, 'Ramiati', 'P', '', 'ST.,SST.,M.Kom', '0007047602.jpg'),
('0007087701', 3, 7, 'Dikky Chandra', 'L', '', 'ST.,MT', '0007087701.jpg'),
('0009046904', 3, 13, 'Aprinal Adila Asril', 'L', '', 'ST.,M.Kom', '0009046904.jpg'),
('0009067701', 3, 7, 'Silfia Rifka', 'P', '', 'SST, MT.', '0009067701.jpg'),
('0010049210', 3, 13, 'Sahid Ridho', 'L', '', 'S.T., M.T.', '0010049210.jpg'),
('0011097202', 3, 7, 'Andi Ahmad Dahlan putra', 'L', '', 'ST., M.Eng', '0011097202.jpg'),
('0012067402', 3, 13, 'Sri Yusnita', 'P', '', 'ST.,MT', '0012067402.jpg'),
('0014057908', 7, 17, 'Sabriandi Erdian', 'L', '', 'SS.,M.Hum', '0014057908.jpg'),
('0016046801', 3, 13, 'Sri Nita', 'P', 'Dra.', 'M.Pd', '0016046801.jpg'),
('0018069304', 3, 7, 'Deri Latika Herda', 'P', '', 'S.T., M.T.', '0018069304.jpg'),
('0019107605', 3, 7, 'Rikki Vitria', 'L', 'Ir.', 'S.ST., M.Sc. Eng.', '0019107605.jpg'),
('0022027208', 3, 13, 'Lifwarda', 'P', '', 'ST., M.Kom', '0022027208.jpg'),
('0022057705', 3, 13, 'Firdaus', 'L', '', 'ST., MT', '0022057705.jpg'),
('0024096804', 3, 13, 'Uzma Septima', 'L', '', 'ST.,M.Eng', '0024096804.jpg'),
('0024127804', 3, 13, 'Firdaus', 'L', '', 'ST., MT', '0024127804.jpg'),
('0025016906', 3, 7, 'Zurnawita', 'P', '', 'ST.,MT', '0025016906.jpg'),
('0025117803', 3, 7, 'Ihsan Lumasa Rimra', 'L', '', 'SST.,M.Sc DECN', '0025117803.jpg'),
('0029046406', 3, 13, 'Afrizal Yuhanef', 'L', 'Dr.', 'ST.,M.Kom', '0029046406.jpg'),
('0029097604', 3, 13, 'Vera Veronica', 'P', '', 'ST.,MT', '0029097604.jpg'),
('0029107506', 3, 13, 'Ratna Dewi', 'P', '', 'SST., M.Kom', '0029107506.jpg'),
('0030046603', 3, 13, 'Yulindon', 'L', '', 'S.T., M.Kom.', '0030046603.jpg'),
('0030116506', 3, 13, 'Nasrul', 'L', 'Dr.', 'ST.,M.Kom', '0030116506.jpg'),
('0215039501', 3, 7, 'Muhammad Putra Pamungkas', 'L', '', 'S.T., M.T.', '0215039501.jpg'),
('1004038801', 3, 13, 'Siska Aulia', 'P', '', 'ST.,MT', '1004038801.jpg'),
('1301018802', 3, 7, 'Herry Setiawan', 'L', '', 'S.ST., MT', '1301018802.jpg'),
('6666666666', 8, 18, 'anii', 'L', 'Dr.', 'ST', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `jurusan`
--

CREATE TABLE `jurusan` (
  `jurId` smallint(6) NOT NULL,
  `jurKode` varchar(20) DEFAULT NULL,
  `jurNama` varchar(100) NOT NULL DEFAULT '',
  `jurNamaAsing` varchar(100) DEFAULT NULL,
  `jurIsAktif` tinyint(4) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `jurusan`
--

INSERT INTO `jurusan` (`jurId`, `jurKode`, `jurNama`, `jurNamaAsing`, `jurIsAktif`) VALUES
(1, 'ME', 'Teknik Mesin', 'Mechanical Engineering', 1),
(2, 'SP', 'Teknik Sipil', 'Civil Engineering', 1),
(3, 'EE', 'Teknik Elektro', 'Electrical Engineering', 1),
(4, 'AN', 'Administrasi Niaga', 'Business Administration', 1),
(5, 'AK', 'Akuntansi', 'Accounting', 1),
(6, 'TI', 'Teknologi Informasi', 'Information Technology', 1),
(7, 'BI', 'Bahasa Inggris', 'English', 1),
(8, 'DP', 'Desain Patung', 'patung', 1);

-- --------------------------------------------------------

--
-- Table structure for table `kelas`
--

CREATE TABLE `kelas` (
  `klsId` int(11) NOT NULL,
  `klsThakdId` smallint(6) DEFAULT NULL,
  `klsProdiId` smallint(6) DEFAULT NULL,
  `klsNama` varchar(30) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `kelas`
--

INSERT INTO `kelas` (`klsId`, `klsThakdId`, `klsProdiId`, `klsNama`) VALUES
(6, 20232, 7, 'III.E'),
(4, 20241, 3, 'IV'),
(1, 20241, 7, 'III.A'),
(2, 20241, 7, 'III.B'),
(3, 20241, 7, 'III.C'),
(5, 20241, 7, 'III.D'),
(7, 20241, 7, 'III.E'),
(8, 20241, 9, 'IV.P'),
(9, 20241, 18, 'I.P'),
(10, 20241, 18, 'V.P'),
(11, 20241, 19, '1.PD');

-- --------------------------------------------------------

--
-- Table structure for table `kelas_dosen`
--

CREATE TABLE `kelas_dosen` (
  `klsdsnId` int(11) NOT NULL,
  `klsdsnKlsId` int(11) DEFAULT NULL,
  `klsdsnDsnNidn` char(10) DEFAULT NULL,
  `klsdsnMkId` int(11) NOT NULL DEFAULT 0,
  `klsdsnIsAktif` tinyint(4) NOT NULL DEFAULT 1 COMMENT '0=Non-Aktif, 1=Aktif'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `kelas_dosen`
--

INSERT INTO `kelas_dosen` (`klsdsnId`, `klsdsnKlsId`, `klsdsnDsnNidn`, `klsdsnMkId`, `klsdsnIsAktif`) VALUES
(1, 1, '0014057908', 8, 1),
(2, 1, '0009067701', 9, 1),
(3, 1, '0007039304', 9, 1),
(4, 1, '0012067402', 10, 1),
(5, 1, '1004038801', 10, 1),
(6, 1, '0006058302', 11, 1),
(7, 1, '0011097202', 12, 1),
(8, 1, '0010049210', 13, 1),
(9, 1, '0029107506', 14, 1),
(10, 1, '0007047602', 14, 1),
(11, 1, '0030116506', 15, 1),
(12, 2, '0014057908', 8, 1),
(13, 2, '0009067701', 9, 1),
(14, 2, '0018069304', 9, 1),
(15, 2, '0029046406', 10, 1),
(16, 2, '0215039501', 10, 1),
(17, 2, '0006058302', 11, 1),
(18, 2, '0025117803', 12, 1),
(19, 2, '1301018802', 12, 1),
(20, 2, '0025016906', 13, 1),
(21, 2, '0011097202', 14, 1),
(22, 2, '1301018802', 14, 1),
(23, 2, '0030116506', 15, 1),
(24, 1, '1301018802', 12, 1),
(25, 3, '0014057908', 8, 1),
(26, 3, '0025016906', 9, 1),
(27, 3, '0006058302', 9, 1),
(28, 3, '0007087701', 10, 1),
(29, 3, '0025016906', 10, 1),
(30, 3, '0007087701', 11, 1),
(31, 3, '0011097202', 12, 1),
(32, 3, '0007039304', 12, 1),
(33, 3, '0010049210', 13, 1),
(34, 3, '0009067701', 14, 1),
(35, 3, '1301018802', 14, 1),
(36, 3, '0030046603', 15, 1);

-- --------------------------------------------------------

--
-- Table structure for table `kelas_mahasiswa`
--

CREATE TABLE `kelas_mahasiswa` (
  `klsmhsId` int(11) NOT NULL,
  `klsmhsKlsId` int(11) DEFAULT NULL,
  `klsmhsMhsNim` varchar(10) DEFAULT NULL,
  `klsmhsIsAktif` tinyint(4) NOT NULL DEFAULT 1 COMMENT '1=Aktif, 2=Non-Aktif'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `kelas_mahasiswa`
--

INSERT INTO `kelas_mahasiswa` (`klsmhsId`, `klsmhsKlsId`, `klsmhsMhsNim`, `klsmhsIsAktif`) VALUES
(1, 1, '2211071001', 1),
(2, 1, '2211071002', 1),
(3, 1, '2211071004', 1),
(4, 1, '2211071005', 1),
(5, 1, '2211071006', 1),
(6, 1, '2211071008', 1),
(7, 1, '2211071010', 1),
(8, 1, '2211072002', 1),
(9, 1, '2211072003', 1),
(10, 1, '2211072004', 1),
(11, 1, '2211072006', 1),
(12, 1, '2211072007', 1),
(13, 1, '2211072009', 1),
(14, 1, '2211072011', 1),
(15, 1, '2211073001', 1),
(16, 1, '2211073002', 1),
(17, 1, '2211073003', 1),
(18, 1, '2211073004', 1),
(19, 1, '2211073005', 1),
(20, 1, '2211073006', 1),
(21, 1, '2211073007', 1),
(22, 1, '2211073009', 1),
(23, 1, '2211073010', 1),
(24, 1, '2211073021', 1),
(25, 1, '2211072001', 1),
(26, 1, '2211071003', 1),
(27, 2, '2211071011', 1),
(28, 2, '2211071012', 1),
(29, 2, '2211071013', 1),
(30, 2, '2211071014', 1),
(31, 2, '2211071015', 1),
(32, 2, '2211071016', 1),
(33, 2, '2211072010', 1),
(34, 2, '2211072012', 1),
(35, 2, '2211072014', 1),
(36, 2, '2211072015', 1),
(37, 2, '2211072016', 1),
(38, 2, '2211072017', 1),
(39, 2, '2211072019', 1),
(40, 2, '2211072022', 1),
(41, 2, '2211073012', 1),
(42, 2, '2211073013', 1),
(43, 2, '2211073014', 1),
(44, 2, '2211073015', 1),
(45, 2, '2211073017', 1),
(46, 2, '2211073018', 1),
(47, 2, '2211073019', 1),
(48, 2, '2211073025', 1),
(49, 2, '2211073026', 1),
(50, 2, '2211073028', 1),
(51, 2, '2211073030', 1),
(52, 3, '2111071006', 1),
(53, 3, '2211071017', 1),
(54, 3, '2211071019', 1),
(55, 3, '2211071020', 1),
(56, 3, '2211071022', 1),
(57, 3, '2211071023', 1),
(58, 3, '2211072018', 1),
(59, 3, '2211072021', 1),
(60, 3, '2211072023', 1),
(61, 3, '2211072024', 1),
(62, 3, '2211072025', 1),
(63, 3, '2211072027', 1),
(64, 3, '2211072028', 1),
(65, 3, '2211072030', 1),
(66, 3, '2211073020', 1),
(67, 3, '2211073022', 1),
(68, 3, '2211073023', 1),
(69, 3, '2211073024', 1),
(70, 3, '2211073027', 1),
(71, 3, '2211073031', 1),
(72, 3, '2211073033', 1),
(73, 3, '2211073034', 1),
(74, 3, '2211073035', 1),
(75, 3, '2211071018', 1),
(76, 3, '2211073011', 1),
(77, 3, '2211071021', 1),
(78, 3, '2211073032', 1);

-- --------------------------------------------------------

--
-- Table structure for table `kelas_matakuliah`
--

CREATE TABLE `kelas_matakuliah` (
  `klsmkId` int(11) NOT NULL,
  `klsmkKlsId` int(11) DEFAULT NULL,
  `klsmkMkId` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `kelas_matakuliah`
--

INSERT INTO `kelas_matakuliah` (`klsmkId`, `klsmkKlsId`, `klsmkMkId`) VALUES
(25, 1, 4),
(27, 1, 7),
(1, 1, 8),
(2, 1, 9),
(3, 1, 10),
(4, 1, 11),
(5, 1, 12),
(6, 1, 13),
(7, 1, 14),
(8, 1, 15),
(9, 2, 8),
(10, 2, 9),
(11, 2, 10),
(12, 2, 11),
(13, 2, 12),
(14, 2, 13),
(15, 2, 14),
(16, 2, 15),
(17, 3, 8),
(18, 3, 9),
(19, 3, 10),
(20, 3, 11),
(21, 3, 12),
(22, 3, 13),
(23, 3, 14),
(24, 3, 15);

-- --------------------------------------------------------

--
-- Table structure for table `krs`
--

CREATE TABLE `krs` (
  `krsId` int(11) NOT NULL,
  `krsThakdId` smallint(6) DEFAULT NULL,
  `krsMhsNim` varchar(10) DEFAULT NULL,
  `krsPADsnNidn` char(10) DEFAULT NULL,
  `krsTglTerdaftar` date DEFAULT NULL,
  `krsSemester` tinyint(4) NOT NULL DEFAULT 0,
  `krsStatusLulus` enum('L','LP','TL','DO','-') DEFAULT '-'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `krs`
--

INSERT INTO `krs` (`krsId`, `krsThakdId`, `krsMhsNim`, `krsPADsnNidn`, `krsTglTerdaftar`, `krsSemester`, `krsStatusLulus`) VALUES
(1, 20241, '2211072002', '0022057705', '2024-08-09', 5, '-'),
(2, 20241, '2211073001', '0022057705', '2024-08-09', 5, '-'),
(3, 20241, '2211071001', '0022057705', '2024-08-09', 5, '-'),
(4, 20241, '2211071002', '0022057705', '2024-08-09', 5, '-'),
(5, 20241, '2211072011', '0022057705', '2024-08-09', 5, '-'),
(6, 20241, '2211071004', '0022057705', '2024-08-09', 5, '-'),
(7, 20241, '2211073021', '0022057705', '2024-08-12', 5, '-'),
(8, 20241, '2211073009', '0022057705', '2024-08-12', 5, '-'),
(9, 20241, '2211072004', '0022057705', '2024-08-13', 5, '-'),
(10, 20241, '2211072006', '0022057705', '2024-08-13', 5, '-'),
(11, 20241, '2211071010', '0022057705', '2024-08-13', 5, '-'),
(12, 20241, '2211073005', '0022057705', '2024-08-13', 5, '-'),
(13, 20241, '2211072003', '0022057705', '2024-08-14', 5, '-'),
(14, 20241, '2211073003', '0022057705', '2024-08-14', 5, '-'),
(15, 20241, '2211072009', '0022057705', '2024-08-14', 5, '-'),
(16, 20241, '2211073004', '0022057705', '2024-08-14', 5, '-'),
(17, 20241, '2211073002', '0022057705', '2024-08-14', 5, '-'),
(18, 20241, '2211073010', '0022057705', '2024-08-14', 5, '-'),
(19, 20241, '2211071008', '0022057705', '2024-08-14', 5, '-'),
(20, 20241, '2211071005', '0022057705', '2024-08-15', 5, '-'),
(21, 20241, '2211073007', '0022057705', '2024-08-16', 5, '-'),
(22, 20241, '2211073006', '0022057705', '2024-08-16', 5, '-'),
(23, 20241, '2211071006', '0022057705', '2024-08-16', 5, '-'),
(24, 20241, '2211072007', '0022057705', '2024-08-16', 5, '-'),
(25, 20241, '2211072001', '0022057705', '2024-08-20', 5, '-'),
(26, 20241, '2211071003', '0022057705', '2024-08-30', 5, '-'),
(27, 20241, '2211071011', '0018069304', '2024-08-09', 5, '-'),
(28, 20241, '2211071012', '0018069304', '2024-08-09', 5, '-'),
(29, 20241, '2211071013', '0018069304', '2024-08-14', 5, '-'),
(30, 20241, '2211071014', '0018069304', '2024-08-16', 5, '-'),
(31, 20241, '2211071015', '0018069304', '2024-08-16', 5, '-'),
(32, 20241, '2211071016', '0018069304', '2024-08-09', 5, '-'),
(33, 20241, '2211072010', '0018069304', '2024-08-09', 5, '-'),
(34, 20241, '2211072012', '0018069304', '2024-08-15', 5, '-'),
(35, 20241, '2211072014', '0018069304', '2024-08-15', 5, '-'),
(36, 20241, '2211072015', '0018069304', '2024-08-09', 5, '-'),
(37, 20241, '2211072016', '0018069304', '2024-08-16', 5, '-'),
(38, 20241, '2211072017', '0018069304', '2024-08-15', 5, '-'),
(39, 20241, '2211072019', '0018069304', '2024-08-13', 5, '-'),
(40, 20241, '2211072022', '0018069304', '2024-08-16', 5, '-'),
(41, 20241, '2211073012', '0018069304', '2024-08-15', 5, '-'),
(42, 20241, '2211073013', '0018069304', '2024-08-13', 5, '-'),
(43, 20241, '2211073014', '0018069304', '2024-08-16', 5, '-'),
(44, 20241, '2211073015', '0018069304', '2024-08-14', 5, '-'),
(45, 20241, '2211073017', '0018069304', '2024-08-16', 5, '-'),
(46, 20241, '2211073018', '0018069304', '2024-08-14', 5, '-'),
(47, 20241, '2211073019', '0018069304', '2024-08-13', 5, '-'),
(48, 20241, '2211073025', '0018069304', '2024-08-09', 5, '-'),
(49, 20241, '2211073026', '0018069304', '2024-08-09', 5, '-'),
(50, 20241, '2211073028', '0018069304', '2024-08-09', 5, '-'),
(51, 20241, '2211073030', '0018069304', '2024-08-09', 5, '-'),
(52, 20241, '2211073023', '0011097202', '2024-08-09', 5, '-'),
(53, 20241, '2211073024', '0011097202', '2024-08-09', 5, '-'),
(54, 20241, '2211072025', '0011097202', '2024-08-09', 5, '-'),
(55, 20241, '2211071022', '0011097202', '2024-08-09', 5, '-'),
(56, 20241, '2211073027', '0011097202', '2024-08-09', 5, '-'),
(57, 20241, '2211072027', '0011097202', '2024-08-09', 5, '-'),
(58, 20241, '2211073031', '0011097202', '2024-08-09', 5, '-'),
(59, 20241, '2211071017', '0011097202', '2024-08-09', 5, '-'),
(60, 20241, '2211072030', '0011097202', '2024-08-09', 5, '-'),
(61, 20241, '2211071019', '0011097202', '2024-08-09', 5, '-'),
(62, 20241, '2211072024', '0011097202', '2024-08-09', 5, '-'),
(63, 20241, '2211073033', '0011097202', '2024-08-09', 5, '-'),
(64, 20241, '2211071020', '0011097202', '2024-08-09', 5, '-'),
(65, 20241, '2211073034', '0011097202', '2024-08-09', 5, '-'),
(66, 20241, '2211072021', '0011097202', '2024-08-13', 5, '-'),
(67, 20241, '2111071006', '0011097202', '2024-08-14', 5, '-'),
(68, 20241, '2211073020', '0011097202', '2024-08-14', 5, '-'),
(69, 20241, '2211073022', '0011097202', '2024-08-14', 5, '-'),
(70, 20241, '2211072018', '0011097202', '2024-08-15', 5, '-'),
(71, 20241, '2211072023', '0011097202', '2024-08-15', 5, '-'),
(72, 20241, '2211072028', '0011097202', '2024-08-15', 5, '-'),
(73, 20241, '2211071023', '0011097202', '2024-08-16', 5, '-'),
(74, 20241, '2211073035', '0011097202', '2024-08-16', 5, '-'),
(75, 20241, '2211071021', '0011097202', '2024-08-28', 5, '-'),
(76, 20241, '2211073032', '0011097202', '2024-08-28', 5, '-'),
(77, 20241, '2211073011', '0011097202', '2024-08-28', 5, '-'),
(78, 20241, '2211071018', '0011097202', '2024-08-28', 5, '-');

-- --------------------------------------------------------

--
-- Table structure for table `krs_khs`
--

CREATE TABLE `krs_khs` (
  `khsId` int(11) NOT NULL,
  `khsKrsId` int(11) NOT NULL DEFAULT 0,
  `khsMkId` int(11) NOT NULL DEFAULT 0,
  `khsKodeNilai` varchar(3) DEFAULT NULL,
  `khsBobotNilai` decimal(4,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `kurikulum`
--

CREATE TABLE `kurikulum` (
  `kurId` int(11) NOT NULL,
  `kurProdiId` smallint(6) DEFAULT NULL,
  `kurTahun` year(4) NOT NULL DEFAULT 0000,
  `kurNama` varchar(100) NOT NULL DEFAULT '',
  `kurIsAktif` tinyint(4) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `kurikulum`
--

INSERT INTO `kurikulum` (`kurId`, `kurProdiId`, `kurTahun`, `kurNama`, `kurIsAktif`) VALUES
(1, 7, '2013', 'Kurikulum 2013', 1),
(2, 7, '2022', 'Kurikulum 2022', 1),
(3, 18, '2025', 'PATUNG merdeka', 1);

-- --------------------------------------------------------

--
-- Table structure for table `mahasiswa`
--

CREATE TABLE `mahasiswa` (
  `mhsNim` varchar(10) NOT NULL DEFAULT '',
  `mhsNama` varchar(50) NOT NULL DEFAULT '',
  `mhsTempatLahir` varchar(50) DEFAULT NULL,
  `mhsTglLahir` date DEFAULT NULL,
  `mhsJenisKelamin` enum('L','P') DEFAULT 'L',
  `mhsJurId` smallint(6) DEFAULT NULL,
  `mhsProdiId` smallint(6) DEFAULT NULL,
  `mhsKodeKelas` enum('A','B','C','D') DEFAULT 'A',
  `mhsStsAkademik` enum('A','L','C','D','K','M') DEFAULT 'A',
  `mhsSmsAktif` tinyint(4) DEFAULT 0,
  `mhsFoto` varchar(50) DEFAULT NULL,
  `mhsPaId` char(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `mahasiswa`
--

INSERT INTO `mahasiswa` (`mhsNim`, `mhsNama`, `mhsTempatLahir`, `mhsTglLahir`, `mhsJenisKelamin`, `mhsJurId`, `mhsProdiId`, `mhsKodeKelas`, `mhsStsAkademik`, `mhsSmsAktif`, `mhsFoto`, `mhsPaId`) VALUES
('2111071006', 'Ferdiansyah', 'Pariaman', '2003-07-29', 'L', 3, 7, 'C', 'A', 5, '2111071006.jpg', NULL),
('2211071001', 'Cindy Juniarti', 'Sikapak', '2004-06-16', 'P', 3, 7, 'A', 'A', 5, '2211071001.jpg', NULL),
('2211071002', 'Dhuha Rais', 'Padang', '2003-08-30', 'L', 3, 7, 'A', 'A', 5, '2211071002.jpg', NULL),
('2211071003', 'Dinda Kurnia Illahi', 'None', '2003-01-01', 'P', 3, 7, 'A', 'A', 5, '2211071003.jpg', NULL),
('2211071004', 'Erin Aulia Rahma', 'Koto Tuo', '2002-11-13', 'P', 3, 7, 'A', 'A', 5, '2211071004.jpg', NULL),
('2211071005', 'Ervin Nawal Andra', 'Lohong', '2004-11-21', 'L', 3, 7, 'A', 'A', 5, '2211071005.jpg', NULL),
('2211071006', 'Fikri Adi Pratama', 'Padang', '2004-01-24', 'L', 3, 7, 'A', 'A', 5, '2211071006.jpg', NULL),
('2211071008', 'Iqbal Rizantha', 'Sri Bintan', '2004-03-11', 'L', 3, 7, 'A', 'A', 5, '2211071008.jpg', NULL),
('2211071010', 'M. Aqil Hisyam Akbar', 'Pekan Baru', '2004-05-03', 'L', 3, 7, 'A', 'A', 5, '2211071010.jpg', NULL),
('2211071011', 'M. Aqsha Aqrizal', 'Lubuk Nyiur', '2003-01-22', 'L', 3, 7, 'B', 'A', 5, '2211071011.jpg', NULL),
('2211071012', 'M. Rizky Irawan', 'Dumai', '2003-01-06', 'L', 3, 7, 'B', 'A', 5, '2211071012.jpg', NULL),
('2211071013', 'Maulana Alfarizi', 'Jambi', '2005-01-15', 'L', 3, 7, 'B', 'A', 5, '2211071013.jpg', NULL),
('2211071014', 'Mhd. Yadil Ulya', 'Balai Rupih', '2004-02-26', 'L', 3, 7, 'B', 'A', 5, '2211071014.jpg', NULL),
('2211071015', 'Muhammad Zaki', 'Padang', '2004-06-08', 'L', 3, 7, 'B', 'A', 5, '2211071015.jpg', NULL),
('2211071016', 'Nisa Rahima Sakinah', 'Padang', '2004-04-27', 'P', 3, 7, 'B', 'A', 5, '2211071016.jpg', NULL),
('2211071017', 'Revandi Jeanifar', 'Pekanbaru', '2004-01-25', 'L', 3, 7, 'C', 'A', 5, '2211071017.jpg', NULL),
('2211071018', 'Rezi Apandi Sitompul', 'Persanggarahan', '2004-01-05', 'L', 3, 7, 'C', 'A', 5, '2211071018.jpg', NULL),
('2211071019', 'Shintia Destrianita', 'Lubuk Linggau', '2003-12-03', 'P', 3, 7, 'C', 'A', 5, '2211071019.jpg', NULL),
('2211071020', 'Taura Ramadhani', 'Perawang', '2003-11-10', 'P', 3, 7, 'C', 'A', 5, '2211071020.jpg', NULL),
('2211071021', 'Wisra Yandi', 'Ampang Gadang', '2004-04-12', 'L', 3, 7, 'C', 'A', 5, '2211071021.jpg', NULL),
('2211071022', 'Ilhamdi', 'Padang', '2003-09-10', 'L', 3, 7, 'C', 'A', 5, '2211071022.jpg', NULL),
('2211071023', 'Rio Agus Saputra', 'Talu', '2002-08-30', 'L', 3, 7, 'C', 'A', 5, '2211071023.jpg', NULL),
('2211072001', 'Adinda Aliya Maharani cantik', 'Padang', '2005-03-15', 'P', 3, 7, 'A', 'A', 5, '2211072001.jpg', NULL),
('2211072002', 'Aisyah Vianda Putri', 'Asam Kumbang', '2003-04-14', 'P', 3, 7, 'A', 'A', 5, '2211072002.jpg', NULL),
('2211072003', 'Alfhadila', 'Padang', '2004-01-12', 'L', 3, 7, 'A', 'A', 5, '2211072003.jpg', NULL),
('2211072004', 'Aliyah Arniz Nagia', 'Padang', '2004-05-15', 'P', 3, 7, 'A', 'A', 5, '2211072004.jpg', NULL),
('2211072006', 'Ashilah Khairunnisa Putri', 'Padang', '2004-05-16', 'P', 3, 7, 'A', 'A', 5, '2211072006.jpg', NULL),
('2211072007', 'Budi Mulia', 'Bukittinggi', '2003-10-29', 'L', 3, 7, 'A', 'A', 5, '2211072007.jpg', NULL),
('2211072009', 'Danda Maritsyah Putra', 'Jalan Balantai', '2004-03-20', 'L', 3, 7, 'A', 'A', 5, '2211072009.jpg', NULL),
('2211072010', 'Dian Indah Lestari', 'Padang Tongga', '2003-10-29', 'P', 3, 7, 'B', 'A', 5, '2211072010.jpg', NULL),
('2211072011', 'Dipal Irpandi', 'Sungai Geringging', '2003-09-19', 'L', 3, 7, 'A', 'A', 5, '2211072011.jpg', NULL),
('2211072012', 'Fawwaz Zahran', 'Padang', '2004-04-07', 'L', 3, 7, 'B', 'A', 5, '2211072012.jpg', NULL),
('2211072014', 'Lin Fitri Alif Aisyah', 'Dharmasraya', '2001-12-16', 'P', 3, 7, 'B', 'A', 5, '2211072014.jpg', NULL),
('2211072015', 'Aidil Safitra', 'Padang Panjang', '2002-02-16', 'L', 3, 7, 'B', 'A', 5, '2211072015.jpg', NULL),
('2211072016', 'Muhammad Aulia Zaki', 'Lubuk Basung', '2004-05-06', 'L', 3, 7, 'B', 'A', 5, '2211072016.jpg', NULL),
('2211072017', 'Muhammad Nur Fadly', 'Batam', '2003-07-12', 'L', 3, 7, 'B', 'A', 5, '2211072017.jpg', NULL),
('2211072018', 'Najip Nesta', 'Padang', '2004-06-10', 'L', 3, 7, 'C', 'A', 5, '2211072018.jpg', NULL),
('2211072019', 'Najla Raiqah Luthfiah', 'Padang', '2004-03-03', 'P', 3, 7, 'B', 'A', 5, '2211072019.jpg', NULL),
('2211072021', 'Nurwahid Fil Qodri', 'Koto Alam', '2004-01-06', 'L', 3, 7, 'C', 'A', 5, '2211072021.jpg', NULL),
('2211072022', 'Rindu Zulhimi Qolbu', 'Padang', '2004-02-10', 'P', 3, 7, 'B', 'A', 5, '2211072022.jpg', NULL),
('2211072023', 'Shalma Zopi Habibah', 'Lubuk Anau', '2004-12-01', 'P', 3, 7, 'C', 'A', 5, '2211072023.jpg', NULL),
('2211072024', 'Siti Aisyah Aliyah', 'Padang', '2005-01-18', 'P', 3, 7, 'C', 'A', 5, '2211072024.jpg', NULL),
('2211072025', 'Dani Adzmi', 'Padang', '2003-12-06', 'L', 3, 7, 'C', 'A', 5, '2211072025.jpg', NULL),
('2211072027', 'Mutiara Muthmainnah Nasution', 'Padang', '2002-05-17', 'P', 3, 7, 'C', 'A', 5, '2211072027.jpg', NULL),
('2211072028', 'Nabil Fajri', 'Sikapak', '2004-09-10', 'L', 3, 7, 'C', 'A', 5, '2211072028.jpg', NULL),
('2211072030', 'Salsa Bila', 'Sungai Penuh', '2004-03-13', 'P', 3, 7, 'C', 'A', 5, '2211072030.jpg', NULL),
('2211073001', 'Alif Bintang Al Ikhlas', 'Solok', '2003-05-22', 'L', 3, 7, 'A', 'A', 5, '2211073001.jpg', NULL),
('2211073002', 'Evan Adicandra', 'Jakarta', '2004-08-28', 'L', 3, 7, 'A', 'A', 5, '2211073002.jpg', NULL),
('2211073003', 'Miftahul Hamdi', 'Padang', '2004-08-02', 'L', 3, 7, 'A', 'A', 5, '2211073003.jpg', NULL),
('2211073004', 'Muhammad Erlangga', 'Padang', '2004-05-05', 'L', 3, 7, 'A', 'A', 5, '2211073004.jpg', NULL),
('2211073005', 'Muhammad Yudis Afriansyah Saputra', 'Kabupaten Kudus', '2003-07-14', 'L', 3, 7, 'A', 'A', 5, '2211073005.jpg', NULL),
('2211073006', 'Ramanda Grace Aulia', 'Batusangkar', '2003-12-23', 'P', 3, 7, 'A', 'A', 5, '2211073006.jpg', NULL),
('2211073007', 'Wendra Satria Utama', 'Sontang', '2004-04-16', 'L', 3, 7, 'A', 'A', 5, '2211073007.jpg', NULL),
('2211073009', 'Viony Monica', 'Padang', '2004-06-04', 'P', 3, 7, 'A', 'A', 5, '2211073009.jpg', NULL),
('2211073010', 'Yola Febrilla', 'Padang', '2004-02-09', 'P', 3, 7, 'A', 'A', 5, '2211073010.jpg', NULL),
('2211073011', 'Afif Ainur Rafiq', 'Bukittinggi', '2004-07-05', 'L', 3, 7, 'C', 'A', 5, '2211073011.jpg', NULL),
('2211073012', 'Ariq Muzakki', 'Pariaman', '2004-05-14', 'L', 3, 7, 'B', 'A', 5, '2211073012.jpg', NULL),
('2211073013', 'Khoirul Yazid', 'Bengkolan Dua', '2003-09-12', 'L', 3, 7, 'B', 'A', 5, '2211073013.jpg', NULL),
('2211073014', 'M. Affandi', 'Perawang', '2003-08-12', 'L', 3, 7, 'B', 'A', 5, '2211073014.jpg', NULL),
('2211073015', 'M. Ravi Setiawan P.C', 'Jakarta', '2003-04-23', 'L', 3, 7, 'B', 'A', 5, '2211073015.jpg', NULL),
('2211073017', 'Muhammad Rafif', 'Padang', '2003-08-01', 'L', 3, 7, 'B', 'A', 5, '2211073017.jpg', NULL),
('2211073018', 'Raihan Riyon Pratama', 'Bukittinggi', '2002-10-05', 'L', 3, 7, 'B', 'A', 5, '2211073018.jpg', NULL),
('2211073019', 'Rama Ihya Ulumuddin', 'Kayu Jao', '2002-12-03', 'L', 3, 7, 'B', 'A', 5, '2211073019.jpg', NULL),
('2211073020', 'Sultha Redysa', 'Padang', '2004-09-20', 'L', 3, 7, 'C', 'A', 5, '2211073020.jpg', NULL),
('2211073021', 'Tsabitah Hanum', 'Gaung', '2005-01-29', 'P', 3, 7, 'A', 'A', 5, '2211073021.jpg', NULL),
('2211073022', 'Zahra Haiva Meydina', 'Ampalu Tinggi', '2003-05-12', 'P', 3, 7, 'C', 'A', 5, '2211073022.jpg', NULL),
('2211073023', 'Andhika Fachrurriadi', 'Yogyakarta', '2003-07-15', 'L', 3, 7, 'C', 'A', 5, '2211073023.jpg', NULL),
('2211073024', 'Andika Putra', 'Bogor', '2003-10-20', 'L', 3, 7, 'C', 'A', 5, '2211073024.jpg', NULL),
('2211073025', 'Maya Dwi Anita', 'Balai Tangah', '2004-05-02', 'P', 3, 7, 'B', 'A', 5, '2211073025.jpg', NULL),
('2211073026', 'Meidina Agnesia', 'Kampung Tangah Ganting', '2004-05-25', 'P', 3, 7, 'B', 'A', 5, '2211073026.jpg', NULL),
('2211073027', 'Muhamad Ilham', 'Padang', '2001-10-21', 'L', 3, 7, 'C', 'A', 5, '2211073027.jpg', NULL),
('2211073028', 'Naia Az - Zahra', 'Padang', '2004-06-19', 'P', 3, 7, 'B', 'A', 5, '2211073028.jpg', NULL),
('2211073030', 'Putri Aulia Rahmi', 'Koto Tuo', '2003-03-29', 'P', 3, 7, 'B', 'A', 5, '2211073030.jpg', NULL),
('2211073031', 'Ranik Jintan', 'Koto Taratak', '2004-10-03', 'P', 3, 7, 'C', 'A', 5, '2211073031.jpg', NULL),
('2211073032', 'Riska Andini', 'Padang Panjang', '2003-08-13', 'P', 3, 7, 'C', 'A', 5, '2211073032.jpg', NULL),
('2211073033', 'Sri Mailani', 'Padang', '2004-05-14', 'P', 3, 7, 'C', 'A', 5, '2211073033.jpg', NULL),
('2211073034', 'Tri Sukma Afani', 'Padang', '2004-03-28', 'P', 3, 7, 'C', 'A', 5, '2211073034.jpg', NULL),
('2211073035', 'Wildan Zahky', 'Payakumbuh', '2003-09-26', 'L', 3, 7, 'C', 'A', 5, '2211073035.jpg', NULL),
('2311053012', 'alif', 'pdg', '2025-12-02', 'L', 8, 18, 'C', 'A', 1, '', '6666666666'),
('2311053016', 'gio', 'pdg', '2025-12-02', 'L', 8, 19, 'A', 'A', 5, '', '6666666666'),
('2311053066', 'HANIF ABDUL', 'pdg', '2025-12-08', 'L', 3, 7, 'B', 'A', 5, '', '0011097202'),
('2311053666', 'hanif-01', 'pdg', '2025-12-01', 'L', 8, 19, 'A', 'A', 5, '', '6666666666');

-- --------------------------------------------------------

--
-- Table structure for table `matakuliah`
--

CREATE TABLE `matakuliah` (
  `mkId` int(11) NOT NULL,
  `mkKurId` int(11) DEFAULT NULL,
  `mkKode` varchar(20) NOT NULL DEFAULT '',
  `mkNama` varchar(100) NOT NULL DEFAULT '',
  `mkNamaAsing` varchar(100) DEFAULT NULL,
  `mkSemester` tinyint(4) DEFAULT NULL,
  `mkSks` tinyint(4) NOT NULL DEFAULT 0,
  `mkIsAktif` tinyint(4) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `matakuliah`
--

INSERT INTO `matakuliah` (`mkId`, `mkKurId`, `mkKode`, `mkNama`, `mkNamaAsing`, `mkSemester`, `mkSks`, `mkIsAktif`) VALUES
(1, 2, 'TCE4106', 'Bahasa  inggris 2 ', '', 4, 2, 1),
(2, 2, 'TCE4218', 'Rangkaian Elektronika Telekomunikasi', '', 4, 3, 1),
(3, 2, 'TCE4219', 'Teori dan Perancangan Antena', '', 4, 2, 1),
(4, 2, 'TCE4220', 'Sistem Komunikasi Serat Optik', '', 4, 4, 1),
(5, 2, 'TCE4223', 'Jaringan Radio Akses ', '', 4, 4, 1),
(6, 2, 'TCE4229', 'Jaringan Komputer', '', 4, 3, 1),
(7, 2, 'TCE4232', 'Sistem Tertanam', '', 4, 3, 1),
(8, 2, 'TCE4107', 'Bahasa Inggris 3', '', 5, 2, 1),
(9, 2, 'TCE4221', 'Aplikasi Pengolahan Sinyal', '', 5, 3, 1),
(10, 2, 'TCE4224', 'Rekayasa Frekuensi Radio', '', 5, 4, 1),
(11, 2, 'TCE4225', 'Manajemen Telekomunikasi ', '', 5, 2, 1),
(12, 2, 'TCE4230', 'Komunikasi Data', '', 5, 3, 1),
(13, 2, 'TCE4231', 'Teknik Penyambungan ', '', 5, 3, 1),
(14, 2, 'TCE4234', 'Pemrograman Internet', '', 5, 2, 1),
(15, 2, 'TCE4307', 'Metoda Penelitian dan Penulisan Ilmiah', '', 5, 2, 1),
(16, 3, 'UKR4', 'ukir patung', 'broke', 1, 3, 1),
(17, 3, 'des4', 'desain patung', 'desain grafis', 1, 3, 1),
(18, 3, 'LjP', 'entiot', 'eww', 2, 3, 1),
(19, 3, 'LjP2', 'ewe patung', 'broke', 5, 5, 1),
(20, 3, 'UKR-06', 'UKIR LANJUT', 'NEXT', 3, 4, 1);

-- --------------------------------------------------------

--
-- Table structure for table `nilai_mahasiswa`
--

CREATE TABLE `nilai_mahasiswa` (
  `nmId` int(11) NOT NULL,
  `nmNim` varchar(20) DEFAULT NULL,
  `nmMkKode` varchar(20) DEFAULT NULL,
  `nmNilaiAngka` float DEFAULT NULL,
  `nmSemesterId` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `program_studi`
--

CREATE TABLE `program_studi` (
  `prodiId` smallint(6) NOT NULL,
  `prodiJurId` smallint(6) DEFAULT NULL,
  `prodiKode` varchar(20) DEFAULT NULL,
  `prodiNama` varchar(100) NOT NULL DEFAULT '',
  `prodiNamaAsing` varchar(100) DEFAULT NULL,
  `prodiJenjang` varchar(10) DEFAULT NULL,
  `prodiEmail` varchar(50) DEFAULT NULL,
  `prodiWebsite` varchar(50) DEFAULT NULL,
  `prodiIsAktif` tinyint(4) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `program_studi`
--

INSERT INTO `program_studi` (`prodiId`, `prodiJurId`, `prodiKode`, `prodiNama`, `prodiNamaAsing`, `prodiJenjang`, `prodiEmail`, `prodiWebsite`, `prodiIsAktif`) VALUES
(1, 3, '4EC', 'Teknik Elektronika', 'Electronics Engineering', 'D-4', 'ec@polinpdg.ac.id', 'http://ec.polinpdg.ac.id', 1),
(2, 2, '4TPIR', 'Teknik Perencanaan Irigasi dan Rawa', 'Irrigation Planning and Lowland Engineering', 'D-4', 'sp@polinpdg.ac.id', 'http://sp.polinpdg.ac.id', 1),
(3, 1, '4TMAN', 'Teknik Manufaktur', 'Manufacturing Engineering', 'D-4', 'me@polinpdg.ac.id', 'http://me.polinpdg.ac.id', 1),
(4, 2, '4PJJ', 'Perancangan Jalan dan Jembatan', 'Road and Bridge Design', 'D-4', 'sp@polinpdg.ac.id', 'http://sp.polinpdg.ac.id', 1),
(5, 2, '4MRK', 'Manajemen Rekayasa Konstruksi', 'Construction Engineering Management', 'D-4', 'sp@polinpdg.ac.id', 'http://sp.polinpdg.ac.id', 1),
(6, 3, '4TRIL', 'Teknologi Rekayasa Instalasi Listrik', 'Electrical Installation Engineering', 'D-4', 'tril@pnp.ac.id', '', 1),
(7, 3, '4TC', 'Teknik Telekomunikasi', 'Telecommunication Engineering', 'D-4', 'tc@polinpdg.ac.id', 'http://tc.polinpdg.ac.id', 1),
(8, 1, '4RPM', 'Rekayasa Perancangan Mekanik', '', 'D-4', '', '', 1),
(9, 1, '3ME', 'Teknik Mesin', 'Mechanical Engineering', 'D-3', 'me@polinpdg.ac.id', 'http://me.polinpdg.ac.id', 1),
(10, 2, '3SP', 'Teknik Sipil', 'Civil Engineering', 'D-3', 'sp@polinpdg.ac.id', 'http://sp.polinpdg.ac.id', 1),
(11, 3, '3EL', 'Teknik Listrik', 'Electrical Engineering', 'D-3', 'el@polinpdg.ac.id', 'http://el.polinpdg.ac.id', 1),
(12, 3, '3EC', 'Teknik Elektronika', 'Electronics Engineering', 'D-3', 'ec@polinpdg.ac.id', 'http://ec.polinpdg.ac.id', 1),
(13, 3, '3TC', 'Teknik Telekomunikasi', 'Telecommunication Engineering', 'D-3', 'tc@polinpdg.ac.id', 'http://tc.polinpdg.ac.id', 1),
(14, 1, '3TAB', 'Teknik Alat Berat', 'Heavy Equipment Engineering', 'D-3', 'tab@polinpdg.ac.id', 'http://me.polinpdg.ac.id', 1),
(15, 2, '3TS-TD', 'Teknologi Sipil', 'Civil Techonology', 'D-3', 'sp@polinpdg.ac.id', 'http://sp.polinpdg.ac.id', 1),
(16, 3, '3TL-P', 'Teknik Listrik', 'Electrical Engineering', 'D-3', 'el@polinpdg.ac.id', 'http://el.polinpdg.ac.id', 1),
(17, 7, '3BI', 'Bahasa Inggris', 'English', 'D-3', 'bi@polinpdg.ac.id', 'http://bi.polinpdg.ac.id', 1),
(18, 8, 'Pdas', 'patung dasar banget', '', 'D-3', '', '', 1),
(19, 8, 'LP', 'Lanjut patung ', 'next', 'S-1', 'patung@gmail.com', 'patung.com', 1);

-- --------------------------------------------------------

--
-- Table structure for table `tahun_akademik`
--

CREATE TABLE `tahun_akademik` (
  `thakdId` smallint(6) NOT NULL DEFAULT 0,
  `thakdTahun` year(4) NOT NULL DEFAULT 0000,
  `thakdSemester` enum('1','2') NOT NULL DEFAULT '1',
  `thakdTglMulai` date DEFAULT NULL,
  `thakdTglSelesai` date DEFAULT NULL,
  `thakdIsAktif` tinyint(4) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tahun_akademik`
--

INSERT INTO `tahun_akademik` (`thakdId`, `thakdTahun`, `thakdSemester`, `thakdTglMulai`, `thakdTglSelesai`, `thakdIsAktif`) VALUES
(20232, '2023', '2', '2024-02-19', '2024-07-20', 0),
(20241, '2024', '1', '2024-08-19', '2025-01-06', 1);

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `appusrID` varchar(50) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '',
  `appusrNama` varchar(50) DEFAULT NULL,
  `appusrPassword` varchar(50) DEFAULT NULL,
  `appusrGrupUser` varchar(50) DEFAULT NULL,
  `appusrNoHp` varchar(20) DEFAULT NULL,
  `appusrIsEnabled` tinyint(3) UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`appusrID`, `appusrNama`, `appusrPassword`, `appusrGrupUser`, `appusrNoHp`, `appusrIsEnabled`) VALUES
('ADMIN', 'RAHMA', '123', 'Admin', '081122334455', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `dosen`
--
ALTER TABLE `dosen`
  ADD PRIMARY KEY (`dsnNidn`),
  ADD KEY `dsnJurId_fk` (`dsnJurId`),
  ADD KEY `dsnProdiId_fk` (`dsnProdiId`);

--
-- Indexes for table `jurusan`
--
ALTER TABLE `jurusan`
  ADD PRIMARY KEY (`jurId`);

--
-- Indexes for table `kelas`
--
ALTER TABLE `kelas`
  ADD PRIMARY KEY (`klsId`),
  ADD UNIQUE KEY `klsUID` (`klsThakdId`,`klsProdiId`,`klsNama`),
  ADD KEY `klsProdiId_fk` (`klsProdiId`);

--
-- Indexes for table `kelas_dosen`
--
ALTER TABLE `kelas_dosen`
  ADD PRIMARY KEY (`klsdsnId`),
  ADD UNIQUE KEY `klsdsnUID` (`klsdsnKlsId`,`klsdsnDsnNidn`,`klsdsnMkId`),
  ADD KEY `klsdsnDsnNidn_fk` (`klsdsnDsnNidn`),
  ADD KEY `klsdsnMkId_fk` (`klsdsnMkId`);

--
-- Indexes for table `kelas_mahasiswa`
--
ALTER TABLE `kelas_mahasiswa`
  ADD PRIMARY KEY (`klsmhsId`),
  ADD UNIQUE KEY `klsmhsUID` (`klsmhsKlsId`,`klsmhsMhsNim`),
  ADD KEY `klsmhsMhsNim_fk` (`klsmhsMhsNim`);

--
-- Indexes for table `kelas_matakuliah`
--
ALTER TABLE `kelas_matakuliah`
  ADD PRIMARY KEY (`klsmkId`),
  ADD UNIQUE KEY `klsmkUID` (`klsmkKlsId`,`klsmkMkId`),
  ADD KEY `klsmkMkId_fk` (`klsmkMkId`);

--
-- Indexes for table `krs`
--
ALTER TABLE `krs`
  ADD PRIMARY KEY (`krsId`),
  ADD UNIQUE KEY `krsUID` (`krsThakdId`,`krsMhsNim`),
  ADD KEY `krsMhsNim_fk` (`krsMhsNim`),
  ADD KEY `krsPADsnNidn_fk` (`krsPADsnNidn`);

--
-- Indexes for table `krs_khs`
--
ALTER TABLE `krs_khs`
  ADD PRIMARY KEY (`khsId`),
  ADD UNIQUE KEY `khsUID` (`khsKrsId`,`khsMkId`),
  ADD KEY `khsMkId_fk` (`khsMkId`);

--
-- Indexes for table `kurikulum`
--
ALTER TABLE `kurikulum`
  ADD PRIMARY KEY (`kurId`),
  ADD KEY `kurProdiId_fk` (`kurProdiId`);

--
-- Indexes for table `mahasiswa`
--
ALTER TABLE `mahasiswa`
  ADD PRIMARY KEY (`mhsNim`),
  ADD KEY `mhsJurId_fk` (`mhsJurId`),
  ADD KEY `mhsProdiId_fk` (`mhsProdiId`);

--
-- Indexes for table `matakuliah`
--
ALTER TABLE `matakuliah`
  ADD PRIMARY KEY (`mkId`),
  ADD UNIQUE KEY `mkUniqueKey` (`mkKurId`,`mkKode`);

--
-- Indexes for table `nilai_mahasiswa`
--
ALTER TABLE `nilai_mahasiswa`
  ADD PRIMARY KEY (`nmId`);

--
-- Indexes for table `program_studi`
--
ALTER TABLE `program_studi`
  ADD PRIMARY KEY (`prodiId`),
  ADD KEY `prodiJurId_fk` (`prodiJurId`);

--
-- Indexes for table `tahun_akademik`
--
ALTER TABLE `tahun_akademik`
  ADD PRIMARY KEY (`thakdId`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`appusrID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `jurusan`
--
ALTER TABLE `jurusan`
  MODIFY `jurId` smallint(6) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `kelas`
--
ALTER TABLE `kelas`
  MODIFY `klsId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `kelas_dosen`
--
ALTER TABLE `kelas_dosen`
  MODIFY `klsdsnId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `kelas_mahasiswa`
--
ALTER TABLE `kelas_mahasiswa`
  MODIFY `klsmhsId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=79;

--
-- AUTO_INCREMENT for table `kelas_matakuliah`
--
ALTER TABLE `kelas_matakuliah`
  MODIFY `klsmkId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `krs`
--
ALTER TABLE `krs`
  MODIFY `krsId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=79;

--
-- AUTO_INCREMENT for table `krs_khs`
--
ALTER TABLE `krs_khs`
  MODIFY `khsId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `kurikulum`
--
ALTER TABLE `kurikulum`
  MODIFY `kurId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `matakuliah`
--
ALTER TABLE `matakuliah`
  MODIFY `mkId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `nilai_mahasiswa`
--
ALTER TABLE `nilai_mahasiswa`
  MODIFY `nmId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `program_studi`
--
ALTER TABLE `program_studi`
  MODIFY `prodiId` smallint(6) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `dosen`
--
ALTER TABLE `dosen`
  ADD CONSTRAINT `dsnJurId_fk` FOREIGN KEY (`dsnJurId`) REFERENCES `jurusan` (`jurId`) ON UPDATE CASCADE,
  ADD CONSTRAINT `dsnProdiId_fk` FOREIGN KEY (`dsnProdiId`) REFERENCES `program_studi` (`prodiId`) ON UPDATE CASCADE;

--
-- Constraints for table `kelas`
--
ALTER TABLE `kelas`
  ADD CONSTRAINT `klsProdiId_fk` FOREIGN KEY (`klsProdiId`) REFERENCES `program_studi` (`prodiId`) ON UPDATE CASCADE,
  ADD CONSTRAINT `klsThakdId_fk` FOREIGN KEY (`klsThakdId`) REFERENCES `tahun_akademik` (`thakdId`) ON UPDATE CASCADE;

--
-- Constraints for table `kelas_dosen`
--
ALTER TABLE `kelas_dosen`
  ADD CONSTRAINT `klsdsnDsnNidn_fk` FOREIGN KEY (`klsdsnDsnNidn`) REFERENCES `dosen` (`dsnNidn`) ON UPDATE CASCADE,
  ADD CONSTRAINT `klsdsnKlsId_fk` FOREIGN KEY (`klsdsnKlsId`) REFERENCES `kelas` (`klsId`) ON UPDATE CASCADE,
  ADD CONSTRAINT `klsdsnMkId_fk` FOREIGN KEY (`klsdsnMkId`) REFERENCES `matakuliah` (`mkId`) ON UPDATE CASCADE;

--
-- Constraints for table `kelas_mahasiswa`
--
ALTER TABLE `kelas_mahasiswa`
  ADD CONSTRAINT `klsmhsKlsId_fk` FOREIGN KEY (`klsmhsKlsId`) REFERENCES `kelas` (`klsId`) ON UPDATE CASCADE,
  ADD CONSTRAINT `klsmhsMhsNim_fk` FOREIGN KEY (`klsmhsMhsNim`) REFERENCES `mahasiswa` (`mhsNim`) ON UPDATE CASCADE;

--
-- Constraints for table `kelas_matakuliah`
--
ALTER TABLE `kelas_matakuliah`
  ADD CONSTRAINT `klsmkKlsId_fk` FOREIGN KEY (`klsmkKlsId`) REFERENCES `kelas` (`klsId`) ON UPDATE CASCADE,
  ADD CONSTRAINT `klsmkMkId_fk` FOREIGN KEY (`klsmkMkId`) REFERENCES `matakuliah` (`mkId`) ON UPDATE CASCADE;

--
-- Constraints for table `krs`
--
ALTER TABLE `krs`
  ADD CONSTRAINT `krsMhsNim_fk` FOREIGN KEY (`krsMhsNim`) REFERENCES `mahasiswa` (`mhsNim`) ON UPDATE CASCADE,
  ADD CONSTRAINT `krsPADsnNidn_fk` FOREIGN KEY (`krsPADsnNidn`) REFERENCES `dosen` (`dsnNidn`) ON UPDATE CASCADE,
  ADD CONSTRAINT `krsThakdId_fk` FOREIGN KEY (`krsThakdId`) REFERENCES `tahun_akademik` (`thakdId`) ON UPDATE CASCADE;

--
-- Constraints for table `krs_khs`
--
ALTER TABLE `krs_khs`
  ADD CONSTRAINT `khsKrsId_fk` FOREIGN KEY (`khsKrsId`) REFERENCES `krs` (`krsId`) ON UPDATE CASCADE,
  ADD CONSTRAINT `khsMkId_fk` FOREIGN KEY (`khsMkId`) REFERENCES `matakuliah` (`mkId`) ON UPDATE CASCADE;

--
-- Constraints for table `kurikulum`
--
ALTER TABLE `kurikulum`
  ADD CONSTRAINT `kurProdiId_fk` FOREIGN KEY (`kurProdiId`) REFERENCES `program_studi` (`prodiId`) ON UPDATE CASCADE;

--
-- Constraints for table `mahasiswa`
--
ALTER TABLE `mahasiswa`
  ADD CONSTRAINT `mhsJurId_fk` FOREIGN KEY (`mhsJurId`) REFERENCES `jurusan` (`jurId`) ON UPDATE CASCADE,
  ADD CONSTRAINT `mhsProdiId_fk` FOREIGN KEY (`mhsProdiId`) REFERENCES `program_studi` (`prodiId`) ON UPDATE CASCADE;

--
-- Constraints for table `matakuliah`
--
ALTER TABLE `matakuliah`
  ADD CONSTRAINT `mkKurId_fk` FOREIGN KEY (`mkKurId`) REFERENCES `kurikulum` (`kurId`) ON UPDATE CASCADE;

--
-- Constraints for table `program_studi`
--
ALTER TABLE `program_studi`
  ADD CONSTRAINT `prodiJurId_fk` FOREIGN KEY (`prodiJurId`) REFERENCES `jurusan` (`jurId`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
