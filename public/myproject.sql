-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Oct 24, 2025 at 12:04 PM
-- Server version: 9.1.0
-- PHP Version: 8.2.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `myproject`
--

-- --------------------------------------------------------

--
-- Table structure for table `article`
--

DROP TABLE IF EXISTS `article`;
CREATE TABLE IF NOT EXISTS `article` (
  `id` varchar(32) NOT NULL,
  `title` varchar(128) DEFAULT NULL,
  `slug` varchar(128) NOT NULL,
  `content` text,
  `draft` enum('true','false') NOT NULL DEFAULT 'true',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `article`
--

INSERT INTO `article` (`id`, `title`, `slug`, `content`, `draft`, `created_at`) VALUES
('68f12c453f7de4.79768814', 'Peran Kecerdasan Buatan dalam Dunia Modern', 'peran-kecerdasan-buatan-dalam-dunia-modern-68f12c453f7de4.79768814', '<p>Kecerdasan buatan (AI) kini banyak digunakan di berbagai bidang, mulai dari kesehatan, transportasi, hingga pendidikan. Mahasiswa teknik informatika mempelajari cara membuat sistem yang mampu berpikir layaknya manusia, seperti chatbot, pengenal wajah, dan sistem rekomendasi.</p>', 'false', '2025-10-17 00:32:53'),
('68f12fd23c7fe3.00527216', 'Pengembangan Aplikasi Berbasis Web', 'pengembangan-aplikasi-berbasis-web-68f12fd23c7fe3.00527216', '<p>Web development menjadi salah satu keahlian penting di dunia teknik informatika. Dengan bahasa seperti HTML, CSS, dan JavaScript, pengembang dapat menciptakan aplikasi yang interaktif dan mudah diakses melalui browser.</p>', 'false', '2025-10-17 00:48:02'),
('68f12fe8827856.75575052', 'Keamanan Siber: Melindungi Data di Era Digital', 'keamanan-siber-melindungi-data-di-era-digital-68f12fe8827856.75575052', '<p>Keamanan siber berperan penting untuk menjaga data dari ancaman hacker. Mahasiswa teknik informatika mempelajari teknik enkripsi, firewall, dan etika hacking untuk menciptakan sistem yang aman dan terpercaya.</p>', 'false', '2025-10-17 00:48:24'),
('68f13000901bc8.58503504', 'Internet of Things (IoT) dan Kehidupan Cerdas', 'internet-of-things-iot-dan-kehidupan-cerdas-68f13000901bc8.58503504', '<p>IoT memungkinkan berbagai perangkat terhubung ke internet, seperti lampu pintar atau sistem rumah otomatis. Bidang ini menggabungkan pemrograman, jaringan, dan sensor untuk menciptakan lingkungan yang efisien dan modern.</p>', 'false', '2025-10-17 00:48:48'),
('68f13016110a29.25514249', 'Machine Learning: Membuat Komputer Belajar Sendiri', 'machine-learning-membuat-komputer-belajar-sendiri-68f13016110a29.25514249', '<p>Machine learning adalah cabang AI yang memungkinkan komputer belajar dari data tanpa diprogram secara eksplisit. Contohnya, sistem bisa mengenali pola wajah atau memprediksi cuaca berdasarkan data sebelumnya.</p>', 'false', '2025-10-17 00:49:10');

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

DROP TABLE IF EXISTS `feedback`;
CREATE TABLE IF NOT EXISTS `feedback` (
  `id` varchar(32) NOT NULL,
  `name` varchar(32) NOT NULL,
  `email` varchar(32) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `feedback`
--

INSERT INTO `feedback` (`id`, `name`, `email`, `message`, `created_at`) VALUES
('68f1304f604ac3.71922849', 'nami', 'nami@gmail.com', 'artikel sangat berguna, mantep', '2025-10-16 17:50:07'),
('68f1306b7781e9.63852679', 'chihiro', 'chihiro@gmail.com', 'sangat bagus', '2025-10-16 17:50:35');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
CREATE TABLE IF NOT EXISTS `user` (
  `id` varchar(32) NOT NULL,
  `name` varchar(32) NOT NULL,
  `email` varchar(64) NOT NULL,
  `username` varchar(64) NOT NULL,
  `password` varchar(255) NOT NULL,
  `avatar` varchar(32) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `last_login` timestamp NULL DEFAULT NULL,
  `password_updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `name`, `email`, `username`, `password`, `avatar`, `created_at`, `last_login`, `password_updated_at`) VALUES
('6118b2a943acc2.78631959', 'Administrator', 'admin@mail.com', 'admin', '$2y$10$ijz0kg11AQkFE55IVgaO.OCHQBdHzi4vutMXkXosVeIEe6Kx8tqt6', '6118b2a943acc278631959.jpg', '2021-08-14 23:22:33', '2025-10-23 19:25:48', '2025-10-16 14:54:27');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
