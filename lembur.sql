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
-- Database: `lembur`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `ct_users_hash`
--

CREATE TABLE `ct_users_hash` (
  `npk` varchar(6) NOT NULL,
  `full_name` varchar(50) DEFAULT NULL,
  `pwd` varchar(255) DEFAULT NULL,
  `dept` varchar(50) DEFAULT NULL,
  `sect` varchar(20) DEFAULT NULL,
  `golongan` int(1) DEFAULT NULL,
  `acting` int(1) DEFAULT NULL,
  `no_telp` char(15) DEFAULT NULL,
  `user_email` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `ct_users_hash`
--

INSERT INTO `ct_users_hash` (`npk`, `full_name`, `pwd`, `dept`, `sect`, `golongan`, `acting`, `no_telp`, `user_email`) VALUES
('00924', 'Arry Kuswarto', '$2y$10$7Zde3vW7PFV7RNF2Pq8iVOsvDcyYOJibMNiOC8RRteMbsuqN6zvpG', 'MKT', '', 3, 1, NULL, NULL),
('01271', 'Agus Subiyono', '$2y$10$O76FKrELiNudIjb7qTCBTuCPrNOhoPxrDFnbXVsNTfIT1sawH2CAS', 'VDD', '', 4, 1, NULL, NULL),
('01299', 'Supri', '$2y$10$UiQRZ.wbVdHzLAr2QMaBe.wt7dXWoFR78.I2uRJ9MsaDXAF3gxj1G', 'VDD', '', 3, 1, NULL, NULL),
('01313', 'Nurrokhman A', '$2y$10$NPjlMPFXk1q7GWzYeUDS5eK1LAkXAnSryD4YvKZKwh9WklxuhE9.q', 'PCE', '', 4, 1, NULL, NULL),
('01317', 'Sigit Nugroho', '$2y$10$4DiXKVIv91KDXhyriiHgc.DiEakOgOatO/4//E5WG0mUbZcr4MSyK', 'PS', '', 3, 1, NULL, NULL),
('01326', 'Hariyanto', '$2y$10$Moxorj38UgFkbiV3.Tyh3.AG.GJamgP766SdMab4C5Uaoql0yJg3q', 'PDE', 'oem', 2, 1, NULL, NULL),
('01345', 'Aris Nugroho', '$2y$10$KfTcz6NR82eA/TK1q.rzhOK0RcGG3V9wQcz57Lg4XGVxU6rHtVmOy', 'PDE', 'oem', 3, 1, NULL, NULL),
('01466', 'Eko Widodo', '$2y$10$nSvjdYXljjg9p9gJb0tGf.LYWdZ9ahWAag9Rgt7SvLwq7bIHhl0Ga', 'PDE', '', 4, 1, NULL, NULL),
('01537', 'Hasoloan', '$2y$10$aCmgykX7jZ55.rkvAXQrWOZ4O7I0uOJGxviyvmIkxKxhT6.GcQ2MO', 'QA', '', 4, 1, NULL, NULL),
('01560', 'Purwanto', '$2y$10$401q8nS2OEXLs3540bppWuW9yKRQiK5lKhNsjYGLu.Hl6VmGjkfxO', 'WH', '', 4, 1, NULL, NULL),
('01656', 'Dwi S', '$2y$10$4RvrOPQw2Km.aSBwOFdhkOIPmVZDnh9QbWjH3RA5YStw1czyIX.ly', 'PS', '', 4, 1, NULL, NULL),
('01818', 'Gunawan', '$2y$10$RKRr.E6nQMKBvYz9RsSV6eO1Hu9Bdnlk8PTycXqG.i/pko.DFdsaq', 'PCE', '', 2, 1, NULL, NULL),
('01870', 'Tri Fuad', '$2y$10$IiMF4AVF.GYNodeIQUFY8.lQ.zJ9yC.AixHLVoCOKax9XV1zmxMWC', 'PDE', 'racing', 3, 1, NULL, NULL),
('01878', 'Annas Nugroho', '$2y$10$gZBhet7m0TYyRXWhSn.rAOgLyRxdmdonxCBVu7ecPA1I0ohGcIag2', 'PDE', 'standarisasi', 3, 1, NULL, NULL),
('01905', 'Tina Setyawati', '$2y$10$5v3R1oeuwh6TxPNzTxsFaOXwEnacGBs63GIl/dRmw.lzOCrDwhbte', 'MKT', '', 2, 1, NULL, NULL),
('01941', 'Chandra Y', '$2y$10$4x86jmYzfV7crBB2ZGubvu06qBqxv3yYkfn3A9SZZwEj.qNu.zwBG', 'PPC', '', 4, 1, NULL, NULL),
('01958', 'Aldi Reza', '$2y$10$W4dn0w6zIHz4XaUSD3dOzuKaTruP.Wt91UpnAzzWWJRmwqvGn/lUK', 'PPC', '', 3, 1, NULL, NULL),
('01959', 'Indra', '$2y$10$BFQHMdGbal4JJ59wcD9evuGVKPENsodyzL9zM0THnHYNLdISdJ9dm', 'WH', '', 3, 1, NULL, NULL),
('01960', 'Santi Yulika', '$2y$10$0LEJb/oCf.f2rWKzOCwEp.6uQIzZ4EnEpC0YELlhLMdzTudUt5NXK', 'QA', '', 3, 1, NULL, NULL),
('02026', 'Bagus W', '$2y$10$lgD0IZ6GXom5no1CgMrmY.G0CeojkPkHr2aK/XDFNGDU23/qvzyPi', 'MKT', '', 4, 1, NULL, NULL),
('02028', 'Subekti', '$2y$10$iqH28xVH9KQtmcshjOJodO7DP4vNN.Dc54W1fEjM6Bwzltql102DW', 'WH', '', 2, 1, NULL, NULL),
('02057', 'Sapto Bowo', '$2y$10$7zJkTYtoYWfY2WYZ8FYT7uVawwf69Alu5SsbX37aEq3yqqxWB6gDa', 'PDE', 'oem', 2, 1, NULL, NULL),
('02332', 'Ade Supriyan', '$2y$10$eKxYy62s4UW7jyM2sG4kCenzXFZs8LLXBM3WwT20DUMpqNvDYLIF.', 'T&A', '', 2, 1, NULL, NULL),
('02364', 'M.Aqomuddin', '$2y$10$WtHt75NB1rlouBsyHPssa.3I8anVwQomFkvwAfljYTfM9x52TWR/W', 'PS', '', 2, 1, NULL, NULL),
('02373', 'Sidik Wijana', '$2y$10$05RMJ.lS4knsNMTAUwiX8O5bFoci5jii.Ky.eudluCM3kc28KJ19S', 'QA', '', 2, 1, NULL, NULL),
('02382', 'Ical Prahara', '$2y$10$1mK6mrdLrXfZ0oJV/3HZNOgA8Z3jzzStSaRYeoQi.PtA25hXbWDwW', 'PDE', 'oem', 2, 1, NULL, NULL),
('02404', 'Saiful N', '$2y$10$rQ2pr6hfV/mVuuky85sR3esCrbqtnhtbkQmjlzw4cV8BN7/4XITdi', 'PPC', '', 2, 1, NULL, NULL),
('02507', 'Muhammad Safii', '$2y$10$fDUGxURXv.iOVBV.sw4T5uRh4fGts.mH.a7pEADHNKgPqPvpx4x.C', 'PDE', 'racing', 2, 1, NULL, NULL),
('02552', 'Uray Muhamad', '$2y$10$/CVV5DCY5VLD83lQw.LqE.dUzTGIsxlOk2XWuBh531Q/cMJSY9Jxa', 'VDD', '', 2, 1, NULL, NULL),
('02570', 'Ghafar Fachrizal', '$2y$10$AP2rHG7zxiSZcK8eSoga..siQIiFNb66YB0OQPHwVnszcLdgyXOta', 'PCE', '', 3, 1, NULL, NULL),
('02590', 'Himindo', '$2y$10$V8uK4yiQ.p6IhU0WsZny1.htfhjZLM./64d3zNy35LFwaYSffFC7O', 'T&A', '', 4, 1, NULL, NULL),
('02606', 'Chatrina', '$2y$10$QXhFEfEIFEGnWbIZdcVSEe9J5Ra4jOIIcLlx/PiqaXbQeuXUYsLXa', 'T&A', '', 3, 1, NULL, NULL),
('1111', 'Contoh User', '$2y$10$Yr0TQ7WBVdSkn5nlQdnnqORDBDd0FNTbrNHh24iPRr.kf68d9y.Wq', 'HRD', 'admin', 2, 2, NULL, NULL),
('2222', 'Contoh User Produksi', '$2y$10$Yr0TQ7WBVdSkn5nlQdnnqORDBDd0FNTbrNHh24iPRr.kf68d9y.Wq', 'PROD1', NULL, 2, 3, NULL, NULL),
('3004', 'Ageng Admin', '$2y$10$Yr0TQ7WBVdSkn5nlQdnnqORDBDd0FNTbrNHh24iPRr.kf68d9y.Wq', 'PDE', 'admin', 2, 1, NULL, NULL),
('3333', 'Contoh User 2', '$2y$10$CTEMV33WvDucW/bBlmbMauWW47AZsSeJMmLByFlAvIQP79a9x9r8.', 'PROD1', '2', 0, 0, '', ''),
('M1512', 'Ageng Susila', '$2y$10$Yr0TQ7WBVdSkn5nlQdnnqORDBDd0FNTbrNHh24iPRr.kf68d9y.Wq', 'PDE', 'admin', NULL, NULL, NULL, NULL),
('N666', 'Wiliam Beckam', '$2a$12$.12.yj5fur93NwuTliII8OJ0UaecIqX/J1rY92k1ruT5vVHrn1pFi', 'MIS', 'WH', 4, 1, '089652102473', NULL),
('P1095', 'Syariifah Erni ', '$2y$10$CTEMV33WvDucW/bBlmbMauWW47AZsSeJMmLByFlAvIQP79a9x9r8.', 'PDE', 'standarisasi', 2, 1, NULL, NULL),
('T123', 'Ahmad Rizky Ananda', '$2a$12$Nt7cV.cgWQ0pVLwwDx4mF.wkbMLVUobYbI.aYLXk1RNWMMqd3o4zG', 'MIS', NULL, 3, NULL, '6281398267055', NULL),
('T1234', 'Charlesudin', '$2a$12$Nt7cV.cgWQ0pVLwwDx4mF.wkbMLVUobYbI.aYLXk1RNWMMqd3o4zG', 'Incoming', NULL, 3, NULL, '6289652102473', NULL),
('T12345', 'Gusmarten Paes', '$2a$12$Nt7cV.cgWQ0pVLwwDx4mF.wkbMLVUobYbI.aYLXk1RNWMMqd3o4zG', 'Karyawan', NULL, 2, NULL, '6281398267055', NULL),
('T30004', 'Ageng MIS', '$2y$10$Yr0TQ7WBVdSkn5nlQdnnqORDBDd0FNTbrNHh24iPRr.kf68d9y.Wq', 'MIS', 'admin', 2, 1, NULL, NULL),
('T30005', 'Non MIS', '$2y$10$Yr0TQ7WBVdSkn5nlQdnnqORDBDd0FNTbrNHh24iPRr.kf68d9y.Wq', 'HRD', 'admin', 2, 1, NULL, NULL),
('T30006', 'Susila MIS', '$2y$10$Yr0TQ7WBVdSkn5nlQdnnqORDBDd0FNTbrNHh24iPRr.kf68d9y.Wq', 'MIS', 'admin', 2, 1, NULL, NULL),
('T666', 'William Beckam', '$2a$12$XGMaDR.LVyoGUlQB0FLbdew2RJQ2c6Vi2a85s/0.gmfJ6Pui0vNKW', 'MIS', '1', 1, NULL, '089667141062', NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `hrd_so`
--

CREATE TABLE `hrd_so` (
  `npk` varchar(5) NOT NULL,
  `tipe` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data untuk tabel `hrd_so`
--

INSERT INTO `hrd_so` (`npk`, `tipe`) VALUES
('22222', 1),
('99999', 1),
('N666', 1);

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `ct_users_hash`
--
ALTER TABLE `ct_users_hash`
  ADD PRIMARY KEY (`npk`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
