-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Feb 19, 2019 at 12:32 AM
-- Server version: 10.2.17-MariaDB
-- PHP Version: 7.2.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `u364272929_sc2v2`
--

-- --------------------------------------------------------

--
-- Table structure for table `botrequests`
--

CREATE TABLE `botrequests` (
  `id` int(11) NOT NULL,
  `UploadedTime` timestamp NOT NULL DEFAULT current_timestamp(),
  `FileLoc` varchar(128) DEFAULT NULL,
  `DownloadLink` varchar(128) DEFAULT NULL,
  `Comments` text DEFAULT NULL,
  `AdminComments` varchar(1024) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `loginattempts`
--

CREATE TABLE `loginattempts` (
  `IP` varchar(20) NOT NULL,
  `Attempts` int(11) NOT NULL,
  `LastLogin` datetime NOT NULL,
  `Username` varchar(65) DEFAULT NULL,
  `ID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `maps`
--

CREATE TABLE `maps` (
  `MapId` int(11) NOT NULL,
  `MapName` varchar(64) NOT NULL,
  `Active` tinyint(4) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `matches`
--

CREATE TABLE `matches` (
  `MatchId` int(11) NOT NULL,
  `Bot1ID` int(11) NOT NULL,
  `Bot2ID` int(11) NOT NULL,
  `MapName` varchar(64) NOT NULL,
  `MatchTime` timestamp NOT NULL DEFAULT current_timestamp(),
  `RequesterId` varchar(32) NOT NULL,
  `ResultId` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `members`
--

CREATE TABLE `members` (
  `id` char(23) NOT NULL,
  `username` varchar(65) NOT NULL DEFAULT '',
  `password` varchar(65) NOT NULL DEFAULT '',
  `email` varchar(65) NOT NULL,
  `verified` tinyint(1) NOT NULL DEFAULT 0,
  `mod_timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `Joined` timestamp NOT NULL DEFAULT current_timestamp(),
  `Alias` varchar(128) DEFAULT NULL,
  `Name` varchar(128) DEFAULT NULL,
  `Avatar` varchar(128) DEFAULT NULL,
  `Website` varchar(128) DEFAULT NULL,
  `Github` varchar(128) DEFAULT NULL,
  `ProfileVisible` tinyint(4) NOT NULL DEFAULT 1,
  `Patreon` tinyint(4) NOT NULL DEFAULT 0,
  `Tournament` tinyint(4) NOT NULL DEFAULT 0,
  `CanRequestGames` tinyint(4) NOT NULL DEFAULT 0,
  `AutoAuth` tinyint(4) NOT NULL DEFAULT 0,
  `Admin` tinyint(4) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `participants`
--

CREATE TABLE `participants` (
  `ID` int(11) NOT NULL,
  `Name` varchar(128) NOT NULL,
  `Author` varchar(100) NOT NULL,
  `Race` int(11) NOT NULL,
  `Verified` int(11) NOT NULL DEFAULT 0,
  `Deactivated` tinyint(4) NOT NULL DEFAULT 0,
  `Deleted` tinyint(4) NOT NULL DEFAULT 0,
  `EloFormat` tinyint(4) NOT NULL DEFAULT 1,
  `CurrentELO` int(11) NOT NULL DEFAULT 0,
  `Downloadable` tinyint(4) NOT NULL DEFAULT 0,
  `WorkingDirectory` varchar(128) DEFAULT NULL,
  `PlayerID` varchar(24) DEFAULT NULL,
  `DataDirectory` varchar(128) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `resetrequests`
--

CREATE TABLE `resetrequests` (
  `user` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `code` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `added` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `results`
--

CREATE TABLE `results` (
  `GameID` int(11) NOT NULL,
  `Date` datetime DEFAULT NULL,
  `Bot1` int(11) NOT NULL,
  `Bot2` int(11) NOT NULL,
  `Map` varchar(64) NOT NULL,
  `Winner` int(11) NOT NULL,
  `Crash` tinyint(4) NOT NULL DEFAULT 0,
  `Result` varchar(32) DEFAULT NULL,
  `ReplayFile` varchar(128) DEFAULT NULL,
  `SeasonId` int(11) NOT NULL DEFAULT 0,
  `Bot1Change` int(11) NOT NULL DEFAULT 0,
  `Bot2Change` int(11) NOT NULL DEFAULT 0,
  `Bot1AvgFrame` double NOT NULL,
  `Bot2AvgFrame` double NOT NULL,
  `Frames` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `seasonids`
--

CREATE TABLE `seasonids` (
  `id` int(11) NOT NULL,
  `SeasonName` varchar(128) NOT NULL,
  `Current` int(11) NOT NULL DEFAULT 0,
  `StartDate` datetime DEFAULT NULL,
  `EndDate` datetime DEFAULT NULL,
  `TournamentResults` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `seasons`
--

CREATE TABLE `seasons` (
  `id` int(11) NOT NULL,
  `Season` int(11) NOT NULL,
  `BotId` int(11) NOT NULL,
  `Author` varchar(50) NOT NULL,
  `Matches` int(11) NOT NULL,
  `Wins` int(11) NOT NULL,
  `WinPct` float NOT NULL,
  `Position` int(11) NOT NULL,
  `ELO` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `tournamententrys`
--

CREATE TABLE `tournamententrys` (
  `ID` int(11) NOT NULL,
  `Name` varchar(127) NOT NULL,
  `Location` varchar(1024) NOT NULL,
  `UserId` varchar(64) NOT NULL,
  `UploadTime` timestamp NOT NULL DEFAULT current_timestamp(),
  `Verified` tinyint(4) NOT NULL DEFAULT 0,
  `Deleted` tinyint(4) NOT NULL DEFAULT 0
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `botrequests`
--
ALTER TABLE `botrequests`
  ADD UNIQUE KEY `id` (`id`),
  ADD KEY `id_2` (`id`);

--
-- Indexes for table `loginattempts`
--
ALTER TABLE `loginattempts`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `maps`
--
ALTER TABLE `maps`
  ADD PRIMARY KEY (`MapId`);

--
-- Indexes for table `matches`
--
ALTER TABLE `matches`
  ADD PRIMARY KEY (`MatchId`);

--
-- Indexes for table `members`
--
ALTER TABLE `members`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username_UNIQUE` (`username`),
  ADD UNIQUE KEY `id_UNIQUE` (`id`),
  ADD UNIQUE KEY `email_UNIQUE` (`email`);

--
-- Indexes for table `participants`
--
ALTER TABLE `participants`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `results`
--
ALTER TABLE `results`
  ADD PRIMARY KEY (`GameID`);

--
-- Indexes for table `seasonids`
--
ALTER TABLE `seasonids`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `seasons`
--
ALTER TABLE `seasons`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tournamententrys`
--
ALTER TABLE `tournamententrys`
  ADD PRIMARY KEY (`ID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `loginattempts`
--
ALTER TABLE `loginattempts`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `maps`
--
ALTER TABLE `maps`
  MODIFY `MapId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `matches`
--
ALTER TABLE `matches`
  MODIFY `MatchId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `participants`
--
ALTER TABLE `participants`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `results`
--
ALTER TABLE `results`
  MODIFY `GameID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `seasonids`
--
ALTER TABLE `seasonids`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `seasons`
--
ALTER TABLE `seasons`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tournamententrys`
--
ALTER TABLE `tournamententrys`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
