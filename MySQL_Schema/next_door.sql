-- phpMyAdmin SQL Dump
-- version 4.9.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Dec 18, 2019 at 08:16 PM
-- Server version: 10.4.8-MariaDB
-- PHP Version: 7.3.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `next_door`
--

-- --------------------------------------------------------

--
-- Table structure for table `Application`
--

CREATE TABLE `Application` (
  `appid` int(11) NOT NULL,
  `applicant` varchar(100) NOT NULL,
  `blockid` int(11) NOT NULL,
  `member_count` int(11) NOT NULL,
  `respond_count` int(11) NOT NULL DEFAULT 0,
  `approve_count` int(11) NOT NULL DEFAULT 0,
  `app_status` enum('pending','approved','denied') NOT NULL DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `Application`
--

INSERT INTO `Application` (`appid`, `applicant`, `blockid`, `member_count`, `respond_count`, `approve_count`, `app_status`) VALUES
(16, 'bc@uci.edu', 1, 3, 1, 1, 'pending');

--
-- Triggers `Application`
--
DELIMITER $$
CREATE TRIGGER `send_request` AFTER INSERT ON `Application` FOR EACH ROW begin
	declare i int;
    declare exist_member varchar(100);
    set i = 0;
	if new.member_count <= 5 then
		while i < new.member_count do
			select email into exist_member from Members where mblock = new.blockid order by since asc limit 1 offset i;
            insert into Join_Request(requester, requestee, appid) values (new.applicant, exist_member, new.appid);
			set i = i + 1;
		end while;
	else 
		while i < 5 do
			select email into exist_member from Members where mblock = new.blockid order by since asc limit 1 offset i;
            insert into Join_Request(requester, requestee, appid) values (new.applicant, exist_member, new.appid);
			set i = i + 1; 
        end while;
    end if;
end
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `update_mblock` BEFORE UPDATE ON `Application` FOR EACH ROW begin
if old.app_status = "pending" then
	if new.member_count < 5 then
		if new.member_count <= 3 then
			if new.approve_count = new.member_count then
				set new.app_status = "approved";
				#update Application set app_status = "approved" where appid = new.appid;
                update Members set mblock = new.blockid where email = new.applicant;
			elseif new.respond_count = new.member_count and new.approve_count < new.member_count then
				set new.app_status = "denied";
				#update Application set app_status = "denied" where appid = new.appid;
			end if;
        else 
			if new.approve_count >= 3 then
				set new.app_status = "approved";
				#update Application set app_status = "approved" where appid = new.appid;
                update Members set mblock = new.blockid where email = new.applicant;
			elseif (new.respond_count-new.approve_count) > 1 then 
				set new.app_status = "denied";
				#update Application set app_status = "denied" where appid = new.appid;
			end if;
        end if;
	else
		if new.approve_count >= 3 then
			set new.app_status = "approved";
			#update Application set app_status = "approved" where appid = new.appid;
			update Members set mblock = new.blockid where email = new.applicant;
		elseif (new.respond_count-new.approve_count) > 2 then
			set new.app_status = "denied";
			#update Application set app_status = "denied" where appid = new.appid;
		end if;
    end if;
end if;
end
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `Blocks`
--

CREATE TABLE `Blocks` (
  `bid` int(11) NOT NULL,
  `bhood` int(11) NOT NULL,
  `bname` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `Blocks`
--

INSERT INTO `Blocks` (`bid`, `bhood`, `bname`) VALUES
(1, 1, '3rd st from 5th to 7th'),
(2, 1, '2nd st'),
(3, 1, 'Union st from 4th to 8th'),
(4, 2, 'Plymouth st'),
(5, 2, 'Water st'),
(6, 2, 'Front st'),
(7, 3, 'E 17th st from Beverley rd to Cortelyou rd'),
(8, 3, 'Argyle rd'),
(9, 3, 'Westminister rd'),
(10, 4, 'San Leon Villa'),
(11, 4, 'San Remo Apartments'),
(12, 4, 'Santa Rosa Apartments'),
(13, 5, 'Marsh Hawk'),
(14, 5, 'Silver Fir'),
(15, 5, 'Seadrift'),
(16, 6, 'Spike Moss'),
(17, 6, 'Black Falcon'),
(18, 6, 'Echo Glen'),
(19, 7, 'W 141st st '),
(20, 7, 'W 131st st from Powell blvd to Lenox Ave'),
(21, 7, 'Odell Clark Pl'),
(22, 8, 'Spring st'),
(23, 8, 'Broome st'),
(24, 8, 'Prince st'),
(25, 9, 'Barrow st to Hudson st'),
(26, 9, 'Morton st to Hudson st'),
(27, 9, 'Clarkson st to Hudson st');

-- --------------------------------------------------------

--
-- Table structure for table `Friends`
--

CREATE TABLE `Friends` (
  `friend_1` varchar(100) NOT NULL,
  `friend_2` varchar(100) NOT NULL,
  `since` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `Friends`
--

INSERT INTO `Friends` (`friend_1`, `friend_2`, `since`) VALUES
('bc@uci.edu', 'binghal@uci.edu', '2019-12-18 18:57:53'),
('nw@uci.edu', 'binghal@uci.edu', '2019-12-09 05:14:53'),
('nw@uci.edu', 'mw@uci.edu', '2019-12-10 02:02:20');

-- --------------------------------------------------------

--
-- Table structure for table `Friend_Request`
--

CREATE TABLE `Friend_Request` (
  `requester` varchar(100) NOT NULL,
  `requestee` varchar(100) NOT NULL,
  `request_time` timestamp NOT NULL DEFAULT current_timestamp(),
  `request_status` enum('pending','approved','denied') NOT NULL DEFAULT 'pending',
  `responed_time` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `Friend_Request`
--

INSERT INTO `Friend_Request` (`requester`, `requestee`, `request_time`, `request_status`, `responed_time`) VALUES
('bc@uci.edu', 'binghal@uci.edu', '2019-12-18 17:25:23', 'approved', '2019-12-18 18:57:53'),
('bc@uci.edu', 'nw@uci.edu', '2019-12-12 02:50:03', 'pending', NULL),
('binghal@uci.edu', 'bc@uci.edu', '2019-12-18 06:52:35', 'pending', NULL),
('binghal@uci.edu', 'mw@uci.edu', '2019-12-18 19:15:02', 'pending', NULL),
('nw@uci.edu', 'binghal@uci.edu', '2019-12-09 05:14:53', 'approved', NULL),
('nw@uci.edu', 'mw@uci.edu', '2019-12-10 01:26:03', 'approved', '2019-12-10 02:02:20');

--
-- Triggers `Friend_Request`
--
DELIMITER $$
CREATE TRIGGER `insert_to_friends` AFTER UPDATE ON `Friend_Request` FOR EACH ROW begin
	if new.request_status = "approved" then
		insert into Friends (friend_1, friend_2) value
        (old.requester, old.requestee);
	end if;
end
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `Hoods`
--

CREATE TABLE `Hoods` (
  `hid` int(11) NOT NULL,
  `hname` varchar(50) NOT NULL,
  `hcity` varchar(50) NOT NULL,
  `hstate` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `Hoods`
--

INSERT INTO `Hoods` (`hid`, `hname`, `hcity`, `hstate`) VALUES
(1, 'Park Slope', 'Brooklyn', 'New York'),
(2, 'Dumbo', 'Brooklyn', 'New York'),
(3, 'Flatbush', 'Brooklyn', 'New York'),
(4, 'Westpark', 'Irvine', 'Calfifornia'),
(5, 'Woodbridge', 'Irvine', 'California'),
(6, 'Shady Canyon', 'Irvine', 'California'),
(7, 'Harlem', 'New York', 'New York'),
(8, 'SoHo', 'New York', 'New York'),
(9, 'Greenwich Village', 'New York', 'New York');

-- --------------------------------------------------------

--
-- Table structure for table `Join_Request`
--

CREATE TABLE `Join_Request` (
  `requester` varchar(100) NOT NULL,
  `requestee` varchar(100) NOT NULL,
  `request_time` timestamp NOT NULL DEFAULT current_timestamp(),
  `request_status` enum('pending','approved','denied') NOT NULL DEFAULT 'pending',
  `responed_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `appid` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `Join_Request`
--

INSERT INTO `Join_Request` (`requester`, `requestee`, `request_time`, `request_status`, `responed_time`, `appid`) VALUES
('bc@uci.edu', 'binghal@uci.edu', '2019-12-18 18:26:43', 'approved', '2019-12-18 18:59:57', 16),
('bc@uci.edu', 'mw@uci.edu', '2019-12-18 18:26:43', 'pending', '0000-00-00 00:00:00', 16),
('bc@uci.edu', 'nw@uci.edu', '2019-12-18 18:26:43', 'pending', '0000-00-00 00:00:00', 16);

--
-- Triggers `Join_Request`
--
DELIMITER $$
CREATE TRIGGER `update_application` AFTER UPDATE ON `Join_Request` FOR EACH ROW begin
	declare old_r_count int;
    declare old_a_count int;
    select respond_count into old_r_count from Application where appid=new.appid;
    select approve_count into old_a_count from Application where appid=new.appid;
	if (new.request_status = 'approved') then 
		update Application set respond_count = 1+old_r_count, approve_count = 1+old_a_count where appid = new.appid;
	else
		update Application set respond_count = 1+old_r_count where appid = new.appid;
    end if;
end
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `Login_Info`
--

CREATE TABLE `Login_Info` (
  `memail` varchar(100) NOT NULL,
  `login_time` timestamp NOT NULL DEFAULT current_timestamp(),
  `logout_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `Login_Info`
--

INSERT INTO `Login_Info` (`memail`, `login_time`, `logout_time`) VALUES
('bc@uci.edu', '2019-12-10 04:25:04', '2019-12-10 21:10:39'),
('bc@uci.edu', '2019-12-10 17:18:27', '2019-12-10 21:10:39'),
('bc@uci.edu', '2019-12-10 17:20:08', '2019-12-10 21:10:39'),
('bc@uci.edu', '2019-12-10 21:11:01', '2019-12-10 21:20:40'),
('bc@uci.edu', '2019-12-10 21:48:19', '2019-12-10 22:07:20'),
('bc@uci.edu', '2019-12-12 04:56:38', '2019-12-12 06:01:33'),
('bc@uci.edu', '2019-12-12 06:01:56', '2019-12-12 06:02:00'),
('bc@uci.edu', '2019-12-12 06:05:41', '2019-12-12 06:11:19'),
('bc@uci.edu', '2019-12-18 17:25:14', '2019-12-18 17:25:44'),
('bc@uci.edu', '2019-12-18 18:23:37', '2019-12-18 18:28:40'),
('binghal@uci.edu', '2019-12-09 03:58:15', '2019-12-09 04:26:08'),
('binghal@uci.edu', '2019-12-09 04:39:53', '2019-12-09 06:22:21'),
('binghal@uci.edu', '2019-12-09 04:40:21', '2019-12-09 06:22:21'),
('binghal@uci.edu', '2019-12-09 06:25:33', '2019-12-09 06:26:30'),
('binghal@uci.edu', '2019-12-09 06:26:35', '2019-12-09 06:26:47'),
('binghal@uci.edu', '2019-12-09 18:01:13', '2019-12-09 18:09:30'),
('binghal@uci.edu', '2019-12-09 18:01:29', '2019-12-09 18:09:30'),
('binghal@uci.edu', '2019-12-09 18:09:39', '2019-12-09 18:13:40'),
('binghal@uci.edu', '2019-12-09 18:13:49', '2019-12-09 18:15:25'),
('binghal@uci.edu', '2019-12-09 18:15:42', '2019-12-09 18:44:12'),
('binghal@uci.edu', '2019-12-09 19:08:38', '2019-12-09 19:10:49'),
('binghal@uci.edu', '2019-12-09 22:26:35', '2019-12-09 22:27:09'),
('binghal@uci.edu', '2019-12-10 04:23:24', '2019-12-10 04:23:30'),
('binghal@uci.edu', '2019-12-10 21:20:46', '2019-12-10 21:29:02'),
('binghal@uci.edu', '2019-12-11 08:15:21', '2019-12-11 08:15:27'),
('binghal@uci.edu', '2019-12-11 08:23:30', '2019-12-11 08:25:00'),
('binghal@uci.edu', '2019-12-12 04:12:02', '2019-12-12 04:56:25'),
('binghal@uci.edu', '2019-12-12 06:02:44', '2019-12-12 06:05:33'),
('binghal@uci.edu', '2019-12-18 06:23:04', '2019-12-18 07:36:03'),
('binghal@uci.edu', '2019-12-18 17:22:10', '2019-12-18 17:25:08'),
('binghal@uci.edu', '2019-12-18 17:25:48', '2019-12-18 17:28:06'),
('binghal@uci.edu', '2019-12-18 18:23:20', '2019-12-18 18:23:32'),
('binghal@uci.edu', '2019-12-18 18:28:49', '2019-12-18 18:46:20'),
('binghal@uci.edu', '2019-12-18 18:54:42', '0000-00-00 00:00:00'),
('binghal@uci.edu', '2019-12-18 19:14:04', '0000-00-00 00:00:00'),
('er@nyu.edu', '2019-12-18 18:53:17', '2019-12-18 18:54:36'),
('mw@uci.edu', '2019-12-10 01:27:04', '2019-12-10 02:02:30'),
('mw@uci.edu', '2019-12-10 02:05:38', '2019-12-10 04:23:18'),
('mw@uci.edu', '2019-12-10 04:07:19', '2019-12-10 04:23:18'),
('mw@uci.edu', '2019-12-11 08:15:58', '2019-12-11 08:16:03'),
('mw@uci.edu', '2019-12-12 06:02:09', '2019-12-12 06:02:16'),
('mw@uci.edu', '2019-12-18 17:28:18', '2019-12-18 17:42:10'),
('nw@uci.edu', '2019-12-09 01:41:53', '2019-12-09 01:49:37'),
('nw@uci.edu', '2019-12-09 02:00:21', '2019-12-09 02:00:30'),
('nw@uci.edu', '2019-12-09 02:00:44', '2019-12-09 03:57:23'),
('nw@uci.edu', '2019-12-09 18:44:18', '2019-12-09 19:08:28'),
('nw@uci.edu', '2019-12-09 19:10:54', '2019-12-09 22:26:14'),
('nw@uci.edu', '2019-12-09 22:26:20', '2019-12-09 22:26:29'),
('nw@uci.edu', '2019-12-09 22:27:17', '2019-12-10 01:26:57'),
('nw@uci.edu', '2019-12-10 02:02:37', '2019-12-10 02:05:22'),
('nw@uci.edu', '2019-12-10 21:29:07', '2019-12-10 21:48:05'),
('nw@uci.edu', '2019-12-10 22:07:33', '2019-12-11 02:30:16'),
('nw@uci.edu', '2019-12-11 02:32:01', '2019-12-11 02:33:56'),
('nw@uci.edu', '2019-12-11 02:34:22', '2019-12-11 08:15:12'),
('nw@uci.edu', '2019-12-11 08:16:21', '2019-12-11 08:23:19'),
('nw@uci.edu', '2019-12-12 02:31:28', '2019-12-12 04:11:56'),
('nw@uci.edu', '2019-12-12 02:32:12', '2019-12-12 04:11:56'),
('nw@uci.edu', '2019-12-12 06:02:27', '2019-12-12 06:02:32'),
('nw@uci.edu', '2019-12-18 05:25:27', '2019-12-18 06:10:58');

-- --------------------------------------------------------

--
-- Table structure for table `Members`
--

CREATE TABLE `Members` (
  `memid` int(11) NOT NULL,
  `firstname` varchar(50) NOT NULL,
  `lastname` varchar(50) NOT NULL,
  `phone` varchar(10) NOT NULL,
  `email` varchar(100) NOT NULL,
  `passwords` varchar(100) NOT NULL,
  `descriptions` varchar(1000) DEFAULT NULL,
  `street` varchar(200) NOT NULL,
  `city` varchar(50) NOT NULL,
  `state` varchar(50) NOT NULL,
  `since` timestamp NOT NULL DEFAULT current_timestamp(),
  `profile_pic` varchar(100) NOT NULL DEFAULT 'img/DefaultProfile.png',
  `mblock` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `Members`
--

INSERT INTO `Members` (`memid`, `firstname`, `lastname`, `phone`, `email`, `passwords`, `descriptions`, `street`, `city`, `state`, `since`, `profile_pic`, `mblock`) VALUES
(1, 'Nick', 'Wang', '9491234567', 'nw@uci.edu', '$2y$10$dTkWCi2vAI9Ov3s/cQss.edzdSC3hBOjTKhR3p.RGVLZj1HydwWHC', 'My name is Nick, I have a dog.', '27682 Daisyfield Dr', 'Laguna Niguel', 'California', '2019-12-08 22:40:45', 'img/NickWang.jpg', 1),
(2, 'Mitchell', 'Wong', '9492026789', 'mw@uci.edu', '$2y$10$q0PD5ckP8z3t5n6GFy37ResSks1ssm5jnhHJdCpvtdDypsGOT/35e', 'Just moved to Brooklyn. ', '453 3rd st', 'Brooklyn', 'New York', '2019-12-09 00:58:51', 'img/MitchellWong.jpg', 1),
(3, 'Oliver', 'Li', '4992026942', 'binghal@uci.edu', '$2y$10$XGTZya/0/.ECNXGry/gRy.UqFJmfIvo8l0kk9n9o0A0irLtcl5YZa', NULL, '524 3rd st', 'Brooklyn', 'New York', '2019-12-09 03:58:03', 'img/Oliver.jpeg', 1),
(4, 'Ben', 'Chen', '9493446789', 'bc@uci.edu', '$2y$10$ImKqene4q0hLTrvitxYp5.Irn1SvoOhI3xEKz/iAUN/rQ1NV7jeqG', NULL, '245 3rd st', 'Brooklyn', 'New York', '2019-12-10 04:24:56', 'img/DefaultProfile.png', NULL),
(5, 'Eric', 'Chen', '1234567890', 'er@nyu.edu', '$2y$10$kZiDmqEwaNWOnbgq2K.3hu89G34taBThZn1k5RvopN2onjovs8aLO', NULL, '5 Metrotech Center', 'Brooklyn', 'New York', '2019-12-18 18:52:39', 'img/DefaultProfile.png', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `Messages`
--

CREATE TABLE `Messages` (
  `mesid` int(11) NOT NULL,
  `poster` varchar(100) NOT NULL,
  `post_time` timestamp NOT NULL DEFAULT current_timestamp(),
  `audiences` enum('person','block','hood','friends','neighbors') NOT NULL,
  `audience_id` int(11) DEFAULT NULL,
  `subjects` varchar(50) NOT NULL,
  `body` varchar(2000) NOT NULL,
  `longitude` double DEFAULT NULL,
  `latitude` double DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `Messages`
--

INSERT INTO `Messages` (`mesid`, `poster`, `post_time`, `audiences`, `audience_id`, `subjects`, `body`, `longitude`, `latitude`) VALUES
(1, 'nw@uci.edu', '2019-12-10 02:54:32', 'block', 1, 'Missing bike', 'I losted my bike.', NULL, NULL),
(2, 'mw@uci.edu', '2019-12-10 03:00:19', 'hood', 1, 'Missing car', 'I losted my car.', 116.363625, 39.913818),
(3, 'binghal@uci.edu', '2019-12-10 03:05:46', 'neighbors', NULL, 'Missing son', 'I losted my son.', 116.363625, 39.913818),
(4, 'mw@uci.edu', '2019-12-10 03:06:57', 'person', 1, 'Missing kid', 'I losted my kid.', 116.363625, 39.913818),
(5, 'nw@uci.edu', '2019-12-10 03:35:57', 'friends', NULL, 'Party Time', 'Having a party tonight.', 116.363625, 39.913818),
(6, 'nw@uci.edu', '2019-12-10 03:43:15', 'neighbors', NULL, 'Selling home', 'I am selling my house.', 116.363625, 39.913818),
(11, 'nw@uci.edu', '2019-12-11 08:12:49', 'friends', NULL, 'Water outage', 'Hello everyone just saw the news there is going to be a water outage', -73.9835502, 40.6716112),
(12, 'nw@uci.edu', '2019-12-11 08:14:02', 'neighbors', NULL, 'test', 'this is a test', NULL, NULL),
(13, 'nw@uci.edu', '2019-12-11 08:14:20', 'block', 1, 'test2', 'this si test2', NULL, NULL),
(14, 'nw@uci.edu', '2019-12-11 08:14:34', 'hood', 1, 'test3', 'gotta make sure all works', NULL, NULL),
(15, 'nw@uci.edu', '2019-12-11 08:15:02', 'person', 3, 'hopefully all worls', 'works ', NULL, NULL),
(16, 'mw@uci.edu', '2019-12-18 17:30:42', 'neighbors', NULL, 'Just Moved To Brooklyn', 'Hello, all. My name is Mitchell, I just moved to Brooklyn, looking for some local restaurant suggestion. Anyone is welcome to reply.  ', NULL, NULL),
(17, 'mw@uci.edu', '2019-12-18 17:34:32', 'neighbors', NULL, 'Discovered a great breakfast place', 'There is a breakfast place on 7th ave called La Bagel Delight, they have the best breakfast bagel!', -73.980086818, 40.6683854),
(18, 'bc@uci.edu', '2019-12-18 18:25:57', 'hood', 1, 'Hello all my name is Ben', 'Hello neighbors, my name is Ben, I just moved from Southern California to Brooklyn. ', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `Msg_Reply`
--

CREATE TABLE `Msg_Reply` (
  `mrid` int(11) NOT NULL,
  `replier` varchar(100) NOT NULL,
  `reply_time` timestamp NOT NULL DEFAULT current_timestamp(),
  `body` varchar(200) NOT NULL,
  `reply_to` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `Msg_Reply`
--

INSERT INTO `Msg_Reply` (`mrid`, `replier`, `reply_time`, `body`, `reply_to`) VALUES
(1, 'binghal@uci.edu', '2019-12-11 04:48:17', 'Interested', 6),
(4, 'nw@uci.edu', '2019-12-11 06:04:03', 'I found him', 4),
(5, 'nw@uci.edu', '2019-12-11 08:13:20', 'Thank you for the heads up Nick', 11),
(6, 'binghal@uci.edu', '2019-12-18 07:12:31', 'Thanks for the heads up\r\n', 11),
(7, 'binghal@uci.edu', '2019-12-18 07:22:20', 'Thanks again', 11);

-- --------------------------------------------------------

--
-- Table structure for table `Neighbor`
--

CREATE TABLE `Neighbor` (
  `memail` varchar(100) NOT NULL,
  `neighbor` varchar(100) NOT NULL,
  `since` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `Neighbor`
--

INSERT INTO `Neighbor` (`memail`, `neighbor`, `since`) VALUES
('binghal@uci.edu', 'nw@uci.edu', '2019-12-18 17:25:57'),
('mw@uci.edu', 'binghal@uci.edu', '2019-12-10 02:11:01'),
('mw@uci.edu', 'nw@uci.edu', '2019-12-10 02:10:59'),
('nw@uci.edu', 'binghal@uci.edu', '2019-12-10 01:26:25'),
('nw@uci.edu', 'mw@uci.edu', '2019-12-09 21:19:36');

-- --------------------------------------------------------

--
-- Table structure for table `Read_Messages`
--

CREATE TABLE `Read_Messages` (
  `memail` varchar(100) NOT NULL,
  `mesid` int(11) NOT NULL,
  `read_time` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `Read_Messages`
--

INSERT INTO `Read_Messages` (`memail`, `mesid`, `read_time`) VALUES
('binghal@uci.edu', 1, '2019-12-12 04:21:22'),
('binghal@uci.edu', 1, '2019-12-12 04:28:12'),
('binghal@uci.edu', 1, '2019-12-18 07:31:20'),
('binghal@uci.edu', 1, '2019-12-18 18:56:23'),
('binghal@uci.edu', 3, '2019-12-12 04:13:23'),
('binghal@uci.edu', 3, '2019-12-12 04:21:08'),
('binghal@uci.edu', 3, '2019-12-12 04:26:52'),
('binghal@uci.edu', 11, '2019-12-12 04:13:08'),
('binghal@uci.edu', 11, '2019-12-12 04:20:12'),
('binghal@uci.edu', 11, '2019-12-12 04:20:51'),
('binghal@uci.edu', 11, '2019-12-18 07:09:18'),
('binghal@uci.edu', 11, '2019-12-18 07:11:58'),
('binghal@uci.edu', 11, '2019-12-18 07:12:12'),
('binghal@uci.edu', 11, '2019-12-18 07:12:19'),
('binghal@uci.edu', 11, '2019-12-18 07:12:31'),
('binghal@uci.edu', 11, '2019-12-18 07:18:29'),
('binghal@uci.edu', 11, '2019-12-18 07:20:17'),
('binghal@uci.edu', 11, '2019-12-18 07:22:03'),
('binghal@uci.edu', 11, '2019-12-18 07:22:04'),
('binghal@uci.edu', 11, '2019-12-18 07:22:11'),
('binghal@uci.edu', 11, '2019-12-18 07:22:20'),
('binghal@uci.edu', 11, '2019-12-18 07:22:26'),
('binghal@uci.edu', 11, '2019-12-18 07:22:49'),
('binghal@uci.edu', 11, '2019-12-18 17:27:19'),
('binghal@uci.edu', 14, '2019-12-12 04:13:28'),
('binghal@uci.edu', 14, '2019-12-18 17:24:35'),
('binghal@uci.edu', 15, '2019-12-12 04:12:28'),
('binghal@uci.edu', 15, '2019-12-12 04:13:48'),
('binghal@uci.edu', 15, '2019-12-12 04:26:57'),
('binghal@uci.edu', 15, '2019-12-12 04:27:11'),
('binghal@uci.edu', 15, '2019-12-12 04:28:03'),
('binghal@uci.edu', 15, '2019-12-18 07:09:13'),
('binghal@uci.edu', 15, '2019-12-18 07:12:16'),
('binghal@uci.edu', 15, '2019-12-18 07:18:33'),
('binghal@uci.edu', 17, '2019-12-18 18:54:59'),
('binghal@uci.edu', 17, '2019-12-18 18:56:17'),
('binghal@uci.edu', 18, '2019-12-18 18:54:50'),
('mw@uci.edu', 11, '2019-12-18 17:30:49'),
('mw@uci.edu', 17, '2019-12-18 17:34:35'),
('nw@uci.edu', 15, '2019-12-12 03:05:36'),
('nw@uci.edu', 15, '2019-12-12 03:05:38'),
('nw@uci.edu', 15, '2019-12-12 03:05:59');

-- --------------------------------------------------------

--
-- Table structure for table `Reply_Reply`
--

CREATE TABLE `Reply_Reply` (
  `rrid` int(11) NOT NULL,
  `replier` varchar(100) NOT NULL,
  `reply_time` timestamp NOT NULL DEFAULT current_timestamp(),
  `body` varchar(200) NOT NULL,
  `reply_to` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `Reply_Reply`
--

INSERT INTO `Reply_Reply` (`rrid`, `replier`, `reply_time`, `body`, `reply_to`) VALUES
(1, 'mw@uci.edu', '2019-12-11 04:49:27', 'You got no money dude', 1),
(2, 'nw@uci.edu', '2019-12-11 06:04:45', 'or her ', 4);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `Application`
--
ALTER TABLE `Application`
  ADD PRIMARY KEY (`appid`,`applicant`,`blockid`),
  ADD KEY `applicant` (`applicant`),
  ADD KEY `blockid` (`blockid`);

--
-- Indexes for table `Blocks`
--
ALTER TABLE `Blocks`
  ADD PRIMARY KEY (`bid`,`bhood`),
  ADD KEY `bhood` (`bhood`);

--
-- Indexes for table `Friends`
--
ALTER TABLE `Friends`
  ADD PRIMARY KEY (`friend_1`,`friend_2`),
  ADD KEY `friend_2` (`friend_2`);

--
-- Indexes for table `Friend_Request`
--
ALTER TABLE `Friend_Request`
  ADD PRIMARY KEY (`requester`,`requestee`),
  ADD KEY `requestee` (`requestee`);

--
-- Indexes for table `Hoods`
--
ALTER TABLE `Hoods`
  ADD PRIMARY KEY (`hid`);

--
-- Indexes for table `Join_Request`
--
ALTER TABLE `Join_Request`
  ADD PRIMARY KEY (`requester`,`requestee`,`appid`),
  ADD KEY `requestee` (`requestee`),
  ADD KEY `appid` (`appid`);

--
-- Indexes for table `Login_Info`
--
ALTER TABLE `Login_Info`
  ADD PRIMARY KEY (`memail`,`login_time`);

--
-- Indexes for table `Members`
--
ALTER TABLE `Members`
  ADD PRIMARY KEY (`memid`,`email`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `mblock` (`mblock`);

--
-- Indexes for table `Messages`
--
ALTER TABLE `Messages`
  ADD PRIMARY KEY (`mesid`),
  ADD KEY `poster` (`poster`);

--
-- Indexes for table `Msg_Reply`
--
ALTER TABLE `Msg_Reply`
  ADD PRIMARY KEY (`mrid`),
  ADD KEY `reply_to` (`reply_to`),
  ADD KEY `replier` (`replier`);

--
-- Indexes for table `Neighbor`
--
ALTER TABLE `Neighbor`
  ADD PRIMARY KEY (`memail`,`neighbor`),
  ADD KEY `neighbor` (`neighbor`);

--
-- Indexes for table `Read_Messages`
--
ALTER TABLE `Read_Messages`
  ADD PRIMARY KEY (`memail`,`mesid`,`read_time`),
  ADD KEY `mesid` (`mesid`);

--
-- Indexes for table `Reply_Reply`
--
ALTER TABLE `Reply_Reply`
  ADD PRIMARY KEY (`rrid`),
  ADD KEY `reply_to` (`reply_to`),
  ADD KEY `replier` (`replier`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `Application`
--
ALTER TABLE `Application`
  MODIFY `appid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `Blocks`
--
ALTER TABLE `Blocks`
  MODIFY `bid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `Hoods`
--
ALTER TABLE `Hoods`
  MODIFY `hid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `Members`
--
ALTER TABLE `Members`
  MODIFY `memid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `Messages`
--
ALTER TABLE `Messages`
  MODIFY `mesid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `Msg_Reply`
--
ALTER TABLE `Msg_Reply`
  MODIFY `mrid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `Reply_Reply`
--
ALTER TABLE `Reply_Reply`
  MODIFY `rrid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `Application`
--
ALTER TABLE `Application`
  ADD CONSTRAINT `Application_ibfk_1` FOREIGN KEY (`applicant`) REFERENCES `Members` (`email`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `Application_ibfk_2` FOREIGN KEY (`blockid`) REFERENCES `Blocks` (`bid`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `Blocks`
--
ALTER TABLE `Blocks`
  ADD CONSTRAINT `Blocks_ibfk_1` FOREIGN KEY (`bhood`) REFERENCES `Hoods` (`hid`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `Friends`
--
ALTER TABLE `Friends`
  ADD CONSTRAINT `Friends_ibfk_1` FOREIGN KEY (`friend_1`) REFERENCES `Members` (`email`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `Friends_ibfk_2` FOREIGN KEY (`friend_2`) REFERENCES `Members` (`email`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `Friend_Request`
--
ALTER TABLE `Friend_Request`
  ADD CONSTRAINT `Friend_Request_ibfk_1` FOREIGN KEY (`requester`) REFERENCES `Members` (`email`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `Friend_Request_ibfk_2` FOREIGN KEY (`requestee`) REFERENCES `Members` (`email`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `Join_Request`
--
ALTER TABLE `Join_Request`
  ADD CONSTRAINT `Join_Request_ibfk_1` FOREIGN KEY (`requester`) REFERENCES `Members` (`email`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `Join_Request_ibfk_2` FOREIGN KEY (`requestee`) REFERENCES `Members` (`email`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `Join_Request_ibfk_3` FOREIGN KEY (`appid`) REFERENCES `Application` (`appid`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `Login_Info`
--
ALTER TABLE `Login_Info`
  ADD CONSTRAINT `Login_Info_ibfk_1` FOREIGN KEY (`memail`) REFERENCES `Members` (`email`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `Members`
--
ALTER TABLE `Members`
  ADD CONSTRAINT `Members_ibfk_1` FOREIGN KEY (`mblock`) REFERENCES `Blocks` (`bid`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `Messages`
--
ALTER TABLE `Messages`
  ADD CONSTRAINT `Messages_ibfk_1` FOREIGN KEY (`poster`) REFERENCES `Members` (`email`) ON UPDATE CASCADE;

--
-- Constraints for table `Msg_Reply`
--
ALTER TABLE `Msg_Reply`
  ADD CONSTRAINT `Msg_Reply_ibfk_1` FOREIGN KEY (`reply_to`) REFERENCES `Messages` (`mesid`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `Msg_Reply_ibfk_2` FOREIGN KEY (`replier`) REFERENCES `Members` (`email`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `Neighbor`
--
ALTER TABLE `Neighbor`
  ADD CONSTRAINT `Neighbor_ibfk_1` FOREIGN KEY (`memail`) REFERENCES `Members` (`email`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `Neighbor_ibfk_2` FOREIGN KEY (`neighbor`) REFERENCES `Members` (`email`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `Read_Messages`
--
ALTER TABLE `Read_Messages`
  ADD CONSTRAINT `Read_Messages_ibfk_1` FOREIGN KEY (`memail`) REFERENCES `Members` (`email`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `Read_Messages_ibfk_2` FOREIGN KEY (`mesid`) REFERENCES `Messages` (`mesid`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `Reply_Reply`
--
ALTER TABLE `Reply_Reply`
  ADD CONSTRAINT `Reply_Reply_ibfk_1` FOREIGN KEY (`reply_to`) REFERENCES `Msg_Reply` (`mrid`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `Reply_Reply_ibfk_2` FOREIGN KEY (`replier`) REFERENCES `Members` (`email`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
