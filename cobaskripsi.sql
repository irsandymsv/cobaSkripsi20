-- phpMyAdmin SQL Dump
-- version 4.7.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: 16 Sep 2020 pada 12.22
-- Versi Server: 10.1.30-MariaDB
-- PHP Version: 7.2.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `cobaskripsi`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `hs_image`
--

CREATE TABLE `hs_image` (
  `id` tinyint(4) UNSIGNED NOT NULL,
  `image` text,
  `width` smallint(6) NOT NULL,
  `height` smallint(6) NOT NULL,
  `peak` tinyint(3) UNSIGNED DEFAULT NULL,
  `zero` tinyint(3) UNSIGNED DEFAULT NULL,
  `kapasitas` mediumint(8) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `hs_image`
--

INSERT INTO `hs_image` (`id`, `image`, `width`, `height`, `peak`, `zero`, `kapasitas`, `created_at`, `updated_at`) VALUES
(4, 'histogram/mxxz9sqT3kenT9pBx0FxbaLWo7yhOoZsaRQN2IjK.jpeg', 360, 360, 255, 0, 53935, '2020-02-22 14:10:39', '2020-02-22 14:10:39'),
(5, 'histogram/aWaf4njAx89kGgidrP69uckLeUuOQinEB2YtbdoH.jpeg', 930, 574, 0, 129, 312206, '2020-02-23 05:17:56', '2020-02-23 05:17:56'),
(6, 'histogram/dP9SYE79DKnkeIS7A35UyKN7zB86sRRmOzFZWHI1.jpeg', 1280, 720, 35, 254, 164312, '2020-02-23 05:21:36', '2020-02-23 05:21:36');

-- --------------------------------------------------------

--
-- Struktur dari tabel `login_token`
--

CREATE TABLE `login_token` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `email_token` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `login_token`
--

INSERT INTO `login_token` (`id`, `user_id`, `email_token`, `created_at`, `updated_at`) VALUES
(5, 1, '8542', '2020-02-11 14:41:47', '2020-02-11 14:41:47'),
(7, 1, '528843', '2020-02-11 15:16:15', '2020-02-11 15:16:15');

-- --------------------------------------------------------

--
-- Struktur dari tabel `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2014_10_12_100000_create_password_resets_table', 1),
(3, '2020_02_11_192358_login_token', 1),
(4, '2020_04_08_170229_create_recovery_image_table', 2);

-- --------------------------------------------------------

--
-- Struktur dari tabel `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `recovery_image`
--

CREATE TABLE `recovery_image` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `recovery_image`
--

INSERT INTO `recovery_image` (`id`, `user_id`, `created_at`, `updated_at`) VALUES
(1, 1, '2020-04-08 13:59:52', '2020-04-08 13:59:52'),
(2, 1, '2020-04-09 05:31:26', '2020-04-09 05:31:26'),
(3, 1, '2020-04-09 05:52:04', '2020-04-09 05:52:04'),
(4, 1, '2020-04-10 09:55:20', '2020-04-10 09:55:20');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `nama` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `no_hp` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tgl_lahir` date NOT NULL,
  `gender` varchar(12) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `nama`, `email`, `password`, `no_hp`, `tgl_lahir`, `gender`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'budi cahyanto', 'irsandymsv@yahoo.com', '$2y$10$DRqW2lgiZ6sx/1BvquyuZu9RTmxG7DhiG8SBweJyPduIlpv85sHpW', '08974345678', '2019-07-10', 'Laki-laki', NULL, '2020-02-11 12:51:19', '2020-04-10 10:00:15'),
(2, 'budi Harianto', 'budi123@yahoo.com', '$2y$10$y46ifEktWgBVwrIFPA7btOKblWQ3xNq0FIAAPb.tIK33G5DJI/rp6', '089765434', '2020-01-27', 'Laki-laki', NULL, '2020-02-23 04:17:40', '2020-02-23 04:17:40'),
(3, 'User baru', 'user@emails.com', '$2y$10$9Ph9Sz9sm0QyLu.KE7dmAe4tIB/kInWf9TdUH5dc4JVAoKMG2IA92', '08951234567', '2020-01-27', 'Laki-laki', NULL, '2020-02-23 04:20:10', '2020-02-23 04:20:10'),
(4, 'bambang hore', 'bambang123@gmail.com', '$2y$10$5kxDS8b1hePl4MDitObd6OguoZ6pHv1xcAlEHOMltQMVGkgBb/5g6', '098328787', '2020-01-27', 'Perempuan', NULL, '2020-02-25 03:28:26', '2020-02-25 03:28:26'),
(5, 'agus', 'agus123@yahoo.com', '$2y$10$YZW.QZtdrGpgJANd7tXlt.bppwpmOOiecD8HICBaMLrM/ZLu1Jagy', '08976556', '2020-01-26', 'Laki-laki', NULL, '2020-02-25 03:37:43', '2020-02-25 03:37:43'),
(6, 'budi cahyanto', 'newUser123@gmail.com', '$2y$10$O2fS06.g9JrZkweZgLb.vucsfw5N4xE8eXAGnPfhGnViXC2qesS9e', '085234678907', '2020-01-27', 'Laki-laki', NULL, '2020-02-25 03:42:45', '2020-02-25 03:42:45'),
(7, 'udin', 'udin12345@gmail.com', '$2y$10$0l2bAQ1et5TAK52cyqnIlOjfcOxV/ea/9VkxaUMGoOAmt6CiagrIu', '089472642', '2020-01-26', 'Laki-laki', NULL, '2020-02-25 03:55:20', '2020-02-25 03:55:20'),
(8, 'agus horeee', 'agushore@gmail.com', '$2y$10$5S0qF1oyJvWQvmZ54GlODOLQsbK17/4mNOc74g/C2I0l0opYBPQPi', '08754567313', '2020-01-26', 'Laki-laki', NULL, '2020-02-25 04:07:40', '2020-02-25 04:07:40'),
(9, 'agan baru', 'aganbaru@gmail.com', '$2y$10$pONnEs6PGnX3wHc7HM2.deGRim28Sy8lW.UhRX8ZjZguIHE.ywSEW', '083234234', '2020-01-26', 'Laki-laki', NULL, '2020-02-25 04:23:56', '2020-02-25 04:23:56'),
(10, 'indah dewi', 'indahdewi@yahoo.com', '$2y$10$/QXQf11B10VFD6L3UVGyh.DW.3dS9t6nT8kaEmf6qPpqvALJp3AS.', '08976541212', '2020-03-01', 'Perempuan', NULL, '2020-03-05 12:52:21', '2020-03-05 12:52:21'),
(11, 'rama pratama', 'rama1@gmail.com', '$2y$10$Ni7M0CSPf5lIV96Bv.yJ1eXfxdahHxL9BX1wZ7HJpVAtuYRdDm4.W', '0832456723', '2020-02-06', 'Laki-laki', NULL, '2020-03-05 12:57:42', '2020-03-05 12:57:42'),
(12, 'david indra', 'david321@yahoo.com', '$2y$10$VtxMcEpGAzhID4cFP452D.napubGt83P6eR/f5OuLsviT2O.S/3MC', '088965324', '2020-02-03', 'Laki-laki', NULL, '2020-03-05 13:12:49', '2020-03-05 13:12:49'),
(13, 'irfan andrian', 'irfan321@yahoo.com', '$2y$10$XME2E2g0P.CHQs/guqcaBuKo/.1b41587JeNfa8j0wSzo/tzfoRrC', '089776556', '2020-03-01', 'Laki-laki', NULL, '2020-03-05 13:15:44', '2020-03-05 13:15:44'),
(14, 'admin baru1', 'adminbaru1234566789@yahoo.com', '$2y$10$qedJ7d9/.1IWAoQMv/rCmOVJLlX1xDJ18pMPx./Zs1Z9Z/MhPsw1K', '089512345343', '2020-03-02', 'Laki-laki', NULL, '2020-03-05 13:28:39', '2020-03-05 13:28:39'),
(15, 'coba RSA', 'rsaAlgo@yahoo.com', '$2y$10$2kT/QtfDiqxhJlm5fmrEnuadU7od7RMnCt77dokR4odeH/Fw87LaS', '0897765555', '2020-03-01', 'Laki-laki', NULL, '2020-03-17 12:43:57', '2020-03-17 12:43:57'),
(16, 'Bambang Wijoyono', 'bambangWk@gmail.com', '$2y$10$/HY0hRsTcUrI3fl70Qcr2u5HlKWApWqSPVoHYBlf8jOi9quD8kEXa', '08976555521', '2020-03-31', 'Laki-laki', NULL, '2020-04-10 04:56:19', '2020-04-10 04:56:19'),
(17, 'tes user baru', 'usermail@yahoo.com', '$2y$10$Zmf6pX5aqBrXlH8QmxCvnelyGGdEFaMgrpecXv5R4mWyqzXe.lZci', '0856432342', '2020-03-29', 'Perempuan', NULL, '2020-04-10 05:50:27', '2020-04-10 05:50:27'),
(18, 'Rama Purnama', 'ramaPurnama@yahoo.com', '$2y$10$HxQxHftFhePhkpcPPba7BuxEe7bnwV0yVJLxoxykNhihmV/N3Hme2', '086543456', '2020-04-01', 'Laki-laki', NULL, '2020-04-10 06:35:34', '2020-04-10 06:35:34'),
(19, 'Anwar Budiman', 'anwarbudi@gmail.com', '$2y$10$CdHtnyVaGGN.2zFH/mFkd.1/0oy1/2roCVFa3jutah.VTZTrZ2DZW', '0873212345', '2020-03-20', 'Laki-laki', NULL, '2020-04-10 07:05:14', '2020-04-10 07:05:14'),
(20, 'agus pratama putra', 'agus543@gmail.com', '$2y$10$ERYVIhkBw8i2uawctoQq1.weC3KQA1ExmXuyT6qMxvq/SfnWoRv2W', '08754567432', '2020-03-21', 'Laki-laki', NULL, '2020-04-10 10:20:03', '2020-04-10 10:20:03');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `hs_image`
--
ALTER TABLE `hs_image`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `login_token`
--
ALTER TABLE `login_token`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `login_token_email_token_unique` (`email_token`),
  ADD KEY `login_token_user_id_foreign` (`user_id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD KEY `password_resets_email_index` (`email`);

--
-- Indexes for table `recovery_image`
--
ALTER TABLE `recovery_image`
  ADD PRIMARY KEY (`id`),
  ADD KEY `recovery_image_user_id_foreign` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD UNIQUE KEY `users_no_hp_unique` (`no_hp`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `hs_image`
--
ALTER TABLE `hs_image`
  MODIFY `id` tinyint(4) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `login_token`
--
ALTER TABLE `login_token`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `recovery_image`
--
ALTER TABLE `recovery_image`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `login_token`
--
ALTER TABLE `login_token`
  ADD CONSTRAINT `login_token_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `recovery_image`
--
ALTER TABLE `recovery_image`
  ADD CONSTRAINT `recovery_image_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
