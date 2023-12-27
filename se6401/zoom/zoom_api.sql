-- phpMyAdmin SQL Dump
-- version 4.9.7
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Feb 04, 2021 at 10:42 AM
-- Server version: 5.7.32
-- PHP Version: 7.4.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `zoom_api`
--

-- --------------------------------------------------------

--
-- Table structure for table `token`
--

CREATE TABLE `token` (
  `id` int(11) NOT NULL,
  `access_token` text NOT NULL,
  `user_id` int(255) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `token`
--

INSERT INTO `token` (`id`, `access_token`, `user_id`, `timestamp`, `updated_timestamp`) VALUES
(7, '{\"access_token\":\"eyJhbGciOiJIUzUxMiIsInYiOiIyLjAiLCJraWQiOiJiNzcwY2U0My04Y2UzLTQ0MDAtYTBiMS05MzMyYjczOGIxMGQifQ.eyJ2ZXIiOjcsImF1aWQiOiI4ODI3NDEzNDA2MjJkMjU4NDNiODhjNTA5ZWJmYWJkNCIsImNvZGUiOiJuTmJSN1ZtVm9vX1p1U1pFXy1LUTNXbF9zZHF1ZTBxZWciLCJpc3MiOiJ6bTpjaWQ6cUR2SGpGTFpTMWlSTTlqUWplQ2p6ZyIsImdubyI6MCwidHlwZSI6MCwidGlkIjowLCJhdWQiOiJodHRwczovL29hdXRoLnpvb20udXMiLCJ1aWQiOiJadVNaRV8tS1EzV2xfc2RxdWUwcWVnIiwibmJmIjoxNjEyNDM0ODQyLCJleHAiOjE2MTI0Mzg0NDIsImlhdCI6MTYxMjQzNDg0MiwiYWlkIjoiLUNiYXhJNENUXzJlMmYzb2FGNVVSUSIsImp0aSI6IjBkNDMxNjc2LTJiYjUtNGI2OC04NWZlLWE0YTI0NDY4MWRkZSJ9.hHyeXvF64ZiIsKI51n9mqwDdp1sHetBYa4ryiGiIMYXxn-d9e7uW3kXl3eJ1mRLLYG0M0BNWjuJPgh7_EZZCyw\",\"token_type\":\"bearer\",\"refresh_token\":\"eyJhbGciOiJIUzUxMiIsInYiOiIyLjAiLCJraWQiOiJlYTcyMWViMy0yOWM4LTRkNGQtYWJjNi1jMzY0YmI1Y2YwM2MifQ.eyJ2ZXIiOjcsImF1aWQiOiI4ODI3NDEzNDA2MjJkMjU4NDNiODhjNTA5ZWJmYWJkNCIsImNvZGUiOiJuTmJSN1ZtVm9vX1p1U1pFXy1LUTNXbF9zZHF1ZTBxZWciLCJpc3MiOiJ6bTpjaWQ6cUR2SGpGTFpTMWlSTTlqUWplQ2p6ZyIsImdubyI6MCwidHlwZSI6MSwidGlkIjowLCJhdWQiOiJodHRwczovL29hdXRoLnpvb20udXMiLCJ1aWQiOiJadVNaRV8tS1EzV2xfc2RxdWUwcWVnIiwibmJmIjoxNjEyNDM0ODQyLCJleHAiOjIwODU0NzQ4NDIsImlhdCI6MTYxMjQzNDg0MiwiYWlkIjoiLUNiYXhJNENUXzJlMmYzb2FGNVVSUSIsImp0aSI6ImU4OTMzOTgyLTljZTEtNDQyMy05MTgyLTdiNDJkMjExZmY3YyJ9.8OsjDUpQfLAu1WeyNGsMmod7--bfCbaXgPANuYteR52AoRAIgwu9jB2HiSbI63WT9GDGqHnIm8v2MXMKpnLPdw\",\"expires_in\":3599,\"scope\":\"meeting:read meeting:write\"}', 0, '2021-02-04 10:41:50', '2021-02-04 10:34:02');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `token`
--
ALTER TABLE `token`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `token`
--
ALTER TABLE `token`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
