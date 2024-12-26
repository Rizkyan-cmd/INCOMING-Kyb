-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 25 Des 2024 pada 12.20
-- Versi server: 10.1.38-MariaDB
-- Versi PHP: 5.6.40

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `warehouse`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `incoming_check`
--

CREATE TABLE `incoming_check` (
  `id` int(11) NOT NULL,
  `orno` varchar(10) DEFAULT NULL,
  `item` varchar(50) DEFAULT NULL,
  `dsca` varchar(50) DEFAULT NULL,
  `seqn` int(11) DEFAULT NULL,
  `pono` int(11) DEFAULT NULL,
  `qstok` int(11) DEFAULT NULL,
  `bpid` varchar(10) DEFAULT NULL,
  `no_part` varchar(50) DEFAULT NULL,
  `nama` varchar(50) DEFAULT NULL,
  `trdt` datetime DEFAULT NULL,
  `rcno` varchar(10) DEFAULT NULL,
  `cwar` varchar(10) DEFAULT NULL,
  `smpling` int(11) DEFAULT NULL,
  `status` int(11) DEFAULT NULL,
  `ng` int(11) DEFAULT NULL,
  `ok` int(11) DEFAULT NULL,
  `ng_kategori` int(2) DEFAULT NULL,
  `ng_detail` varchar(255) DEFAULT NULL,
  `ins_dt` datetime DEFAULT NULL,
  `evidence` varchar(110) DEFAULT NULL,
  `check` int(11) DEFAULT NULL,
  `check_usr` varchar(50) DEFAULT NULL,
  `check_dt` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `incoming_check`
--

INSERT INTO `incoming_check` (`id`, `orno`, `item`, `dsca`, `seqn`, `pono`, `qstok`, `bpid`, `no_part`, `nama`, `trdt`, `rcno`, `cwar`, `smpling`, `status`, `ng`, `ok`, `ng_kategori`, `ng_detail`, `ins_dt`, `evidence`, `check`, `check_usr`, `check_dt`) VALUES
(350, 'PO001', 'Segitiga shockbreaker', 'Untuk Shock depan', NULL, NULL, 150, 'KD-04', 'NP123', 'PT MAJU MUNDUR', '2024-12-23 00:00:00', 'R001', 'CW-02', 2, NULL, 0, 150, 0, '', '0000-00-00 00:00:00', NULL, NULL, 'T123', '2024-12-24 00:00:00'),
(362, 'PO005', 'Busa', 'Agar empuk', 0, 0, 200, 'KD-05', 'NP223', 'PT MAJU MUNDUR', '2024-12-23 00:00:00', 'R001', 'CW-02', 3, NULL, 0, 200, 0, '', NULL, NULL, NULL, 'T123', '2024-12-24 00:00:00'),
(363, 'PO002', 'Beras', 'Bor Beras habis bor', 0, 0, 200, 'KD-02', 'NP223444', 'PT KO ALIONG SEJAHTERA', '2024-12-12 00:00:00', 'R002', 'WHCKD0', 3, 0, 0, 200, 0, '', '0000-00-00 00:00:00', '', 0, 'T123', '2024-12-05 00:00:00'),
(364, 'PO003', 'Semen 1 sak', 'Buat ngaduk', 0, 0, 300, 'KD-66', 'NP004', 'PT BABA LIONG', '2024-11-10 04:00:00', 'R002', '', 2, 0, 0, 300, 0, '', '0000-00-00 00:00:00', '', 0, 'T123', '2024-12-04 00:00:00'),
(368, 'PO666', 'Oli', 'Oli bekas', NULL, NULL, 400, 'KD-06', 'BJ00', 'PT LAY ABADI', '2024-12-06 00:00:00', 'RC666', NULL, 3, NULL, 3, 397, 2, NULL, NULL, '1734582237_67639fdd09408.png', NULL, 'T666', '2024-12-19 00:00:00'),
(369, 'P022', 'Helm', NULL, NULL, NULL, 200, NULL, 'PT001', 'PT.NOK', '2024-12-09 00:00:00', 'R001', NULL, 2, NULL, 2, 198, 0, NULL, NULL, '1733889521_67590df1305b3.jpeg', NULL, 'T1234', '2024-12-09 00:00:00'),
(370, 'PO066', 'Spring', '', NULL, NULL, 100, 'KD-01', '0022-11', 'PT.NOK', '2024-12-23 00:00:00', 'RC666', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(371, 'PO11', 'Celana', '', 0, 0, 200, '', '343-4344', 'PT MAJU MUNDUR', '2024-12-09 00:00:00', 'R001', '', 5, NULL, 0, 200, 0, '', NULL, NULL, NULL, 'T666', '2024-12-05 00:00:00'),
(372, 'PO090', 'Sarung', '', 0, 0, 200, '', '', 'PT BABA LIONG', '2024-12-02 00:00:00', 'R002', '', 3, 0, 0, 200, 0, '', '0000-00-00 00:00:00', '', 0, 'T123', '2024-12-05 00:00:00'),
(373, 'PO1222', 'Oli', 'Tes', NULL, NULL, 300, NULL, '11222-233', 'PT MAJU MUNDUR', '2024-12-09 00:00:00', 'R001', NULL, 6, NULL, 0, 300, 0, NULL, NULL, '', NULL, 'T1234', '2024-12-11 00:00:00'),
(374, 'PO055', 'Spring', '', 0, 0, 100, '', '111-22', 'PT BABA LIONG', '2024-12-02 00:00:00', 'R002', '', 4, 0, 0, 100, 0, '', '0000-00-00 00:00:00', '', 0, 'T123', '2024-12-05 00:00:00'),
(375, 'P223', 'Kacamata', '', 0, 0, 200, '', '222-2222', 'PT MAJU MUNDUR', '2024-12-09 00:00:00', 'R001', '', 23, NULL, 2, 198, 1, '', '0000-00-00 00:00:00', '1733900275_675937f3178ff.png', 0, 'T1234', '2024-12-06 00:00:00'),
(376, 'PO002', 'Bushing', '', NULL, NULL, 150, '', '221-332', 'PT LAY ABADI', '2024-12-23 00:00:00', 'RC666', NULL, 2, NULL, 2, 148, 2, 'Tidak sesuai gambar', NULL, '1733979000_675a6b78aeb43.png', NULL, 'T666', '2024-12-23 00:00:00'),
(379, 'POo34', 'Segitiga', 'Untuk Shock', NULL, NULL, 150, 'WH-123', '12344-224', 'PT.  KO ALIONG', '2024-12-23 04:00:00', 'RC666', NULL, NULL, NULL, NULL, NULL, 0, '', NULL, '', NULL, NULL, NULL),
(380, 'PO045', 'Tabung shock', '', 0, 0, 300, 'CKD022', '22313-2434', 'PT> LAY', '2024-12-22 00:00:00', 'R002', '', 5, 0, 0, 300, 0, '', '0000-00-00 00:00:00', '', 0, 'T123', '2024-12-05 00:00:00'),
(382, 'P123', 'Gerobak', 'Hasil bansos', 0, 0, 400, 'CK-2233', '3233-333', 'PT SUMBER JAYA', '2024-11-07 00:00:00', 'RC555', '', 1, NULL, 0, 400, 0, '', '0000-00-00 00:00:00', '', NULL, 'T123', '2024-11-08 00:00:00'),
(383, 'P123', 'Centong', 'Centong aja', NULL, NULL, 300, 'CKD', '233-234', 'PT SUMBER JAYA', '2024-11-07 00:00:00', 'RC555', '', 2, NULL, 0, 300, 0, '', NULL, '', 0, 'T123', '2024-11-08 00:00:00'),
(384, 'P233', 'Semen', 'Semeen aja', NULL, NULL, 200, '', '2133-123', 'PT SUMBER MAKMUR', '2024-11-07 00:00:00', 'RC555', '', 2, NULL, 2, 198, 1, '', '0000-00-00 00:00:00', '1733714214_675661260135e.jpeg', NULL, 'T123', '2024-11-08 00:00:00'),
(387, 'P022', 'Oli', '', NULL, NULL, 400, '', '0222-1234', 'PT SUMBER MAKMUR', '2024-12-23 00:00:00', 'RC666', '', NULL, NULL, NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL),
(388, 'PO066', 'Fork', 'Fork Front', NULL, NULL, 200, 'KD-12', '2234-4422', 'PT SUMBER MAKMUR', '2024-11-11 00:00:00', 'RC555', 'WH 12', 2, NULL, 1, 199, 1, '', NULL, '1733718528_67567200654aa.png', 0, 'T123', '2024-11-08 00:00:00'),
(389, 'PO113', 'Oli Shock', NULL, NULL, NULL, 200, 'KD-22', '123341', 'PT MAJU MUNDUR', '2024-12-09 00:00:00', 'R001', 'WH 23', 1, NULL, 0, 200, 0, NULL, NULL, NULL, NULL, 'T123', '2024-12-12 00:00:00'),
(390, 'PO344', 'Oil Sheal', 'Tes', NULL, NULL, 200, 'KD -222', '2342441', 'PT SUMBER MAKMUR', '2024-12-16 00:00:00', 'RC666', NULL, 2, NULL, 10, 190, 2, NULL, NULL, '1734065128_675bbbe8c19bb.jpeg', NULL, 'T123', '2024-12-18 00:00:00'),
(391, 'P122', 'Plastik Wraping', 'Wraping', NULL, NULL, 200, 'KC321', '332244', 'PT MAKMUR BAHAGIA', '2024-12-12 00:00:00', 'Q666', NULL, 2, NULL, 2, 198, 2, NULL, NULL, '1734063587_675bb5e3a2e8f.jpeg', NULL, 'T666', '2024-12-13 00:00:00'),
(392, 'B324', 'Baut', 'Baut Shock', NULL, NULL, 150, 'XS2323', '43332', 'PT MAKMUR BAHAGIA', '2024-12-12 00:00:00', 'Q666', NULL, 2, NULL, 0, 150, 0, NULL, NULL, NULL, NULL, 'T666', '2024-12-13 00:00:00'),
(393, 'P122', 'Gelas', 'Gelas Karyawan', NULL, NULL, 200, 'Ckasd', '23242', 'PT Gudang sejahterah', '2024-12-16 00:00:00', 'RR11', NULL, 3, NULL, 3, 197, 1, NULL, NULL, '1734320704_675fa240d209b.png', NULL, 'T1234', '2024-12-16 00:00:00'),
(394, 'PO777', 'Spring ', 'Spring kacil bngt', NULL, NULL, 100, 'CP1', '12233', 'PT CIPTA', '2024-12-23 09:00:00', 'R777', 'CW02', 2, NULL, 0, 100, 0, '', NULL, NULL, NULL, 'T1234', '2024-12-24 00:00:00'),
(395, '3322', 'Baut', 'Baut Shock', NULL, NULL, 150, NULL, '43332', 'PT CIPTA', '2024-12-23 09:00:00', 'R777', NULL, 2, NULL, 0, 150, 0, '', NULL, NULL, NULL, 'T123', '2024-12-25 00:00:00');

-- --------------------------------------------------------

--
-- Struktur dari tabel `incoming_ng`
--

CREATE TABLE `incoming_ng` (
  `id` int(11) NOT NULL,
  `ng_kategori` int(11) NOT NULL,
  `ng_desc` varchar(100) NOT NULL,
  `deskripsi` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `incoming_ng`
--

INSERT INTO `incoming_ng` (`id`, `ng_kategori`, `ng_desc`, `deskripsi`) VALUES
(1, 1, 'Fungsi ', 'Tidak berjalan sesuai standar'),
(2, 2, 'Visual', 'Tampilan tidak sesuai');

-- --------------------------------------------------------

--
-- Struktur dari tabel `lajur_pengecekan`
--

CREATE TABLE `lajur_pengecekan` (
  `id` int(11) NOT NULL,
  `nama` varchar(50) DEFAULT NULL,
  `bpid` varchar(10) DEFAULT NULL,
  `status` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `lajur_pengecekan`
--

INSERT INTO `lajur_pengecekan` (`id`, `nama`, `bpid`, `status`) VALUES
(1, 'PT MAJU MUNDUR', 'KD-05', 1),
(2, 'PT SUMBER JAYA', 'CKD', 1),
(3, 'PT. SUMBER MAKMUR', 'KD-12', 2),
(4, 'PT CIPTA', 'CP1', 2);

-- --------------------------------------------------------

--
-- Struktur dari tabel `otp_authentic`
--

CREATE TABLE `otp_authentic` (
  `id` int(11) NOT NULL,
  `npk` varchar(6) NOT NULL,
  `phone_number` varchar(14) NOT NULL,
  `otp` varchar(6) NOT NULL,
  `expiry_date` datetime NOT NULL,
  `send` int(11) NOT NULL,
  `send_date` datetime DEFAULT NULL,
  `use` int(11) NOT NULL,
  `use_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `otp_authentic`
--

INSERT INTO `otp_authentic` (`id`, `npk`, `phone_number`, `otp`, `expiry_date`, `send`, `send_date`, `use`, `use_date`) VALUES
(226, 'T123', '6289502233411', '887774', '2024-12-25 17:46:17', 2, NULL, 1, '2024-12-25 17:41:32'),
(227, 'T1234', '6289502233411', '510421', '2024-12-24 13:16:10', 2, NULL, 1, '2024-12-24 13:11:26'),
(228, 'T666', '6289502233411', '946633', '2024-12-23 11:28:42', 2, NULL, 1, '2024-12-23 11:23:57'),
(229, 'T12345', '6289502233411', '744952', '2024-12-12 13:20:24', 2, NULL, 1, '2024-12-12 13:15:46'),
(230, 'N666', '6289502233411', '678787', '2024-12-24 13:15:14', 2, NULL, 1, '2024-12-24 13:10:30');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `incoming_check`
--
ALTER TABLE `incoming_check`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `incoming_ng`
--
ALTER TABLE `incoming_ng`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `lajur_pengecekan`
--
ALTER TABLE `lajur_pengecekan`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `otp_authentic`
--
ALTER TABLE `otp_authentic`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `incoming_check`
--
ALTER TABLE `incoming_check`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=396;

--
-- AUTO_INCREMENT untuk tabel `incoming_ng`
--
ALTER TABLE `incoming_ng`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `lajur_pengecekan`
--
ALTER TABLE `lajur_pengecekan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `otp_authentic`
--
ALTER TABLE `otp_authentic`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=231;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
